<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
            
            // Get all workflow stages
            $workflowStages = DB::table('workflow_stages')
                ->orderBy('id', 'asc')
                ->select(
                    'id',
                    'name',
                    'created_at',
                    'updated_at'
                )
                ->get()
                ->map(function ($stage) {
                    return [
                        'id' => $stage->id,
                        'name' => $stage->name,
                        'stage_name' => $stage->name, // Alias for consistency
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

            return response()->json([
                'success' => true,
                'data' => [
                    'workflow_stages' => $workflowStagesWithActive,
                    'total_stages' => $workflowStages->count(),
                    'active_stage' => $activeStage,
                    'has_active_stage' => !is_null($activeStage),
                    'client_id' => $clientId,
                    'client_matter_id' => $clientMatterId
                ]
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
                'client_matter_id' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $clientMatterId = $request->input('client_matter_id');

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
            // Join with application_documents table to check upload status
            $allowedChecklists = DB::table('application_document_lists')
                ->leftJoin('workflow_stages', 'application_document_lists.typename', '=', 'workflow_stages.name')
                ->leftJoin('application_documents', function($join) use ($applicationId) {
                    $join->on('application_document_lists.id', '=', 'application_documents.list_id')
                         ->where('application_documents.application_id', '=', $applicationId);
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
                    'application_documents.file_name',
                    'application_documents.myfile as file_url'
                )
                ->where('application_document_lists.application_id', $applicationId)
                ->where('application_document_lists.allow_client', 1) // Only documents allowed for client
                ->orderBy('application_document_lists.id', 'asc')
                ->get()
                ->map(function ($item) {
                    $isUploaded = !is_null($item->file_name) && !is_null($item->file_url);
                    
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
                    'client_matter_id' => $clientMatterId
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Allowed Checklist for Stages API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'client_matter_id' => $request->input('client_matter_id'),
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
     * Upload Allowed Checklist Document
     * POST /api/workflow/upload-allowed-checklist
     * 
     * Uploads a document for an allowed checklist item
     */
    public function uploadAllowedChecklistDocument(Request $request)
    {
        try {
            $admin = $request->user();
            $clientId = $admin->id;

            // Validate request
            $validator = Validator::make($request->all(), [
                'client_matter_id' => 'required|integer|min:1',
                'allowed_checklist_id' => 'required|integer|min:1',
                'file' => 'required|file|max:10240' // 10MB max file size, single file only
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Additional validation: Ensure only one file is uploaded
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'File is required and must be uploaded'
                ], 422);
            }

            // Check if multiple files are uploaded (should not happen with single file input)
            if (is_array($request->file('file'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only one file can be uploaded at a time'
                ], 422);
            }

            $clientMatterId = $request->input('client_matter_id');
            $allowedChecklistId = $request->input('allowed_checklist_id');
            $file = $request->file('file');

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

            // Step 1: Get application_id from applications table
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

            // Step 2: Verify the allowed checklist exists and is allowed for client
            $allowedChecklist = DB::table('application_document_lists')
                ->where('id', $allowedChecklistId)
                ->where('application_id', $applicationId)
                ->where('allow_client', 1)
                ->first();

            if (!$allowedChecklist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Allowed checklist not found or not permitted for client'
                ], 404);
            }

            // Validate file name
            $fileName = $file->getClientOriginalName();
            if (!preg_match('/^[a-zA-Z0-9_\-\.\s\$]+$/', $fileName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File name can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), and dollar signs ($). Please rename the file and try again.'
                ], 422);
            }

            // Get file details
            $fileSize = $file->getSize();
            $extension = $file->getClientOriginalExtension();
            $checklistName = $allowedChecklist->document_type;

            // Build new file name: firstname_checklist_timestamp.ext
            $newFileName = $clientFirstName . "_" . $checklistName . "_" . time() . "." . $extension;

            // Build file path for AWS S3: application_documents/client_unique_id/filename
            $filePath = 'application_documents/' . $clientUniqueId . '/' . $newFileName;

            // Upload to AWS S3
            Storage::disk('s3')->put($filePath, file_get_contents($file));

            // Get file URL
            $fileUrl = Storage::disk('s3')->url($filePath);

            // Prepare data for insertion into application_documents table
            $documentData = [
                'type' => $allowedChecklist->type,
                'list_id' => $allowedChecklistId,
                'user_id' => $clientId,
                'file_name' => $clientFirstName . "_" . $checklistName . "_" . time(),
                'file_type' => $extension,
                'myfile' => $fileUrl,
                'myfile_key' => $newFileName,
                'file_size' => $fileSize,
                'application_id' => $applicationId,
                'status' => 0, // Default status
                'created_at' => now(),
                'updated_at' => now(),
                'typename' => $allowedChecklist->typename
            ];

            // Insert the document record
            $documentId = DB::table('application_documents')->insertGetId($documentData);

            if ($documentId) {
                // Create activity log
                /*$activitySubject = 'uploaded allowed checklist document';
                DB::table('activities_logs')->insert([
                    'client_id' => $clientId,
                    'created_by' => $clientId,
                    'subject' => $activitySubject,
                    'description' => 'Allowed checklist document uploaded via client portal: ' . $checklistName,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);*/

                // Update application timestamp
                DB::table('applications')
                    ->where('id', $applicationId)
                    ->update(['updated_at' => now()]);

                return response()->json([
                    'success' => true,
                    'message' => 'Allowed checklist document uploaded successfully',
                    'data' => [
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
                    ]
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save document record'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Upload Allowed Checklist Document API Error: ' . $e->getMessage(), [
                'user_id' => $admin->id ?? null,
                'client_matter_id' => $request->input('client_matter_id'),
                'allowed_checklist_id' => $request->input('allowed_checklist_id'),
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
