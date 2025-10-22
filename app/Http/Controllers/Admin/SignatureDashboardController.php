<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Admin;
use App\Models\Lead;
use App\Services\SignatureService;
use App\Services\SignatureAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class SignatureDashboardController extends Controller
{
    protected $signatureService;
    protected $analyticsService;

    public function __construct(SignatureService $signatureService, SignatureAnalyticsService $analyticsService)
    {
        $this->signatureService = $signatureService;
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        // Get documents based on user permissions using visibility scope
        $query = Document::with(['creator', 'signers', 'documentable'])
            ->visible($user)
            ->notArchived()
            ->orderBy('created_at', 'desc');

        // Apply filters based on visibility scope
        $query->when($request->has('scope'), function ($q) use ($request, $user) {
            return match($request->scope) {
                'my_documents' => $q->forUser($user->id),
                'team' => $q, // Already filtered by visible(), just keep it
                'organization' => $user->role === 1 ? $q : $q->forUser($user->id),
                default => $q
            };
        })
        // Apply status filters
        ->when($request->has('tab'), function ($q) use ($request) {
            return match($request->tab) {
                'pending' => $q->byStatus('sent'),
                'signed' => $q->byStatus('signed'),
                'sent_by_me' => $q->where('created_by', auth('admin')->id()),
                default => $q
            };
        });

        // Additional filters using modern Laravel syntax
        $query->when($request->filled('status'), fn($q) => $q->byStatus($request->status))
              ->when($request->filled('association'), function ($q) use ($request) {
                  return $request->association === 'associated' 
                      ? $q->associated() 
                      : $q->adhoc();
              })
              ->when($request->filled('search'), function ($q) use ($request) {
                  $search = $request->search;
                  return $q->where(function($subQ) use ($search) {
                      $subQ->where('title', 'like', "%{$search}%")
                           ->orWhere('file_name', 'like', "%{$search}%")
                           ->orWhere('primary_signer_email', 'like', "%{$search}%");
                  });
              });

        $documents = $query->paginate(20);

        // Get counts for dashboard cards using visibility scope
        $counts = [
            'sent_by_me' => Document::forUser($user->id)->notArchived()->count(),
            'visible_to_me' => Document::visible($user)->notArchived()->count(),
            'pending' => Document::visible($user)->byStatus('sent')->notArchived()->count(),
            'signed' => Document::visible($user)->byStatus('signed')->notArchived()->count(),
            'overdue' => Document::visible($user)
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->where('status', '!=', 'signed')
                ->notArchived()
                ->count(),
        ];

        // Add admin counts if user is admin
        if ($user->role === 1) {
            $counts['all'] = Document::notArchived()->count();
            $counts['all_pending'] = Document::byStatus('sent')->notArchived()->count();
        }

        // Provide errors variable for the layout
        $errors = $request->session()->get('errors') ?? new \Illuminate\Support\MessageBag();

        return view('Admin.signatures.dashboard', compact('documents', 'counts', 'user', 'errors'));
    }

    public function create(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        // Check authorization
        $this->authorize('create', Document::class);
        
        // Get clients and leads for association dropdown
        $clients = Admin::where('role', '=', 7)->whereNull('is_deleted')->get(['id', 'first_name', 'last_name', 'email']);
        $leads = Lead::get(['id', 'first_name', 'last_name', 'email']);

        // Get active email accounts for template picker
        $emailAccounts = \App\Models\Email::where('status', true)
            ->select('id', 'email', 'display_name')
            ->orderBy('email')
            ->get();

        // Check if we're sending an existing document for signing
        $document = null;
        if ($request->has('document_id')) {
            $document = Document::with('signatureFields')->findOrFail($request->document_id);
            // For existing documents, we'll allow adding signers even if status is not signature_placed
            // The user can still add signers and the system will handle the workflow
        }

        // Provide errors variable for the layout
        $errors = request()->session()->get('errors') ?? new \Illuminate\Support\MessageBag();

        return view('Admin.signatures.create', compact('clients', 'leads', 'emailAccounts', 'user', 'errors', 'document'));
    }

    public function store(Request $request)
    {
        // Check authorization
        $this->authorize('create', Document::class);
        
        // Check if we're using an existing document
        if ($request->has('document_id')) {
            $request->validate([
                'document_id' => 'required|integer|exists:documents,id',
                'signer_email' => 'required|email',
                'signer_name' => 'required|string|min:2|max:100',
                'email_template' => 'nullable|string',
                'email_subject' => 'nullable|string|max:255',
                'email_message' => 'nullable|string|max:1000',
                'from_email' => 'nullable|email',
                'client_matter_id' => 'nullable|integer|exists:client_matters,id',
                'selected_client_id' => 'nullable|integer|exists:admins,id',
            ]);
            
            $document = Document::findOrFail($request->document_id);
            
            // Check for duplicate signer
            $existingSigner = $document->signers()->where('email', $request->signer_email)->first();
            if ($existingSigner && $existingSigner->status === 'pending') {
                return redirect()->back()->withErrors(['signer_email' => 'A signing link has already been sent to this email address.']);
            }

            // Create new signer
            $signer = $document->signers()->create([
                'email' => $request->signer_email,
                'name' => $request->signer_name,
                'token' => Str::random(64),
                'status' => 'pending',
            ]);
            
            // Associate document with matter if specified, or with lead/client if no matter
            if ($request->has('client_matter_id') && $request->client_matter_id) {
                // Get the matter details
                $matter = \DB::table('client_matters')
                    ->where('id', $request->client_matter_id)
                    ->first();
                    
                if ($matter) {
                    // Associate document with the client
                    $client = Admin::find($matter->client_id);
                    if ($client) {
                        $document->update([
                            'documentable_type' => Admin::class,
                            'documentable_id' => $client->id,
                            'origin' => 'client',
                        ]);
                        
                        // Create a note about the matter association
                        \App\Models\DocumentNote::create([
                            'document_id' => $document->id,
                            'created_by' => auth('admin')->id(),
                            'action_type' => 'associated',
                            'note' => "Document associated with matter: {$matter->client_unique_matter_no}",
                            'metadata' => [
                                'matter_id' => $matter->id,
                                'matter_number' => $matter->client_unique_matter_no,
                                'client_id' => $client->id
                            ]
                        ]);
                    }
                }
            } elseif ($request->has('selected_client_id') && $request->selected_client_id) {
                // Associate document with selected client/lead (no specific matter)
                $entity = Admin::find($request->selected_client_id);
                if ($entity) {
                    $entityType = ($entity->type === 'lead') ? 'lead' : 'client';
                    
                    $document->update([
                        'documentable_type' => Admin::class,
                        'documentable_id' => $entity->id,
                        'origin' => $entityType,
                    ]);
                    
                    // Create a note about the association
                    $folderName = ($entityType === 'lead') ? 'personal documents' : 'general documents';
                    \App\Models\DocumentNote::create([
                        'document_id' => $document->id,
                        'created_by' => auth('admin')->id(),
                        'action_type' => 'associated',
                        'note' => "Document associated with {$entityType}: {$entity->first_name} {$entity->last_name} (folder: {$folderName})",
                        'metadata' => [
                            'entity_id' => $entity->id,
                            'entity_type' => $entityType,
                            'folder' => $folderName
                        ]
                    ]);
                }
            }

            // Update document status based on current status
            if ($document->status === 'draft') {
                $document->update(['status' => 'sent']);
            } elseif ($document->status === 'signature_placed') {
                $document->update(['status' => 'sent']);
            }

            // Send email to signer
            $signingUrl = url("/sign/{$document->id}/{$signer->token}");
            
            // Determine template
            $template = $request->email_template ?? 'emails.signature.send';
            if ($document->document_type === 'agreement') {
                $template = 'emails.signature.send_agreement';
            }
            
            $subject = $request->email_subject ?? 'Document Signature Request from Bansal Migration';
            $message = $request->email_message ?? "Please review and sign the attached document.";
            
            // Prepare template data
            $templateData = [
                'signerName' => $signer->name,
                'documentTitle' => $document->display_title ?? $document->title,
                'signingUrl' => $signingUrl,
                'message' => $message,
                'documentType' => $document->document_type ?? 'document',
                'dueDate' => $document->due_at ? $document->due_at->format('F j, Y') : null,
            ];

            try {
                // Apply email configuration if specified
                if ($request->from_email) {
                    $emailConfig = app(\App\Services\EmailConfigService::class)->forAccount($request->from_email);
                    app(\App\Services\EmailConfigService::class)->applyConfig($emailConfig);
                } else {
                    $defaultConfig = app(\App\Services\EmailConfigService::class)->getDefaultAccount();
                    if ($defaultConfig) {
                        app(\App\Services\EmailConfigService::class)->applyConfig($defaultConfig);
                    }
                }

                // Send email
                Mail::send($template, $templateData, function ($mail) use ($signer, $subject) {
                    $mail->to($signer->email, $signer->name)
                         ->subject($subject);
                });

                $successMessage = "Signer added successfully! Signing link sent to {$signer->email}";
                if ($request->has('client_matter_id') && $request->client_matter_id) {
                    $matter = \DB::table('client_matters')->where('id', $request->client_matter_id)->first();
                    if ($matter) {
                        $successMessage .= " and document associated with matter: {$matter->client_unique_matter_no}";
                    }
                } elseif ($request->has('selected_client_id') && $request->selected_client_id) {
                    $entity = Admin::find($request->selected_client_id);
                    if ($entity) {
                        $entityType = ($entity->type === 'lead') ? 'lead' : 'client';
                        $folderName = ($entityType === 'lead') ? 'personal documents' : 'general documents';
                        $successMessage .= " and document associated with {$entityType}: {$entity->first_name} {$entity->last_name} (folder: {$folderName})";
                    }
                }
                
                return redirect()->route('admin.signatures.show', $document->id)
                    ->with('success', $successMessage);
            } catch (\Exception $e) {
                \Log::error('Failed to send signing email', [
                    'signer_id' => $signer->id,
                    'error' => $e->getMessage()
                ]);
                
                return redirect()->route('admin.signatures.show', $document->id)
                    ->with('error', 'Signer added but failed to send email. You can copy the signing link manually.');
            }
        } else {
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
                'title' => 'nullable|string|max:255',
                'signer_email' => 'required|email',
                'signer_name' => 'required|string|min:2|max:100',
                'document_type' => 'nullable|string|in:agreement,nda,general,contract',
                'priority' => 'nullable|string|in:low,normal,high',
                'due_at' => 'nullable|date|after:now',
                'association_type' => 'nullable|string|in:client,lead',
                'association_id' => 'nullable|integer',
                'client_matter_id' => 'nullable|integer',
            ]);
            
            // Handle file upload for new documents
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            // Create document
            $document = Document::create([
                'file_name' => $fileName,
                'filetype' => $file->getClientMimeType(),
                'myfile' => $filePath,
                'title' => $request->title ?: pathinfo($fileName, PATHINFO_FILENAME),
                'status' => 'draft',
                'client_id' => Auth::guard('admin')->id(),
            ]);
        }

        $user = Auth::guard('admin')->user();

        // Set association if provided
        if ($request->association_type && $request->association_id) {
            $this->signatureService->associate(
                $document, 
                $request->association_type, 
                $request->association_id
            );
        }

        // Store signer information in session for later use
        session([
            'pending_document_signer' => [
                'email' => $request->signer_email,
                'name' => $request->signer_name,
                'email_subject' => $request->email_subject,
                'email_message' => $request->email_message,
                'email_template' => $request->email_template,
                'from_email' => $request->from_email,
            ]
        ]);

        // Redirect to signature placement page
        return redirect()->route('admin.documents.edit', $document->id)
            ->with('success', 'Document uploaded! Now place signature fields on the document.');
    }

    public function show($id)
    {
        $document = Document::with(['creator', 'signers', 'documentable', 'signatureFields', 'notes.creator'])
            ->findOrFail($id);

        // Check authorization using policy
        $this->authorize('view', $document);

        // Get active email accounts for template picker
        $emailAccounts = \App\Models\Email::where('status', true)
            ->select('id', 'email', 'display_name')
            ->orderBy('email')
            ->get();

        // Provide errors variable for the layout
        $errors = request()->session()->get('errors') ?? new \Illuminate\Support\MessageBag();

        return view('Admin.signatures.show', compact('document', 'errors', 'emailAccounts'));
    }

    public function sendReminder(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        
        // Check authorization
        $this->authorize('sendReminder', $document);
        
        $signerId = $request->signer_id;
        $signer = $document->signers()->findOrFail($signerId);

        // Use service to send reminder
        $success = $this->signatureService->remind($signer);

        if ($success) {
            return back()->with('success', 'Reminder sent successfully!');
        } else {
            return back()->with('error', 'Failed to send reminder. Please check limits and try again.');
        }
    }

    public function copyLink($id)
    {
        $document = Document::findOrFail($id);
        
        // Check authorization
        $this->authorize('view', $document);
        
        $signer = $document->signers()->first();
        
        if (!$signer) {
            return back()->with('error', 'No signer found for this document.');
        }

        $signingUrl = url("/sign/{$document->id}/{$signer->token}");
        
        return back()->with('success', "Signing link copied to clipboard: {$signingUrl}");
    }

    public function suggestAssociation(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $matches = [];
        
        // Find all clients and leads with this email (both are in admins table with role = 7)
        $entities = Admin::where('email', $request->email)
            ->where('role', '=', 7)
            ->whereNull('is_deleted')
            ->get();
            
        foreach ($entities as $entity) {
            // Determine if it's a client or lead based on type field
            $entityType = ($entity->type === 'lead') ? 'lead' : 'client';
            
            if ($entityType === 'client') {
                // Get client's matters
                $matters = \DB::table('client_matters')
                    ->where('client_id', $entity->id)
                    ->join('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                    ->select(
                        'client_matters.id',
                        'client_matters.client_unique_matter_no',
                        'matters.title as matter_title',
                        'client_matters.matter_status'
                    )
                    ->orderBy('client_matters.created_at', 'desc')
                    ->get()
                    ->map(function($matter) {
                        return [
                            'id' => $matter->id,
                            'label' => $matter->client_unique_matter_no . ' - ' . $matter->matter_title,
                            'status' => $matter->matter_status
                        ];
                    })
                    ->toArray();
            } else {
                // Leads don't have matters
                $matters = [];
            }

            $matches[] = [
                'type' => $entityType,
                'id' => $entity->id,
                'name' => trim("{$entity->first_name} {$entity->last_name}"),
                'email' => $entity->email,
                'matters' => $matters,
                'has_matters' => count($matters) > 0
            ];
        }

        return response()->json([
            'success' => true,
            'matches' => $matches,
            'match' => count($matches) === 1 ? $matches[0] : null // For backward compatibility
        ]);
    }

    public function getClientMatters($id)
    {
        $matters = \DB::table('client_matters')
            ->where('client_id', $id)
            ->join('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
            ->select(
                'client_matters.id',
                'client_matters.client_unique_matter_no',
                'matters.title as matter_title',
                'client_matters.matter_status'
            )
            ->orderBy('client_matters.created_at', 'desc')
            ->get()
            ->map(function($matter) {
                return [
                    'id' => $matter->id,
                    'label' => $matter->client_unique_matter_no . ' - ' . $matter->matter_title,
                    'status' => $matter->matter_status
                ];
            });

        return response()->json([
            'success' => true,
            'matters' => $matters
        ]);
    }

    /**
     * Associate a document with a client or lead (post-signing)
     */
    public function associate(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        
        // Check authorization
        $this->authorize('update', $document);

        $request->validate([
            'entity_type' => 'required|string|in:client,lead',
            'entity_id' => 'required|integer',
            'note' => 'nullable|string|max:500'
        ]);

        $success = $this->signatureService->associate(
            $document,
            $request->entity_type,
            $request->entity_id,
            $request->note
        );

        if ($success) {
            return back()->with('success', 'Document successfully attached to ' . $request->entity_type . '!');
        } else {
            return back()->with('error', 'Failed to attach document. Please try again.');
        }
    }

    /**
     * Detach a document from its current association
     */
    public function detach(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        
        // Check authorization (admin only)
        $this->authorize('update', $document);

        $user = Auth::guard('admin')->user();
        if ($user->role !== 1) {
            return back()->with('error', 'Only administrators can detach documents.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $success = $this->signatureService->detach(
            $document,
            $request->reason
        );

        if ($success) {
            return back()->with('success', 'Document successfully detached!');
        } else {
            return back()->with('error', 'Failed to detach document. Please try again.');
        }
    }

    /**
     * Show analytics dashboard
     */
    public function analytics(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        // Date range filtering
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // Get analytics data
        $medianHours = $this->analyticsService->getMedianTimeToSign();
        $completionRate = $this->analyticsService->getCompletionRate($startDate, $endDate);
        $avgReminders = $this->analyticsService->getAverageReminders($startDate, $endDate);
        $overdueCount = $this->analyticsService->getOverdueCount();
        
        // Get detailed data
        $topSigners = $this->analyticsService->getTopSigners(10);
        $documentTypeStats = $this->analyticsService->getDocumentTypeStats();
        $trendData = $this->analyticsService->getSignatureTrend($startDate, $endDate, 'day');
        $overdueDocuments = $this->analyticsService->getOverdueAnalytics();
        
        // Admin-only: User performance
        $userPerformance = null;
        if ($user->role === 1) {
            $userPerformance = $this->analyticsService->getUserPerformance();
        }
        
        // Activity by hour
        $activityByHour = $this->analyticsService->getActivityByHour();
        
        // Provide errors variable for the layout
        $errors = $request->session()->get('errors') ?? new \Illuminate\Support\MessageBag();
        
        return view('Admin.signatures.analytics', compact(
            'medianHours',
            'completionRate',
            'avgReminders',
            'overdueCount',
            'topSigners',
            'documentTypeStats',
            'trendData',
            'overdueDocuments',
            'userPerformance',
            'activityByHour',
            'startDate',
            'endDate',
            'user',
            'errors'
        ));
    }

    /**
     * Bulk archive documents
     */
    public function bulkArchive(Request $request)
    {
        // Decode JSON if necessary
        $ids = is_string($request->ids) ? json_decode($request->ids, true) : $request->ids;
        
        $request->merge(['ids' => $ids]);
        $request->validate(['ids' => 'required|array|min:1']);
        
        try {
            $count = Document::whereIn('id', $ids)
                ->whereNull('archived_at')
                ->update(['archived_at' => now()]);
            
            return back()->with('success', "Successfully archived {$count} document(s)");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to archive documents: ' . $e->getMessage());
        }
    }

    /**
     * Bulk void documents
     */
    public function bulkVoid(Request $request)
    {
        // Decode JSON if necessary
        $ids = is_string($request->ids) ? json_decode($request->ids, true) : $request->ids;
        
        $request->merge(['ids' => $ids]);
        $request->validate([
            'ids' => 'required|array|min:1',
            'reason' => 'nullable|string|max:500'
        ]);
        
        try {
            $user = Auth::guard('admin')->user();
            $documents = Document::whereIn('id', $ids)->get();
            $count = 0;
            $skipped = 0;
            
            foreach ($documents as $doc) {
                // Check authorization using Gate instead of policy directly
                if ($user->can('void', $doc)) {
                    if ($this->signatureService->void($doc, $request->reason)) {
                        $count++;
                    }
                } else {
                    $skipped++;
                }
            }
            
            $message = "Successfully voided {$count} document(s)";
            if ($skipped > 0) {
                $message .= " ({$skipped} skipped due to permissions)";
            }
            
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to void documents: ' . $e->getMessage());
        }
    }

    /**
     * Bulk resend reminders
     */
    public function bulkResend(Request $request)
    {
        // Decode JSON if necessary
        $ids = is_string($request->ids) ? json_decode($request->ids, true) : $request->ids;
        
        $request->merge(['ids' => $ids]);
        $request->validate(['ids' => 'required|array|min:1']);
        
        try {
            $documents = Document::with('signers')->whereIn('id', $ids)->get();
            $sent = 0;
            $skipped = 0;
            
            foreach ($documents as $doc) {
                foreach ($doc->signers as $signer) {
                    if ($signer->status === 'pending') {
                        if ($this->signatureService->remind($signer)) {
                            $sent++;
                        } else {
                            $skipped++;
                        }
                    }
                }
            }
            
            $message = "Sent {$sent} reminder(s)";
            if ($skipped > 0) {
                $message .= " ({$skipped} skipped due to limits)";
            }
            
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send reminders: ' . $e->getMessage());
        }
    }

    /**
     * Export audit report
     */
    public function exportAudit(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);
        
        $query = Document::with(['creator', 'signers', 'documentable', 'notes'])
            ->notArchived();
        
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }
        
        $documents = $query->orderBy('created_at', 'desc')->get();
        
        if ($request->format === 'csv') {
            return $this->exportCSV($documents);
        } else {
            return $this->exportPDF($documents);
        }
    }

    /**
     * Export as CSV
     */
    protected function exportCSV($documents)
    {
        $filename = 'signature_audit_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($documents) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Document ID',
                'Title',
                'Status',
                'Created By',
                'Created At',
                'Signer Email',
                'Signer Name',
                'Signer Status',
                'Sent At',
                'Signed At',
                'Reminders Sent',
                'Document Type',
                'Priority',
                'Associated With',
                'Due Date'
            ]);
            
            // Data rows
            foreach ($documents as $doc) {
                foreach ($doc->signers as $signer) {
                    $association = 'Ad-hoc';
                    if ($doc->documentable) {
                        $type = class_basename($doc->documentable_type);
                        $name = isset($doc->documentable->first_name) 
                            ? $doc->documentable->first_name . ' ' . $doc->documentable->last_name 
                            : 'Unknown';
                        $association = "{$type}: {$name}";
                    }
                    
                    fputcsv($file, [
                        $doc->id,
                        $doc->display_title,
                        $doc->status,
                        $doc->creator ? $doc->creator->first_name . ' ' . $doc->creator->last_name : 'Unknown',
                        $doc->created_at->format('Y-m-d H:i:s'),
                        $signer->email,
                        $signer->name,
                        $signer->status,
                        $doc->created_at->format('Y-m-d H:i:s'),
                        $signer->signed_at ? $signer->signed_at->format('Y-m-d H:i:s') : 'N/A',
                        $signer->reminder_count,
                        $doc->document_type ?? 'general',
                        $doc->priority ?? 'normal',
                        $association,
                        $doc->due_at ? $doc->due_at->format('Y-m-d') : 'N/A'
                    ]);
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export as PDF
     */
    protected function exportPDF($documents)
    {
        $pdf = \PDF::loadView('Admin.signatures.audit_report', compact('documents'));
        return $pdf->download('signature_audit_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Preview email template before sending
     */
    public function previewEmail(Request $request)
    {
        $request->validate([
            'template' => 'required|string',
            'signer_name' => 'required|string',
            'document_title' => 'nullable|string',
            'message' => 'nullable|string',
        ]);

        $template = $request->template;
        $documentTitle = $request->document_title ?: 'Your Document';
        
        // Mock data for preview
        $templateData = [
            'signerName' => $request->signer_name,
            'documentTitle' => $documentTitle,
            'signingUrl' => url('/sign/preview/token-preview'),
            'message' => $request->message ?: 'Please review and sign the attached document.',
            'documentType' => 'document',
            'dueDate' => now()->addDays(7)->format('F j, Y'),
            'reminderNumber' => 1, // For reminder template
        ];

        try {
            $html = view($template, $templateData)->render();
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to render template: ' . $e->getMessage()
            ], 500);
        }
    }
}