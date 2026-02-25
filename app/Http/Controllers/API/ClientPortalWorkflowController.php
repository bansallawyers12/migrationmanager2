<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Events\NotificationCountUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ClientPortalWorkflowController extends Controller
{
    /**
     * List of Workflow Stages with Active Stage
     * GET /api/workflow/stages
     * 
     * Returns all workflow stages with the current active stage for the client
     */
    public function getWorkflowStages(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;
            
            // Get client_matter_id parameter (optional)
            $clientMatterId = $request->get('client_matter_id');
            
            // Get application_id if client_matter_id is provided
            $applicationId = null;
            if (!is_null($clientMatterId)) {
                $application = DB::table('applications')
                    ->select('id as application_id')
                    ->where('client_matter_id', $clientMatterId)
                    ->where('client_id', $clientId)
                    ->first();
                
                if ($application) {
                    $applicationId = $application->application_id;
                }
            }

            // Get all workflow stages (ordered by sort_order, then id)
            $workflowStages = DB::table('workflow_stages')
                ->orderByRaw('COALESCE(sort_order, id) ASC')
                ->select(
                    'id',
                    'name',
                    'created_at',
                    'updated_at'
                )
                ->get()
                ->map(function ($stage) use ($clientId, $applicationId) {
                    // Calculate allowed_checklist_count and get allowed_checklist items for this stage
                    $allowedChecklistCount = 0;
                    $allowedChecklist = [];
                    
                    if (!is_null($applicationId)) {
                        $checklistItems = DB::table('application_document_lists')
                            ->where('application_id', $applicationId)
                            ->where('client_id', $clientId)
                            ->where('typename', $stage->name)
                            ->where('allow_client', 1)
                            ->select('id', 'document_type')
                            ->orderBy('id', 'asc')
                            ->get();
                        
                        $allowedChecklistCount = $checklistItems->count();
                        
                        // Format checklist items as array with id and name
                        $allowedChecklist = $checklistItems->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->document_type
                            ];
                        })->toArray();
                    }
                    
                    return [
                        'id' => $stage->id,
                        'name' => $stage->name,
                        'stage_name' => $stage->name, // Alias for consistency
                        'allowed_checklist_count' => $allowedChecklistCount,
                        'allowed_checklist' => $allowedChecklist,
                        'created_at' => $stage->created_at,
                        'updated_at' => $stage->updated_at
                    ];
                });

            // Get active stage for the client
            $activeStage = null;
            $activeStageInfo = null;
            
            if (!is_null($clientMatterId)) {
                // Get active stage for specific matter
                $activeStageInfo = DB::table('client_matters')
                    ->join('workflow_stages', 'client_matters.workflow_stage_id', '=', 'workflow_stages.id')
                    ->select(
                        'client_matters.workflow_stage_id',
                        'workflow_stages.name as stage_name',
                        'client_matters.client_unique_matter_no',
                        'client_matters.matter_status',
                        'client_matters.updated_at as stage_updated_at'
                    )
                    ->where('client_matters.client_id', $clientId)
                    ->where('client_matters.id', $clientMatterId)
                    ->where('client_matters.matter_status', 1) // Active matter
                    ->first();
            } else {
                // Get active stage for the most recent active matter
                $activeStageInfo = DB::table('client_matters')
                    ->join('workflow_stages', 'client_matters.workflow_stage_id', '=', 'workflow_stages.id')
                    ->select(
                        'client_matters.workflow_stage_id',
                        'workflow_stages.name as stage_name',
                        'client_matters.client_unique_matter_no',
                        'client_matters.matter_status',
                        'client_matters.updated_at as stage_updated_at'
                    )
                    ->where('client_matters.client_id', $clientId)
                    ->where('client_matters.matter_status', 1) // Active matter
                    ->orderBy('client_matters.updated_at', 'desc')
                    ->first();
            }

            if ($activeStageInfo) {
                $activeStage = [
                    'id' => $activeStageInfo->workflow_stage_id,
                    'name' => $activeStageInfo->stage_name,
                    'stage_name' => $activeStageInfo->stage_name,
                    'client_matter_no' => $activeStageInfo->client_unique_matter_no,
                    'matter_status' => $activeStageInfo->matter_status,
                    'stage_updated_at' => $activeStageInfo->stage_updated_at,
                    'is_active' => true
                ];
            }

            // Mark active stage in the workflow stages list
            $workflowStagesWithActive = $workflowStages->map(function ($stage) use ($activeStage) {
                $stage['is_active'] = ($activeStage && $stage['id'] == $activeStage['id']);
                $stage['is_current_stage'] = ($activeStage && $stage['id'] == $activeStage['id']);
                return $stage;
            });

            // Get case summary when client_matter_id is provided
            $caseSummary = null;
            if (!is_null($clientMatterId)) {
                $caseSummary = $this->getCaseSummaryForClientMatter($clientId, $clientMatterId);
            }

            $responseData = [
                'workflow_stages' => $workflowStagesWithActive,
                'total_stages' => $workflowStages->count(),
                'active_stage' => $activeStage,
                'has_active_stage' => !is_null($activeStage),
                'client_id' => $clientId,
                'client_matter_id' => $clientMatterId
            ];

            if (!is_null($caseSummary)) {
                $responseData['case_summary'] = $caseSummary;
            }

            return response()->json([
                'success' => true,
                'data' => $responseData
            ], 200);

        } catch (\Exception $e) {
            Log::error('Workflow Stages API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'client_matter_id' => $clientMatterId ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch workflow stages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get case summary for a client matter
     * Returns: case_name, case_status, migration_agent, person_responsible, person_assisting
     */
    private function getCaseSummaryForClientMatter($clientId, $clientMatterId)
    {
        $matter = DB::table('client_matters')
            ->leftJoin('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
            ->leftJoin('staff as migration_agent', 'client_matters.sel_migration_agent', '=', 'migration_agent.id')
            ->leftJoin('staff as person_responsible', 'client_matters.sel_person_responsible', '=', 'person_responsible.id')
            ->leftJoin('staff as person_assisting', 'client_matters.sel_person_assisting', '=', 'person_assisting.id')
            ->where('client_matters.client_id', $clientId)
            ->where('client_matters.id', $clientMatterId)
            ->select(
                'client_matters.client_unique_matter_no',
                'client_matters.matter_status',
                'client_matters.sel_matter_id',
                'matters.title as matter_title',
                'matters.nick_name as matter_nick_name',
                'migration_agent.first_name as migration_agent_first_name',
                'migration_agent.last_name as migration_agent_last_name',
                'person_responsible.first_name as person_responsible_first_name',
                'person_responsible.last_name as person_responsible_last_name',
                'person_assisting.first_name as person_assisting_first_name',
                'person_assisting.last_name as person_assisting_last_name'
            )
            ->first();

        if (!$matter) {
            return null;
        }

        // Build case_name: "nick_name - title (client_unique_matter_no)" e.g. "020 - Bridging (Class B) (BA_2)"
        $caseName = 'General Matter';
        if ($matter->sel_matter_id != 1 && !empty($matter->matter_title)) {
            if (!empty($matter->matter_nick_name)) {
                $caseName = $matter->matter_nick_name . ' - ' . $matter->matter_title;
            } else {
                $caseName = $matter->matter_title;
            }
        }
        if (!empty($matter->client_unique_matter_no)) {
            $caseName .= ' (' . $matter->client_unique_matter_no . ')';
        }

        return [
            'case_name' => $caseName,
            'case_status' => $matter->matter_status == 1 ? 'Active' : 'Inactive',
            'migration_agent' => trim(($matter->migration_agent_first_name ?? '') . ' ' . ($matter->migration_agent_last_name ?? '')) ?: 'Unassigned',
            'person_responsible' => trim(($matter->person_responsible_first_name ?? '') . ' ' . ($matter->person_responsible_last_name ?? '')) ?: 'Unassigned',
            'person_assisting' => trim(($matter->person_assisting_first_name ?? '') . ' ' . ($matter->person_assisting_last_name ?? '')) ?: 'Unassigned'
        ];
    }

    /**
     * Get Workflow Stage Details
     * GET /api/workflow/stages/{stage_id}
     * 
     * Returns details of a specific workflow stage
     */
    public function getWorkflowStageDetails(Request $request, $stageId)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            // Validate stage_id
            if (!is_numeric($stageId) || $stageId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid stage ID'
                ], 422);
            }

            // Get workflow stage details
            $stage = DB::table('workflow_stages')
                ->where('id', $stageId)
                ->select(
                    'id',
                    'name',
                    'created_at',
                    'updated_at'
                )
                ->first();

            if (!$stage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Workflow stage not found'
                ], 404);
            }

            // Check if this stage is currently active for the client
            $isActive = DB::table('client_matters')
                ->where('client_id', $clientId)
                ->where('workflow_stage_id', $stageId)
                ->where('matter_status', 1) // Active matter
                ->exists();

            // Get client matters in this stage
            $clientMattersInStage = DB::table('client_matters')
                ->join('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                ->select(
                    'client_matters.id',
                    'client_matters.client_unique_matter_no',
                    'client_matters.matter_status',
                    'matters.title as matter_title',
                    'client_matters.updated_at'
                )
                ->where('client_matters.client_id', $clientId)
                ->where('client_matters.workflow_stage_id', $stageId)
                ->orderBy('client_matters.updated_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'stage' => [
                        'id' => $stage->id,
                        'name' => $stage->name,
                        'stage_name' => $stage->name,
                        'is_active' => $isActive,
                        'created_at' => $stage->created_at,
                        'updated_at' => $stage->updated_at
                    ],
                    'client_matters_in_stage' => $clientMattersInStage,
                    'total_matters_in_stage' => $clientMattersInStage->count(),
                    'active_matters_in_stage' => $clientMattersInStage->where('matter_status', 1)->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Workflow Stage Details API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'stage_id' => $stageId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch workflow stage details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    

    /**
     * Get Allowed Checklist for Stages
     * GET /api/workflow/allowed-checklist
     * 
     * Returns allowed checklist names for a specific client matter
     */
    public function allowedChecklistForStages(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;
            
            // Validate input parameters
            $validator = Validator::make($request->all(), [
                'client_matter_id' => 'required|integer|min:1',
                'stage_id' => 'nullable|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $clientMatterId = $request->input('client_matter_id');
            $stageId = $request->input('stage_id');

            // Get stage name if stage_id is provided
            $stageName = null;
            if (!empty($stageId)) {
                $stage = DB::table('workflow_stages')
                    ->where('id', $stageId)
                    ->select('name')
                    ->first();
                
                if ($stage) {
                    $stageName = $stage->name;
                }
            }

            // Step 1: Get application_id from applications table
            $application = DB::table('applications')
                ->select('id as application_id', 'client_matter_id', 'client_id', 'stage', 'status')
                ->where('client_matter_id', $clientMatterId)
                ->where('client_id', $clientId)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'No application found for the specified client matter',
                    'data' => [
                        'client_matter_id' => $clientMatterId,
                        'client_id' => $clientId
                    ]
                ], 404);
            }

            $applicationId = $application->application_id;

            // Step 2: Get allowed checklist names from application_document_lists table
            // Join with workflow_stages table to get type_id based on typename
            // Join with application_documents - only the LATEST document per checklist to avoid duplicate rows
            // (multiple uploads per checklist would otherwise create duplicate checklist entries)
            $latestDocSubquery = DB::table('application_documents')
                ->select('list_id', DB::raw('MAX(id) as latest_doc_id'))
                ->where('application_id', $applicationId)
                ->groupBy('list_id');

            $query = DB::table('application_document_lists')
                ->leftJoin('workflow_stages', 'application_document_lists.typename', '=', 'workflow_stages.name')
                ->leftJoinSub($latestDocSubquery, 'latest_docs', 'application_document_lists.id', '=', 'latest_docs.list_id')
                ->leftJoin('application_documents as ad', function($join) use ($applicationId) {
                    $join->on('ad.list_id', '=', 'application_document_lists.id')
                         ->on('ad.id', '=', 'latest_docs.latest_doc_id')
                         ->where('ad.application_id', '=', $applicationId);
                })
                ->select(
                    'application_document_lists.id',
                    'application_document_lists.document_type',
                    'application_document_lists.description',
                    'application_document_lists.type',
                    'application_document_lists.typename',
                    'application_document_lists.make_mandatory',
                    'application_document_lists.date',
                    'application_document_lists.time',
                    'application_document_lists.created_at',
                    'application_document_lists.updated_at',
                    'workflow_stages.id as type_id',
                    'ad.file_name',
                    'ad.myfile as file_url',
                    'ad.status as doc_status',
                    'ad.doc_rejection_reason'
                )
                ->where('application_document_lists.application_id', $applicationId)
                ->where('application_document_lists.allow_client', 1); // Only documents allowed for client
            
            // Filter by stage name (typename) if stage_id is provided
            if (!empty($stageName)) {
                $query->where('application_document_lists.typename', $stageName);
            }
            
            $allowedChecklists = $query
                ->orderBy('application_document_lists.id', 'asc')
                ->get()
                ->map(function ($item) {
                    $isUploaded = !is_null($item->file_name) && !is_null($item->file_url);
                    
                    // Set doc_status_id, doc_status_text and doc_rejection_reason
                    // If no document is uploaded, all should be null
                    $docStatusId = null;
                    $docStatusText = null;
                    $docRejectionReason = null;
                    
                    if ($isUploaded && !is_null($item->doc_status)) {
                        $docStatusId = (int)$item->doc_status; // 0, 1, or 2
                        
                        // Map status ID to status text
                        switch ($docStatusId) {
                            case 0:
                                $docStatusText = 'InProgress';
                                break;
                            case 1:
                                $docStatusText = 'Approved';
                                break;
                            case 2:
                                $docStatusText = 'Rejected';
                                // doc_rejection_reason only if status is 2 (Reject)
                                if (!is_null($item->doc_rejection_reason)) {
                                    $docRejectionReason = $item->doc_rejection_reason;
                                }
                                break;
                            default:
                                $docStatusText = null;
                        }
                    }
                    
                    return [
                        'id' => $item->id,
                        'checklist_name' => $item->document_type,
                        'document_type' => $item->document_type,
                        'description' => $item->description,
                        'type' => $item->type,
                        'type_id' => $item->type_id,
                        'type_name' => $item->typename,
                        'is_mandatory' => $item->make_mandatory == 1,
                        'due_date' => $item->date,
                        'due_time' => $item->time,
                        'is_upload' => $isUploaded,
                        'file_name' => $isUploaded ? $item->file_name : null,
                        'file_url' => $isUploaded ? $item->file_url : null,
                        'doc_status_id' => $docStatusId,
                        'doc_status_text' => $docStatusText,
                        'doc_rejection_reason' => $docRejectionReason,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'application_info' => [
                        'application_id' => $application->application_id,
                        'client_matter_id' => $application->client_matter_id,
                        'client_id' => $application->client_id,
                        'current_stage' => $application->stage,
                        'status' => $application->status
                    ],
                    'allowed_checklists' => $allowedChecklists,
                    'total_allowed_checklists' => $allowedChecklists->count(),
                    'mandatory_checklists' => $allowedChecklists->where('is_mandatory', true)->count(),
                    'optional_checklists' => $allowedChecklists->where('is_mandatory', false)->count(),
                    'client_matter_id' => $clientMatterId,
                    'stage_id' => $stageId ? (int)$stageId : null,
                    'stage_name' => $stageName
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Allowed Checklist for Stages API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'client_matter_id' => $request->input('client_matter_id'),
                'stage_id' => $request->input('stage_id'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch allowed checklist for stages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to format file size
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Upload Allowed Checklist Document - Single Upload
     * POST /api/workflow/upload-allowed-checklist
     *
     * Uploads a single document. Params: client_matter_id, file, allowed_checklist_id
     */
    public function uploadAllowedChecklistDocument(Request $request)
    {
        return $this->processChecklistDocumentUpload($request, $singleOnly = true);
    }

    /**
     * Upload Allowed Checklist Document - Bulk Upload
     * POST /api/workflow/upload-allowed-checklist-bulk-upload
     *
     * Uploads multiple documents. Params: client_matter_id, files[], allowed_checklist_ids (comma-separated e.g. "41,42,43").
     * Number of files must equal number of checklist IDs.
     */
    public function uploadAllowedChecklistDocumentBulk(Request $request)
    {
        return $this->processChecklistDocumentUpload($request, $singleOnly = false);
    }

    /**
     * Shared logic for checklist document upload (single and bulk)
     */
    private function processChecklistDocumentUpload(Request $request, bool $singleOnly)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            $clientMatterId = $request->input('client_matter_id');
            $files = [];
            $allowedChecklistIds = [];

            if ($singleOnly) {
                // Single upload: file + allowed_checklist_id
                $validator = Validator::make($request->all(), [
                    'client_matter_id' => 'required|integer|min:1',
                    'allowed_checklist_id' => 'required|integer|min:1',
                    'file' => 'required|file|max:10240'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $files = [$request->file('file')];
                $allowedChecklistIds = [(int) $request->input('allowed_checklist_id')];
            } else {
                // Bulk upload: files[] + allowed_checklist_ids (comma-separated or array)
                $validator = Validator::make($request->all(), [
                    'client_matter_id' => 'required|integer|min:1',
                    'files' => 'required|array',
                    'files.*' => 'required|file|max:10240',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $files = array_values($request->file('files'));

                // Single parameter: allowed_checklist_ids as comma-separated (e.g. "41,42,43")
                $allowedChecklistIdsInput = $request->input('allowed_checklist_ids') ?? $request->input('allowed_checklist_ids[]');
                if (is_string($allowedChecklistIdsInput)) {
                    $allowedChecklistIds = array_values(array_map(function ($id) {
                        return (int) trim($id);
                    }, array_filter(explode(',', $allowedChecklistIdsInput), function ($id) {
                        return trim($id) !== '';
                    })));
                } elseif (is_array($allowedChecklistIdsInput)) {
                    $allowedChecklistIds = array_values(array_map('intval', $allowedChecklistIdsInput));
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'allowed_checklist_ids is required. Use comma-separated values (e.g. "41,42,43"). Number of IDs must match number of files.'
                    ], 422);
                }

                if (count(array_filter($allowedChecklistIds, fn($id) => $id < 1)) > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Each allowed_checklist_id must be a positive integer'
                    ], 422);
                }

                if (count($files) !== count($allowedChecklistIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Number of files must match number of allowed_checklist_ids. You uploaded ' . count($files) . ' file(s), but provided ' . count($allowedChecklistIds) . ' checklist ID(s). Use comma-separated format (e.g. "41,42,43").'
                    ], 422);
                }

                if (count($files) > 20) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maximum 20 files allowed per request'
                    ], 422);
                }
            }

            // Get client information
            $adminInfo = DB::table('admins')
                ->select('client_id', 'first_name')
                ->where('id', $clientId)
                ->first();

            if (!$adminInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found'
                ], 404);
            }

            $clientUniqueId = $adminInfo->client_id;
            $clientFirstName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $adminInfo->first_name);

            // Get application_id from applications table
            $application = DB::table('applications')
                ->select('id as application_id', 'client_matter_id', 'client_id', 'stage')
                ->where('client_matter_id', $clientMatterId)
                ->where('client_id', $clientId)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'No application found for the specified client matter'
                ], 404);
            }

            $applicationId = $application->application_id;

            // Get client matter for notifications (once per batch)
            $clientMatter = DB::table('client_matters')
                ->leftJoin('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                ->where('client_matters.id', $clientMatterId)
                ->select(
                    'client_matters.client_id',
                    'client_matters.client_unique_matter_no',
                    'client_matters.sel_migration_agent',
                    'matters.title as matter_title',
                    'matters.nick_name as matter_nick_name'
                )
                ->first();

            $uploadedDocuments = [];
            $errors = [];

            foreach ($files as $index => $file) {
                $allowedChecklistId = $allowedChecklistIds[$index] ?? null;

                if (!$allowedChecklistId) {
                    $errors[] = ['index' => $index, 'file' => $file->getClientOriginalName(), 'message' => 'Missing allowed_checklist_id'];
                    continue;
                }

                // Verify the allowed checklist exists and is allowed for client
                $allowedChecklist = DB::table('application_document_lists')
                    ->where('id', $allowedChecklistId)
                    ->where('application_id', $applicationId)
                    ->where('allow_client', 1)
                    ->first();

                if (!$allowedChecklist) {
                    $errors[] = ['index' => $index, 'file' => $file->getClientOriginalName(), 'message' => 'Allowed checklist not found or not permitted for client'];
                    continue;
                }

                // Validate file name
                $fileName = $file->getClientOriginalName();
                if (!preg_match('/^[a-zA-Z0-9_\-\.\s\$]+$/', $fileName)) {
                    $errors[] = ['index' => $index, 'file' => $fileName, 'message' => 'File name can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), and dollar signs ($). Please rename the file.'];
                    continue;
                }

                // Get file details
                $fileSize = $file->getSize();
                $extension = $file->getClientOriginalExtension();
                $checklistName = $allowedChecklist->document_type;

                // Build new file name with microtime to avoid collisions in bulk upload
                $timestamp = time() . '_' . $index;
                $newFileName = $clientFirstName . "_" . $checklistName . "_" . $timestamp . "." . $extension;

                // Build file path for AWS S3
                $filePath = 'application_documents/' . $clientUniqueId . '/' . $newFileName;

                try {
                    // Upload to AWS S3
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
                    $fileUrl = Storage::disk('s3')->url($filePath);
                } catch (\Exception $e) {
                    $errors[] = ['index' => $index, 'file' => $fileName, 'message' => 'S3 upload failed: ' . $e->getMessage()];
                    continue;
                }

                // Prepare data for insertion into application_documents table
                $documentData = [
                    'type' => $allowedChecklist->type,
                    'list_id' => $allowedChecklistId,
                    'user_id' => $clientId,
                    'file_name' => $clientFirstName . "_" . $checklistName . "_" . $timestamp,
                    'file_type' => $extension,
                    'myfile' => $fileUrl,
                    'myfile_key' => $newFileName,
                    'file_size' => $fileSize,
                    'application_id' => $applicationId,
                    'status' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'typename' => $allowedChecklist->typename
                ];

                $documentId = DB::table('application_documents')->insertGetId($documentData);

                if (!$documentId) {
                    $errors[] = ['index' => $index, 'file' => $fileName, 'message' => 'Failed to save document record'];
                    continue;
                }

                $uploadedDocuments[] = [
                    'document_id' => $documentId,
                    'application_id' => $applicationId,
                    'client_matter_id' => $clientMatterId,
                    'allowed_checklist_id' => $allowedChecklistId,
                    'checklist_name' => $checklistName,
                    'file_name' => $documentData['file_name'],
                    'file_type' => $extension,
                    'file_size' => $fileSize,
                    'file_size_formatted' => $this->formatFileSize($fileSize),
                    'file_url' => $fileUrl,
                    'file_key' => $newFileName,
                    's3_path' => $filePath,
                    'uploaded_at' => now()->toISOString()
                ];

                // Update application timestamp
                DB::table('applications')
                    ->where('id', $applicationId)
                    ->update(['updated_at' => now()]);

                // Send notifications for each uploaded document
                if ($clientMatter) {
                    $matterName = 'Matter';
                    if (!empty($clientMatter->matter_title) || !empty($clientMatter->matter_nick_name)) {
                        $matterName = trim(($clientMatter->matter_nick_name ?? '') . ' - ' . ($clientMatter->matter_title ?? ''));
                        if (empty(trim($matterName, ' -'))) {
                            $matterName = $clientMatter->matter_title ?? $clientMatter->matter_nick_name ?? 'Matter';
                        }
                    }
                    if (!empty($clientMatter->client_unique_matter_no)) {
                        $matterName .= ' (' . $clientMatter->client_unique_matter_no . ')';
                    }

                    $notificationMessage = 'Client has uploaded document in this checklist - ' . $checklistName . ' of this matter - ' . $matterName . '. You can review and approve/reject this.';

                    $encodedClientId = base64_encode(convert_uuencode($clientMatter->client_id));
                    $matterRef = $clientMatter->client_unique_matter_no ?? '';
                    $notificationUrl = $matterRef
                        ? url('/clients/detail/' . $encodedClientId . '/' . $matterRef . '/checklists')
                        : url('/clients/detail/' . $encodedClientId . '/checklists');

                    $recipientIds = collect();
                    $superAdminIds = Staff::where('role', 1)->where('status', 1)->pluck('id');
                    $recipientIds = $recipientIds->merge($superAdminIds);
                    if (!empty($clientMatter->sel_migration_agent)) {
                        $recipientIds = $recipientIds->push($clientMatter->sel_migration_agent);
                    }
                    $recipientIds = $recipientIds->unique()->filter()->values();

                    foreach ($recipientIds as $receiverStaffId) {
                        if (!$receiverStaffId || !Staff::where('id', $receiverStaffId)->exists()) {
                            continue;
                        }

                        DB::table('notifications')->insert([
                            'sender_id' => $clientId,
                            'receiver_id' => $receiverStaffId,
                            'module_id' => $clientMatterId,
                            'url' => $notificationUrl,
                            'notification_type' => 'checklist',
                            'message' => $notificationMessage,
                            'receiver_status' => 0,
                            'sender_status' => 1,
                            'seen' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        try {
                            $unreadCount = DB::table('notifications')
                                ->where('receiver_id', $receiverStaffId)
                                ->where('receiver_status', 0)
                                ->count();
                            broadcast(new NotificationCountUpdated($receiverStaffId, $unreadCount, $notificationMessage, $notificationUrl));
                        } catch (\Exception $e) {
                            Log::warning('Failed to broadcast notification count for checklist upload', [
                                'receiver_id' => $receiverStaffId,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            }

            // Build response
            if (empty($uploadedDocuments)) {
                return response()->json([
                    'success' => false,
                    'message' => count($errors) > 0 ? 'All uploads failed' : 'No documents to upload',
                    'errors' => $errors
                ], 422);
            }

            $totalCount = count($files);
            $uploadedCount = count($uploadedDocuments);
            $failedCount = count($errors);

            // Single upload response format
            if ($singleOnly && $uploadedCount === 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Allowed checklist document uploaded successfully',
                    'data' => $uploadedDocuments[0]
                ], 201);
            }

            // Bulk upload response format
            return response()->json([
                'success' => true,
                'message' => $uploadedCount . ' document(s) uploaded successfully' . ($failedCount > 0 ? ', ' . $failedCount . ' failed' : ''),
                'data' => [
                    'documents' => $uploadedDocuments,
                    'uploaded_count' => $uploadedCount,
                    'failed_count' => $failedCount,
                    'total_count' => $totalCount,
                    'application_id' => $applicationId,
                    'client_matter_id' => $clientMatterId,
                ],
                'errors' => $failedCount > 0 ? $errors : null
            ], 201);

        } catch (\Exception $e) {
            Log::error('Upload Allowed Checklist Document API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'client_matter_id' => $request->input('client_matter_id'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload allowed checklist document',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
