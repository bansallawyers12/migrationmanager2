<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Admin;
use App\Models\Lead;
use App\Services\SignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignatureDashboardController extends Controller
{
    protected $signatureService;

    public function __construct(SignatureService $signatureService)
    {
        $this->signatureService = $signatureService;
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

    public function create()
    {
        $user = Auth::guard('admin')->user();
        
        // Check authorization
        $this->authorize('create', Document::class);
        
        // Get clients and leads for association dropdown
        $clients = Admin::where('role', '!=', 7)->get(['id', 'first_name', 'last_name', 'email']);
        $leads = Lead::get(['id', 'first_name', 'last_name', 'email']);

        // Get active email accounts for template picker
        $emailAccounts = \App\Models\Email::where('status', true)
            ->select('id', 'email', 'display_name')
            ->orderBy('email')
            ->get();

        // Provide errors variable for the layout
        $errors = request()->session()->get('errors') ?? new \Illuminate\Support\MessageBag();

        return view('Admin.signatures.create', compact('clients', 'leads', 'emailAccounts', 'user', 'errors'));
    }

    public function store(Request $request)
    {
        // Check authorization
        $this->authorize('create', Document::class);
        
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

        $user = Auth::guard('admin')->user();

        // Handle file upload
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // Create document
        $document = Document::create([
            'file_name' => $fileName,
            'filetype' => $file->getClientMimeType(),
            'myfile' => $filePath,
            'file_size' => $file->getSize(),
            'status' => 'draft',
            'created_by' => $user->id,
            'origin' => 'ad_hoc',
            'title' => $request->title ?: $file->getClientOriginalName(),
            'document_type' => $request->document_type ?: 'general',
            'priority' => $request->priority ?: 'normal',
            'due_at' => $request->due_at,
            'primary_signer_email' => $request->signer_email,
            'signer_count' => 1,
            'last_activity_at' => now(),
            'client_matter_id' => $request->client_matter_id,
        ]);

        // Set association if provided
        if ($request->association_type && $request->association_id) {
            $this->signatureService->associate(
                $document, 
                $request->association_type, 
                $request->association_id
            );
        }

        // Send document for signature using service
        $signers = [
            ['email' => $request->signer_email, 'name' => $request->signer_name]
        ];
        
        // Prepare email options
        $emailOptions = [
            'subject' => $request->email_subject ?: 'Document Signature Request from Bansal Migration',
            'message' => $request->email_message ?: 'Please review and sign the attached document.',
            'template' => $request->email_template ?: 'emails.signature.send',
        ];
        
        // Add from_email if specified
        if ($request->filled('from_email')) {
            $emailOptions['from_email'] = $request->from_email;
        }
        
        $success = $this->signatureService->send($document, $signers, $emailOptions);

        if ($success) {
            return redirect()->route('admin.signatures.index')
                ->with('success', 'Document sent for signature successfully!');
        } else {
            return redirect()->route('admin.signatures.index')
                ->with('error', 'Document created but email failed to send. Please try again.');
        }
    }

    public function show($id)
    {
        $document = Document::with(['creator', 'signers', 'documentable', 'signatureFields', 'notes.creator'])
            ->findOrFail($id);

        // Check authorization using policy
        $this->authorize('view', $document);

        // Provide errors variable for the layout
        $errors = request()->session()->get('errors') ?? new \Illuminate\Support\MessageBag();

        return view('Admin.signatures.show', compact('document', 'errors'));
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

        $suggestion = $this->signatureService->suggestAssociation($request->email);

        return response()->json([
            'success' => true,
            'match' => $suggestion
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