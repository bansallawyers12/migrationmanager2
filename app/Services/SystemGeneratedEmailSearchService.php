<?php

namespace App\Services;

use App\Models\EmailLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemGeneratedEmailSearchService
{
    public function __construct(private SystemEmailLogService $logService)
    {
    }

    /**
     * Base query scoped to system-generated outgoing emails only.
     */
    public function baseQuery(): Builder
    {
        return EmailLog::query()
            ->where('mail_type', 1)
            ->where('conversion_type', SystemEmailLogService::CONVERSION_TYPE);
    }

    public function applyFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('search')) {
            $pattern = '%' . mb_strtolower(trim($request->input('search')), 'UTF-8') . '%';
            $query->where(function (Builder $q) use ($pattern) {
                $q->whereRaw('LOWER(subject) LIKE ?', [$pattern])
                  ->orWhereRaw('LOWER(from_mail) LIKE ?', [$pattern])
                  ->orWhereRaw('LOWER(to_mail) LIKE ?', [$pattern]);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('category')) {
            $query->where('system_email_category', $request->input('category'));
        }

        if ($request->filled('from_address')) {
            $query->whereRaw('LOWER(from_mail) LIKE ?', [
                '%' . mb_strtolower(trim($request->input('from_address')), 'UTF-8') . '%',
            ]);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', (int) $request->input('client_id'));
        }

        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->input('delivery_status'));
        }

        if ($request->boolean('failed')) {
            $query->whereIn('delivery_status', ['bounced', 'dropped', 'send_failed', 'blocked']);
        }

        if ($request->filled('has_attachments')) {
            if ($request->input('has_attachments') === '1') {
                $query->has('attachments');
            } elseif ($request->input('has_attachments') === '0') {
                $query->doesntHave('attachments');
            }
        }

        return $query;
    }

    public function getDashboardStats(): array
    {
        $base = $this->baseQuery();

        $today      = now()->startOfDay();
        $weekStart  = now()->startOfWeek();
        $monthStart = now()->startOfMonth();

        $totalToday = (clone $base)->where('created_at', '>=', $today)->count();
        $totalWeek  = (clone $base)->where('created_at', '>=', $weekStart)->count();
        $totalMonth = (clone $base)->where('created_at', '>=', $monthStart)->count();

        $withAttachments = (clone $base)->has('attachments')->count();

        $failedCount = (clone $base)
            ->whereIn('delivery_status', ['bounced', 'dropped', 'send_failed', 'blocked'])
            ->where('created_at', '>=', $monthStart)
            ->count();

        $topCategories = (clone $base)
            ->where('created_at', '>=', $monthStart)
            ->whereNotNull('system_email_category')
            ->select('system_email_category', DB::raw('COUNT(*) as send_count'))
            ->groupBy('system_email_category')
            ->orderByDesc('send_count')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'category'       => $row->system_email_category,
                'category_label' => $this->logService->categoryLabel($row->system_email_category),
                'send_count'     => $row->send_count,
            ]);

        return compact('totalToday', 'totalWeek', 'totalMonth', 'withAttachments', 'failedCount', 'topCategories');
    }

    public function resolveS3PreviewUrl(EmailLog $email): ?string
    {
        return app(OutgoingEmailSearchService::class)->resolveS3PreviewUrl($email);
    }
}
