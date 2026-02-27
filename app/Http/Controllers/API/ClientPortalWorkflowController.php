<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            
            // Use client_matter_id for document checklists (migrated from applications table)
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
                ->map(function ($stage) use ($clientId, $clientMatterId) {
                    // Calculate allowed_checklist_count and get allowed_checklist items for this stage
                    $allowedChecklistCount = 0;
                    $allowedChecklist = [];
                    
                    if (!is_null($clientMatterId)) {
                        $checklistItems = DB::table('cp_doc_checklists')
                            ->where('client_matter_id', $clientMatterId)
                            ->where('client_id', $clientId)
                            ->where('wf_stage', $stage->name)
                            ->where('allow_client', 1)
                            ->select('id', 'cp_checklist_name')
                            ->orderBy('id', 'asc')
                            ->get();
                        
                        $allowedChecklistCount = $checklistItems->count();
                        
                        // Format checklist items as array with id and name
                        $allowedChecklist = $checklistItems->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->cp_checklist_name
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

    // allowedChecklistForStages REMOVED - GET /api/workflow/allowed-checklist unused (no consumer; Documents tab removed)
    // formatFileSize, uploadAllowedChecklistDocument, uploadAllowedChecklistDocumentBulk, processChecklistDocumentUpload REMOVED - workflow checklist upload unused
}
