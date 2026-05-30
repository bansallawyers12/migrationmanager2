<?php

namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\EmailLog;
use App\Services\SystemEmailLogService;
use App\Services\SystemGeneratedEmailSearchService;
use Illuminate\Http\Request;

class SystemGeneratedEmailController extends Controller
{
    public function __construct(
        private SystemGeneratedEmailSearchService $searchService,
        private SystemEmailLogService $logService,
    ) {
        $this->middleware('auth:admin');
    }

    public function dashboard()
    {
        $stats = $this->searchService->getDashboardStats();

        $recent = $this->searchService->baseQuery()
            ->with(['uploader:id,first_name,last_name', 'client:id,first_name,last_name,client_id,type', 'attachments'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn (EmailLog $e) => $this->prepareListRow($e));

        $categories = SystemEmailLogService::CATEGORIES;

        return view('AdminConsole.features.system-emails.dashboard', compact('stats', 'recent', 'categories'));
    }

    public function index(Request $request)
    {
        $emails = collect();
        $total  = 0;

        if ($request->hasAny(['search', 'date_from', 'date_to', 'category', 'from_address', 'client_id', 'delivery_status', 'has_attachments', 'filter', 'failed'])) {
            $query = $this->searchService->baseQuery();
            $query = $this->searchService->applyFilters($query, $request);
            $query->with(['uploader:id,first_name,last_name', 'client:id,first_name,last_name,client_id,type', 'attachments'])
                  ->orderByDesc('created_at');

            $paginator = $query->paginate(50)->withQueryString();
            $emails    = $paginator->getCollection()->map(fn (EmailLog $e) => $this->prepareListRow($e));
            $total     = $paginator->total();
        } else {
            $paginator = null;
        }

        $selectedClient = null;
        if ($request->filled('client_id')) {
            $selectedClient = Admin::select('id', 'first_name', 'last_name')
                ->find((int) $request->input('client_id'));
        }

        $categories = SystemEmailLogService::CATEGORIES;

        return view('AdminConsole.features.system-emails.index',
            compact('emails', 'total', 'paginator', 'selectedClient', 'categories'));
    }

    public function show(int $id)
    {
        $email = $this->searchService->baseQuery()
            ->with([
                'uploader:id,first_name,last_name,email',
                'client:id,first_name,last_name,client_id,type',
                'clientMatter.matter:id,title,nick_name',
                'attachments',
                'sendgridEvents',
            ])
            ->findOrFail($id);

        $previewUrl  = $this->searchService->resolveS3PreviewUrl($email);
        $toDisplay   = EmailLog::resolveRecipientDisplay($email->to_mail, $email->type);
        $ccDisplay   = EmailLog::resolveRecipientDisplay($email->cc, $email->type);
        $attachments = $email->getFileAttachmentCollection();
        $categoryLabel = $this->logService->categoryLabel($email->system_email_category);

        return view('AdminConsole.features.system-emails.show',
            compact('email', 'previewUrl', 'toDisplay', 'ccDisplay', 'attachments', 'categoryLabel'));
    }

    private function prepareListRow(EmailLog $email): array
    {
        return [
            'id'                    => $email->id,
            'created_at'            => $email->created_at,
            'sent_by'               => $email->uploader
                                        ? trim($email->uploader->first_name . ' ' . $email->uploader->last_name)
                                        : 'System',
            'category'              => $email->system_email_category,
            'category_label'        => $this->logService->categoryLabel($email->system_email_category),
            'from_mail'             => $email->from_mail ?? '—',
            'to_mail'               => EmailLog::resolveRecipientDisplay($email->to_mail, $email->type),
            'subject'               => $email->subject ?? '(no subject)',
            'client_name'           => $email->client
                                        ? trim($email->client->first_name . ' ' . $email->client->last_name)
                                        : '—',
            'client_id'             => $email->client_id,
            'client_ref'            => $email->client?->client_id,
            'type'                  => $email->type,
            'attach_count'          => $email->getFileAttachmentCollection()->count(),
            'delivery_status'       => $email->delivery_status ?? 'pending',
            'delivery_status_label' => \App\Services\SendGridWebhookService::statusLabel($email->delivery_status),
            'delivery_status_badge' => \App\Services\SendGridWebhookService::statusBadgeClass($email->delivery_status),
            'status_reason'         => $email->status_reason,
            'delivered_at'          => $email->delivered_at,
            'opened_at'             => $email->opened_at,
            'clicked_at'            => $email->clicked_at,
            'spam_reported_at'      => $email->spam_reported_at,
        ];
    }
}
