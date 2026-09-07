<?php

use App\Support\WorkflowPortalTasklistDefaults;
use App\Support\WorkflowStageChecklistSync;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('workflow_stage_portal_tasklists') || ! Schema::hasTable('workflow_stages')) {
            return;
        }

        $workflowId = WorkflowPortalTasklistDefaults::WORKFLOW_ID;
        $workflowExists = DB::table('workflows')->where('id', $workflowId)->exists();
        if (! $workflowExists) {
            return;
        }

        $now = now();
        $stages = DB::table('workflow_stages')
            ->where('workflow_id', $workflowId)
            ->get(['id', 'name']);

        $stagesByName = [];
        foreach ($stages as $stage) {
            $stagesByName[strtolower(trim((string) $stage->name))] = $stage;
        }

        foreach (WorkflowPortalTasklistDefaults::byStageName($workflowId) as $stageName => $tasks) {
            $stage = $stagesByName[strtolower(trim($stageName))] ?? null;
            if (! $stage) {
                continue;
            }

            $existingNames = [];
            foreach (DB::table('workflow_stage_portal_tasklists')
                ->where('workflow_id', $workflowId)
                ->where('workflow_stage_id', $stage->id)
                ->get(['name']) as $row) {
                $existingNames[strtolower(trim((string) $row->name))] = true;
            }

            $sort = (int) DB::table('workflow_stage_portal_tasklists')
                ->where('workflow_stage_id', $stage->id)
                ->max('sort_order');

            foreach ($tasks as $task) {
                $name = trim((string) $task['name']);
                if ($name === '' || isset($existingNames[strtolower($name)])) {
                    continue;
                }

                $sort++;
                DB::table('workflow_stage_portal_tasklists')->insert([
                    'workflow_id' => $workflowId,
                    'workflow_stage_id' => $stage->id,
                    'name' => $name,
                    'description' => $task['description'] ?? null,
                    'task_type' => $task['task_type'],
                    'allow_client' => $task['allow_client'] ? 1 : 0,
                    'is_required' => $task['is_required'] ? 1 : 0,
                    'sort_order' => $sort,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $existingNames[strtolower($name)] = true;
            }
        }

        WorkflowStageChecklistSync::ensureSeededForWorkflow($workflowId);
    }

    public function down(): void
    {
        if (! Schema::hasTable('workflow_stage_portal_tasklists')) {
            return;
        }

        $workflowId = WorkflowPortalTasklistDefaults::WORKFLOW_ID;
        foreach (WorkflowPortalTasklistDefaults::byStageName($workflowId) as $tasks) {
            foreach ($tasks as $task) {
                DB::table('workflow_stage_portal_tasklists')
                    ->where('workflow_id', $workflowId)
                    ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim((string) $task['name']))])
                    ->delete();
            }
        }
    }
};
