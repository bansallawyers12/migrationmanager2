<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ClientEoiReference;
use App\Services\PointsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

            $eoiReference->delete();

            // Clear points cache
            $this->pointsService->clearCache($client->id);

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
            $client->qualifications = \App\Models\ClientQualification::where('client_id', $client->id)->orderByRaw('finish_date IS NULL, finish_date DESC')->get();
            $client->experiences = \App\Models\ClientExperience::where('client_id', $client->id)->orderByRaw('job_finish_date IS NULL, job_finish_date DESC')->get();
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
