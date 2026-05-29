<?php

namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\EmailLog;
use App\Models\Staff;
use App\Services\OutgoingEmailSearchService;
use Illuminate\Http\Request;

class OutgoingEmailController extends Controller
{
    public function __construct(private OutgoingEmailSearchService $searchService)
    {
        $this->middleware('auth:admin');
    }

    /**
     * Dashboard — stats cards + top senders + recent 10 emails.
     */
    public function dashboard()
    {
        $stats = $this->searchService->getDashboardStats();

        $recent = $this->searchService->baseQuery()
            ->with(['uploader:id,first_name,last_name', 'client:id,first_name,last_name,client_id,type', 'attachments'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn (EmailLog $e) => $this->prepareListRow($e));

        return view('AdminConsole.features.sent-emails.dashboard', compact('stats', 'recent'));
    }

    /**
     * Searchable, paginated list of outgoing emails.
     */
    public function index(Request $request)
    {
        $staffList = Staff::where('status', 1)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        $emails = collect();
        $total  = 0;

        if ($request->hasAny(['search', 'date_from', 'date_to', 'sender_id', 'from_address', 'client_id', 'type', 'has_attachments', 'filter'])) {
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

        // Pre-fill client name for ajax select persistence
        $selectedClient = null;
        if ($request->filled('client_id')) {
            $selectedClient = Admin::select('id', 'first_name', 'last_name')
                ->find((int) $request->input('client_id'));
        }

        return view('AdminConsole.features.sent-emails.index',
            compact('emails', 'total', 'staffList', 'paginator', 'selectedClient'));
    }

    /**
     * Full detail for a single outgoing email.
     */
    public function show(int $id)
    {
        $email = $this->searchService->baseQuery()
            ->with([
                'uploader:id,first_name,last_name,email',
                'client:id,first_name,last_name,client_id,type',
                'clientMatter.matter:id,title,nick_name',
                'attachments',
            ])
            ->findOrFail($id);

        $previewUrl  = $this->searchService->resolveS3PreviewUrl($email);
        $toDisplay   = EmailLog::resolveRecipientDisplay($email->to_mail, $email->type);
        $ccDisplay   = EmailLog::resolveRecipientDisplay($email->cc, $email->type);
        $attachments = $email->getFileAttachmentCollection();

        return view('AdminConsole.features.sent-emails.show',
            compact('email', 'previewUrl', 'toDisplay', 'ccDisplay', 'attachments'));
    }

    /**
     * Shape a single EmailLog row for list/dashboard views.
     */
    private function prepareListRow(EmailLog $email): array
    {
        return [
            'id'           => $email->id,
            'created_at'   => $email->created_at,
            'sent_by'      => $email->uploader
                                ? trim($email->uploader->first_name . ' ' . $email->uploader->last_name)
                                : '—',
            'from_mail'    => $email->from_mail ?? '—',
            'to_mail'      => EmailLog::resolveRecipientDisplay($email->to_mail, $email->type),
            'subject'      => $email->subject ?? '(no subject)',
            'client_name'  => $email->client
                                ? trim($email->client->first_name . ' ' . $email->client->last_name)
                                : '—',
            'client_id'    => $email->client_id,
            'client_ref'   => $email->client->client_id ?? null,
            'type'         => $email->type,
            'attach_count' => $email->getFileAttachmentCollection()->count(),
            'delivery_status' => $email->delivery_status ?? 'pending',
            'delivery_status_label' => \App\Services\SendGridWebhookService::statusLabel($email->delivery_status),
            'delivery_status_badge' => \App\Services\SendGridWebhookService::statusBadgeClass($email->delivery_status),
            'status_reason' => $email->status_reason,
            'delivered_at' => $email->delivered_at,
        ];
    }
}
