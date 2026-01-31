<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ClientEoiReference;
use App\Models\ActivitiesLog;
use App\Models\Document;
use App\Models\VisaDocumentType;
use App\Services\PointsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\EoiConfirmationMail;

class ClientEoiRoiController extends Controller
{
    protected PointsService $pointsService;

    public function __construct(PointsService $pointsService)
    {
        $this->middleware('auth:admin');
        $this->pointsService = $pointsService;
    }

    /**
     * Display a listing of EOI/ROI records for a client
     * 
     * GET /clients/{client}/eoi-roi
     */
    public function index(Admin $client): JsonResponse
    {
        try {
            // TODO: Implement AdminPolicy and re-enable authorization
            // $this->authorize('view', $client);

            $eoiRecords = ClientEoiReference::where('client_id', $client->id)
                ->with(['creator', 'updater'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($eoi) {
                    return $this->formatEoiForResponse($eoi);
                });

            return response()->json([
                'success' => true,
                'data' => $eoiRecords,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching EOI records', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load EOI records',
            ], 500);
        }
    }

    /**
     * Show a specific EOI/ROI record
     * 
     * GET /clients/{client}/eoi-roi/{eoiReference}
     */
    public function show(Admin $client, ClientEoiReference $eoiReference): JsonResponse
    {
        try {
            $this->authorize('view', $client);

            if ($eoiReference->client_id !== $client->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'EOI record not found for this client',
                ], 404);
            }

            // Load relationships for workflow display
            $eoiReference->load(['creator', 'updater', 'verifier', 'client']);

            return response()->json([
                'success' => true,
                'data' => $this->formatEoiForResponse($eoiReference, true),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching EOI record', [
                'eoi_id' => $eoiReference->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load EOI record',
            ], 500);
        }
    }

    /**
     * Create or update an EOI/ROI record
     * 
     * POST /clients/{client}/eoi-roi
     */
    public function upsert(Request $request, Admin $client): JsonResponse
    {
        try {
            $this->authorize('update', $client);

            // Validate input
            $validator = $this->validateEoiData($request);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validated();

            DB::beginTransaction();

            // Check if updating or creating
            $eoiId = $request->input('id');
            
            if ($eoiId) {
                $eoi = ClientEoiReference::findOrFail($eoiId);
                
                if ($eoi->client_id !== $client->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'EOI record not found',
                    ], 404);
                }
            } else {
                $eoi = new ClientEoiReference();
                $eoi->client_id = $client->id;
                $eoi->admin_id = auth('admin')->id();
            }

            // Normalize dates from dd/mm/yyyy to Y-m-d
            $eoi->EOI_number = $validated['eoi_number'];
            $eoi->eoi_subclasses = $validated['eoi_subclasses'];
            $eoi->eoi_states = $validated['eoi_states'];
            $eoi->EOI_occupation = $validated['eoi_occupation'] ?? null;
            $eoi->EOI_point = $validated['eoi_points'] ?? null;
            $eoi->EOI_submission_date = $this->normalizeDate($validated['eoi_submission_date'] ?? null);
            $eoi->eoi_invitation_date = $this->normalizeDate($validated['eoi_invitation_date'] ?? null);
            $eoi->eoi_nomination_date = $this->normalizeDate($validated['eoi_nomination_date'] ?? null);
            $eoi->EOI_ROI = $validated['eoi_roi'] ?? null;
            $eoi->eoi_status = $validated['eoi_status'] ?? 'draft';

            // Handle password (will be encrypted by model mutator)
            if (!empty($validated['eoi_password'])) {
                $eoi->EOI_password = $validated['eoi_password'];
            }

            $eoi->save();

            // Clear points cache for this client
            $this->pointsService->clearCache($client->id);

            // Log activity
            $action = $eoiId ? 'updated' : 'created';
            $this->logActivity(
                $client->id,
                "EOI Record " . ucfirst($action),
                "EOI #" . $eoi->EOI_number . " was {$action} by " . auth('admin')->user()->first_name . ' ' . auth('admin')->user()->last_name,
                'eoi_' . $action
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $eoiId ? 'EOI record updated successfully' : 'EOI record created successfully',
                'data' => $this->formatEoiForResponse($eoi->fresh(), true),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error saving EOI record', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save EOI record: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an EOI/ROI record
     * 
     * DELETE /clients/{client}/eoi-roi/{eoiReference}
     */
    public function destroy(Admin $client, ClientEoiReference $eoiReference): JsonResponse
    {
        try {
            $this->authorize('update', $client);

            if ($eoiReference->client_id !== $client->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'EOI record not found for this client',
                ], 404);
            }

            DB::beginTransaction();

            $eoiNumber = $eoiReference->EOI_number;
            $eoiReference->delete();

            // Clear points cache
            $this->pointsService->clearCache($client->id);

            // Log activity
            $this->logActivity(
                $client->id,
                "EOI Record Deleted",
                "EOI #" . $eoiNumber . " was deleted by " . auth('admin')->user()->first_name . ' ' . auth('admin')->user()->last_name,
                'eoi_deleted'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'EOI record deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting EOI record', [
                'eoi_id' => $eoiReference->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete EOI record',
            ], 500);
        }
    }

    /**
     * Calculate points for a client with optional subclass
     * 
     * GET /clients/{client}/eoi-roi/calculate-points?subclass=190
     */
    public function calculatePoints(Request $request, Admin $client): JsonResponse
    {
        try {
            // TODO: Implement AdminPolicy and re-enable authorization
            // $this->authorize('view', $client);

            // Load client relationships needed for points calculation (following existing codebase pattern)
            $client->testScores = \App\Models\ClientTestScore::where('client_id', $client->id)->get();
            $client->qualifications = \App\Models\ClientQualification::where('client_id', $client->id)->orderByRaw('finish_date DESC NULLS LAST')->get();
            $client->experiences = \App\Models\ClientExperience::where('client_id', $client->id)->orderByRaw('job_finish_date DESC NULLS LAST')->get();
            $client->partner = \App\Models\ClientSpouseDetail::where('client_id', $client->id)->first();

            $subclass = $request->input('subclass');
            $monthsAhead = (int) ($request->input('months_ahead', 6));

            $result = $this->pointsService->compute($client, $subclass, $monthsAhead);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('Error calculating points', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate points: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View the decrypted password for an EOI record (authorized viewing only)
     * 
     * GET /clients/{client}/eoi-roi/{eoiReference}/reveal-password
     */
    public function revealPassword(Admin $client, ClientEoiReference $eoiReference): JsonResponse
    {
        try {
            $this->authorize('update', $client); // Only allow admins with update permission

            if ($eoiReference->client_id !== $client->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'EOI record not found',
                ], 404);
            }

            $password = $eoiReference->getEOIPasswordDecrypted();

            // Log access for audit trail
            Log::info('EOI password viewed', [
                'eoi_id' => $eoiReference->id,
                'client_id' => $client->id,
                'viewed_by' => auth('admin')->id(),
                'viewed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'password' => $password,
            ]);
        } catch (\Exception $e) {
            Log::error('Error revealing password', [
                'eoi_id' => $eoiReference->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reveal password',
            ], 500);
        }
    }

    /**
     * Staff verifies EOI details
     * 
     * POST /clients/{client}/eoi-roi/{eoiReference}/verify
     */
    public function verifyByStaff(Admin $client, ClientEoiReference $eoiReference): JsonResponse
    {
        try {
            $this->authorize('update', $client);

            if ($eoiReference->client_id !== $client->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'EOI record not found',
                ], 404);
            }

            // Update verification fields
            $eoiReference->staff_verified = true;
            $eoiReference->confirmation_date = Carbon::now();
            $eoiReference->checked_by = auth('admin')->id();
            $eoiReference->save();

            // Log activity
            $this->logActivity(
                $client->id,
                'EOI Verified by Staff',
                'EOI details verified by ' . auth('admin')->user()->first_name . ' ' . auth('admin')->user()->last_name . 
                ' for EOI #' . $eoiReference->EOI_number,
                'eoi_verification'
            );

            return response()->json([
                'success' => true,
                'message' => 'EOI details verified successfully. You can now send confirmation email to the client.',
                'confirmation_date' => $eoiReference->confirmation_date->format('d/m/Y H:i'),
                'checked_by' => auth('admin')->user()->first_name . ' ' . auth('admin')->user()->last_name
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifying EOI', [
                'eoi_id' => $eoiReference->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Error verifying EOI details'], 500);
        }
    }

    /**
     * Send or resend confirmation email to client
     * 
     * POST /clients/{client}/eoi-roi/{eoiReference}/send-email
     */
    public function sendConfirmationEmail(Admin $client, ClientEoiReference $eoiReference, Request $request): JsonResponse
    {
        try {
            $this->authorize('update', $client);

            if ($eoiReference->client_id !== $client->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'EOI record not found',
                ], 404);
            }

            // Check if staff has verified first
            if (!$eoiReference->staff_verified) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Please verify the EOI details first before sending to client.'
                ], 400);
            }

            // Check if client exists and has email
            if (!$client->email) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Client email not found. Please update client email first.'
                ], 400);
            }

            $isResend = !empty($eoiReference->confirmation_email_sent_at);

            // Generate unique token for confirmation (or use existing if resending)
            if (!$isResend || empty($eoiReference->client_confirmation_token)) {
                $eoiReference->client_confirmation_token = Str::random(64);
            }
            
            $eoiReference->confirmation_email_sent_at = Carbon::now();
            $eoiReference->client_confirmation_status = 'pending';
            $eoiReference->save();

            // Get EOI/Points/ROI visa documents to attach (per-EOI or shared)
            $attachmentsData = $this->getEoiRelatedAttachments($client, $eoiReference);
            $attachmentLabels = $attachmentsData['labels'];
            $attachments = $attachmentsData['attachments'];

            // Send email
            Mail::to($client->email)->send(new EoiConfirmationMail(
                $eoiReference,
                $client,
                $eoiReference->client_confirmation_token,
                $attachments,
                $attachmentLabels
            ));

            // Log activity
            $action = $isResend ? 'Resent' : 'Sent';
            $this->logActivity(
                $client->id,
                "EOI Confirmation Email {$action}",
                "Confirmation email {$action} to {$client->email} for EOI #{$eoiReference->EOI_number}",
                'email'
            );

            return response()->json([
                'success' => true,
                'message' => "Confirmation email " . strtolower($action) . " successfully to {$client->email}",
                'sent_at' => $eoiReference->confirmation_email_sent_at->format('d/m/Y H:i')
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending confirmation email', [
                'eoi_id' => $eoiReference->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Error sending confirmation email'], 500);
        }
    }

    /**
     * Resolve amendment request (mark as resolved)
     * 
     * POST /clients/{client}/eoi-roi/{eoiReference}/resolve-amendment
     */
    public function resolveAmendment(Admin $client, ClientEoiReference $eoiReference): JsonResponse
    {
        try {
            $this->authorize('update', $client);

            if ($eoiReference->client_id !== $client->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'EOI record not found',
                ], 404);
            }

            if ($eoiReference->client_confirmation_status !== 'amendment_requested') {
                return response()->json([
                    'success' => false,
                    'message' => 'No amendment request found for this EOI',
                ], 400);
            }

            // Reset to pending so email can be resent
            $eoiReference->client_confirmation_status = 'pending';
            $eoiReference->save();

            // Log activity
            $this->logActivity(
                $client->id,
                'EOI Amendment Resolved',
                'Amendment request resolved by ' . auth('admin')->user()->first_name . ' ' . auth('admin')->user()->last_name . 
                ' for EOI #' . $eoiReference->EOI_number,
                'eoi_amendment_resolved'
            );

            return response()->json([
                'success' => true,
                'message' => 'Amendment marked as resolved. You can now resend the confirmation email.',
            ]);

        } catch (\Exception $e) {
            Log::error('Error resolving amendment', [
                'eoi_id' => $eoiReference->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => 'Error resolving amendment'], 500);
        }
    }

    /**
     * Get visa documents from EOI Summary, Points Summary, ROI Draft categories for email attachment.
     * Prefers per-EOI categories (e.g. "EOI Summary - E0121253652") when present; otherwise uses
     * shared categories ("EOI Summary", "Points Summary", "ROI Draft").
     *
     * @return array{attachments: array<int, array{data: string, name: string, mime: string}>, labels: array<int, string>}
     */
    protected function getEoiRelatedAttachments(Admin $client, ClientEoiReference $eoiReference): array
    {
        $eoiNumber = $eoiReference->EOI_number ?? '';
        $bases = ['EOI Summary', 'Points Summary', 'ROI Draft'];

        // For each base, prefer per-EOI category ("EOI Summary - E012...") if it exists, else shared ("EOI Summary")
        $categoryIds = [];
        foreach ($bases as $base) {
            $perEoiTitle = $eoiNumber ? ($base . ' - ' . $eoiNumber) : null;
            $perEoi = $perEoiTitle ? VisaDocumentType::where('status', 1)->where('title', $perEoiTitle)
                ->where(function ($q) use ($client) {
                    $q->whereNull('client_id')->orWhere('client_id', $client->id);
                })->first() : null;
            $shared = VisaDocumentType::where('status', 1)->where('title', $base)
                ->where(function ($q) use ($client) {
                    $q->whereNull('client_id')->orWhere('client_id', $client->id);
                })->first();
            $chosen = $perEoi ?? $shared;
            if ($chosen) {
                $categoryIds[] = $chosen->id;
            }
        }

        if (empty($categoryIds)) {
            return ['attachments' => [], 'labels' => []];
        }

        $categories = VisaDocumentType::whereIn('id', $categoryIds)->pluck('title', 'id')->toArray();
        $documents = Document::where('client_id', $client->id)
            ->where('doc_type', 'visa')
            ->whereIn('folder_name', $categoryIds)
            ->whereNull('not_used_doc')
            ->where('type', 'client')
            ->whereNotNull('myfile')
            ->orderBy('folder_name')
            ->orderBy('created_at')
            ->get();

        $attachments = [];
        $labelsUsed = [];

        foreach ($documents as $doc) {
            $categoryTitle = $categories[$doc->folder_name] ?? 'Document';
            if (!in_array($categoryTitle, $labelsUsed, true)) {
                $labelsUsed[] = $categoryTitle;
            }

            $s3Key = null;
            if (!empty($doc->myfile) && (str_starts_with($doc->myfile, 'http'))) {
                $path = parse_url($doc->myfile, PHP_URL_PATH);
                if ($path) {
                    $s3Key = ltrim(urldecode($path), '/');
                }
            }
            if (empty($s3Key) && !empty($doc->myfile_key)) {
                $s3Key = $client->id . '/visa/' . $doc->myfile_key;
            }
            if (empty($s3Key)) {
                continue;
            }

            try {
                if (!Storage::disk('s3')->exists($s3Key)) {
                    Log::warning('EOI attachment: S3 file not found', ['key' => $s3Key, 'doc_id' => $doc->id]);
                    continue;
                }
                $data = Storage::disk('s3')->get($s3Key);
            } catch (\Throwable $e) {
                Log::warning('EOI attachment: failed to get S3 file', ['key' => $s3Key, 'error' => $e->getMessage()]);
                continue;
            }

            $ext = strtolower($doc->filetype ?? pathinfo($doc->myfile_key ?? '', PATHINFO_EXTENSION) ?: 'pdf');
            $mimeMap = [
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
            ];
            $mime = $mimeMap[$ext] ?? 'application/octet-stream';

            $fileName = $doc->file_name ?: ('document_' . $doc->id);
            $displayName = $categoryTitle . ' - ' . $fileName . (str_contains($fileName, '.') ? '' : '.' . $ext);
            $attachments[] = [
                'data' => $data,
                'name' => $displayName,
                'mime' => $mime,
            ];
        }

        return ['attachments' => $attachments, 'labels' => $labelsUsed];
    }

    /**
     * Log activity to activities_logs table
     * 
     * @param int $clientId
     * @param string $subject
     * @param string $description
     * @param string $activityType
     * @return void
     */
    protected function logActivity($clientId, $subject, $description, $activityType = 'note')
    {
        try {
            ActivitiesLog::create([
                'client_id' => $clientId,
                'created_by' => auth()->guard('admin')->check() ? auth()->guard('admin')->id() : null,
                'subject' => $subject,
                'description' => $description,
                'activity_type' => $activityType,
                'use_for' => 'client'
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging activity', [
                'client_id' => $clientId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Validate EOI data
     */
    protected function validateEoiData(Request $request)
    {
        return Validator::make($request->all(), [
            'id' => 'nullable|integer|exists:client_eoi_references,id',
            'eoi_number' => 'required|string|max:50',
            'eoi_subclasses' => 'required|array|min:1',
            'eoi_subclasses.*' => ['required', 'string', Rule::in(['189', '190', '491'])],
            'eoi_states' => 'required|array|min:1',
            'eoi_states.*' => ['required', 'string', Rule::in(['ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'FED'])],
            'eoi_occupation' => 'nullable|string|max:100',
            'eoi_points' => 'nullable|integer|min:0|max:200',
            'eoi_submission_date' => 'nullable|string', // Will be normalized
            'eoi_invitation_date' => 'nullable|string',
            'eoi_nomination_date' => 'nullable|string',
            'eoi_roi' => 'nullable|string|max:100',
            'eoi_password' => 'nullable|string|max:255',
            'eoi_status' => ['nullable', 'string', Rule::in(['draft', 'submitted', 'invited', 'nominated', 'rejected', 'withdrawn'])],
        ], [
            'eoi_subclasses.*.in' => 'Each subclass must be 189, 190, or 491',
            'eoi_states.*.in' => 'Each state must be a valid Australian state/territory code',
        ]);
    }

    /**
     * Format EOI record for API response
     */
    protected function formatEoiForResponse(ClientEoiReference $eoi, bool $includePassword = false): array
    {
        $data = [
            'id' => $eoi->id,
            'eoi_number' => $eoi->EOI_number,
            'eoi_subclasses' => $eoi->eoi_subclasses,
            'eoi_states' => $eoi->eoi_states,
            'formatted_subclasses' => $eoi->formatted_subclasses,
            'formatted_states' => $eoi->formatted_states,
            'occupation' => $eoi->EOI_occupation,
            'points' => $eoi->EOI_point,
            'submission_date' => $eoi->EOI_submission_date?->format('d/m/Y'),
            'invitation_date' => $eoi->eoi_invitation_date?->format('d/m/Y'),
            'nomination_date' => $eoi->eoi_nomination_date?->format('d/m/Y'),
            'roi' => $eoi->EOI_ROI,
            'status' => $eoi->eoi_status,
            'has_password' => !empty($eoi->EOI_password),
            'created_at' => $eoi->created_at?->format('d/m/Y H:i'),
            'updated_at' => $eoi->updated_at?->format('d/m/Y H:i'),
            'created_by' => $eoi->creator?->first_name . ' ' . $eoi->creator?->last_name ?? 'Unknown',
            'updated_by' => $eoi->updater?->first_name . ' ' . $eoi->updater?->last_name ?? 'Unknown',
            
            // Workflow fields
            'staff_verified' => (bool) $eoi->staff_verified,
            'verification_date' => $eoi->confirmation_date?->format('d/m/Y H:i'),
            'verified_by' => $eoi->verifier ? ($eoi->verifier->first_name . ' ' . $eoi->verifier->last_name) : null,
            'email_sent_at' => $eoi->confirmation_email_sent_at?->format('d/m/Y H:i'),
            'client_confirmation_status' => $eoi->client_confirmation_status,
            'client_confirmation_date' => $eoi->client_last_confirmation?->format('d/m/Y H:i'),
            'client_notes' => $eoi->client_confirmation_notes,
            'client_email' => $eoi->client?->email,
        ];

        // Only include password if explicitly requested (never in list views)
        if ($includePassword && !empty($eoi->EOI_password)) {
            $data['password_encrypted'] = true;
        }

        return $data;
    }

    /**
     * Normalize date from dd/mm/yyyy or other formats to Y-m-d
     */
    protected function normalizeDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Try dd/mm/yyyy format first
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $date, $matches)) {
                return sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
            }

            // Try Carbon parsing as fallback
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Failed to normalize date', ['date' => $date, 'error' => $e->getMessage()]);
            return null;
        }
    }
}
