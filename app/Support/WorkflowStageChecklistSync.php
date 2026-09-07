<?php

namespace App\Support;

use App\Enums\ChecklistSource;
use App\Models\ClientMatter;
use App\Models\WorkflowStage;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds cp_doc_checklists on a matter from workflow_stage_checklists
 * and workflow_stage_portal_tasklists templates.
 * Idempotent: skips items that already exist (same matter + stage + name).
 */
class WorkflowStageChecklistSync
{
    public static function ensureSeededForMatter($matter): void
    {
        if (! Schema::hasTable('cp_doc_checklists')) {
            return;
        }

        if ($matter instanceof ClientMatter) {
            $clientMatter = $matter;
        } elseif (is_numeric($matter)) {
            $clientMatter = ClientMatter::find((int) $matter);
        } else {
            return;
        }

        if (! $clientMatter || empty($clientMatter->workflow_id) || empty($clientMatter->id)) {
            return;
        }

        if (Schema::hasTable('workflow_stage_checklists')) {
            $templates = DB::table('workflow_stage_checklists')
                ->where('workflow_id', $clientMatter->workflow_id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            if ($templates->isNotEmpty()) {
                $stageIds = $templates->pluck('workflow_stage_id')->unique()->filter()->values()->all();
                $stagesById = WorkflowStage::whereIn('id', $stageIds)->get()->keyBy('id');
                $now = now();

                foreach ($templates as $template) {
                    $stage = $stagesById->get($template->workflow_stage_id);
                    if (! $stage || empty($stage->name)) {
                        continue;
                    }

                    $normalizedName = strtolower(trim((string) $template->name));
                    if ($normalizedName === '') {
                        continue;
                    }

                    $exists = DB::table('cp_doc_checklists')
                        ->where('client_matter_id', $clientMatter->id)
                        ->where('wf_stage', $stage->name)
                        ->whereRaw('LOWER(TRIM(cp_checklist_name)) = ?', [$normalizedName])
                        ->exists();

                    if ($exists) {
                        if (Schema::hasColumn('cp_doc_checklists', 'is_required')) {
                            DB::table('cp_doc_checklists')
                                ->where('client_matter_id', $clientMatter->id)
                                ->where('wf_stage', $stage->name)
                                ->whereRaw('LOWER(TRIM(cp_checklist_name)) = ?', [$normalizedName])
                                ->update([
                                    'is_required' => (int) (bool) $template->is_required,
                                    'updated_at' => $now,
                                ]);
                        }

                        continue;
                    }

                    $payload = [
                        'user_id' => null,
                        'client_matter_id' => $clientMatter->id,
                        'client_id' => $clientMatter->client_id,
                        'wf_stage' => $stage->name,
                        'wf_stage_id' => $stage->id,
                        'cp_checklist_name' => trim($template->name),
                        'description' => $template->description,
                        'allow_client' => (int) ($template->allow_client ?? 1),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if (Schema::hasColumn('cp_doc_checklists', 'is_required')) {
                        $payload['is_required'] = (int) (bool) $template->is_required;
                    }

                    $payload = self::withSource($payload, ChecklistSource::Workflow);

                    DB::table('cp_doc_checklists')->insert($payload);
                }
            }
        }

        self::seedPortalTasklistsForMatter($clientMatter);
    }

    /**
     * Copy client-portal tasklist templates onto a matter (idempotent).
     */
    public static function seedPortalTasklistsForMatter($matter): void
    {
        if (! Schema::hasTable('workflow_stage_portal_tasklists') || ! Schema::hasTable('cp_doc_checklists')) {
            return;
        }

        if ($matter instanceof ClientMatter) {
            $clientMatter = $matter;
        } elseif (is_numeric($matter)) {
            $clientMatter = ClientMatter::find((int) $matter);
        } else {
            return;
        }

        if (! $clientMatter || empty($clientMatter->workflow_id) || empty($clientMatter->id)) {
            return;
        }

        $templates = DB::table('workflow_stage_portal_tasklists')
            ->where('workflow_id', $clientMatter->workflow_id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($templates->isEmpty()) {
            return;
        }

        $stageIds = $templates->pluck('workflow_stage_id')->unique()->filter()->values()->all();
        $stagesById = WorkflowStage::whereIn('id', $stageIds)->get()->keyBy('id');
        $now = now();
        $hasTaskType = Schema::hasColumn('cp_doc_checklists', 'task_type');
        $hasRequired = Schema::hasColumn('cp_doc_checklists', 'is_required');
        $hasSource = Schema::hasColumn('cp_doc_checklists', 'source');

        foreach ($templates as $template) {
            $stage = $stagesById->get($template->workflow_stage_id);
            if (! $stage || empty($stage->name)) {
                continue;
            }

            $normalizedName = strtolower(trim((string) $template->name));
            if ($normalizedName === '') {
                continue;
            }

            $exists = DB::table('cp_doc_checklists')
                ->where('client_matter_id', $clientMatter->id)
                ->where('wf_stage', $stage->name)
                ->whereRaw('LOWER(TRIM(cp_checklist_name)) = ?', [$normalizedName])
                ->exists();

            if ($exists) {
                $update = ['updated_at' => $now];
                if ($hasRequired) {
                    $update['is_required'] = (int) (bool) $template->is_required;
                }
                if ($hasTaskType) {
                    $update['task_type'] = (string) ($template->task_type ?: 'upload');
                }
                if ($hasSource) {
                    $update['source'] = ChecklistSource::Portal->value;
                }
                DB::table('cp_doc_checklists')
                    ->where('client_matter_id', $clientMatter->id)
                    ->where('wf_stage', $stage->name)
                    ->whereRaw('LOWER(TRIM(cp_checklist_name)) = ?', [$normalizedName])
                    ->whereNull('user_id')
                    ->update($update);

                continue;
            }

            $payload = [
                'user_id' => null,
                'client_matter_id' => $clientMatter->id,
                'client_id' => $clientMatter->client_id,
                'wf_stage' => $stage->name,
                'wf_stage_id' => $stage->id,
                'cp_checklist_name' => trim($template->name),
                'description' => $template->description,
                'allow_client' => (int) ($template->allow_client ?? 1),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($hasRequired) {
                $payload['is_required'] = (int) (bool) $template->is_required;
            }
            if ($hasTaskType) {
                $payload['task_type'] = (string) ($template->task_type ?: 'upload');
            }

            $payload = self::withSource($payload, ChecklistSource::Portal);

            DB::table('cp_doc_checklists')->insert($payload);
        }
    }

    /**
     * Workflow template names keyed by workflow_stage_id.
     *
     * @return array<int, list<string>>
     */
    public static function templateNamesByStageId(int $workflowId): array
    {
        if ($workflowId <= 0 || ! Schema::hasTable('workflow_stage_checklists')) {
            return [];
        }

        $grouped = [];
        $rows = DB::table('workflow_stage_checklists')
            ->where('workflow_id', $workflowId)
            ->get(['workflow_stage_id', 'name']);

        foreach ($rows as $row) {
            $stageId = (int) $row->workflow_stage_id;
            if ($stageId <= 0) {
                continue;
            }
            $grouped[$stageId][] = (string) $row->name;
        }

        return $grouped;
    }

    /**
     * Client portal tasklist template names keyed by workflow_stage_id.
     *
     * @return array<int, list<string>>
     */
    public static function portalTaskNamesByStageId(int $workflowId): array
    {
        if ($workflowId <= 0 || ! Schema::hasTable('workflow_stage_portal_tasklists')) {
            return [];
        }

        $grouped = [];
        $rows = DB::table('workflow_stage_portal_tasklists')
            ->where('workflow_id', $workflowId)
            ->get(['workflow_stage_id', 'name']);

        foreach ($rows as $row) {
            $stageId = (int) $row->workflow_stage_id;
            if ($stageId <= 0) {
                continue;
            }
            $grouped[$stageId][] = (string) $row->name;
        }

        return $grouped;
    }

    /**
     * Client Portal Documents and Activities list staff-added checklists only.
     * Auto-seeded template copies (null user_id + matching workflow template name) stay on the Workflow tab.
     *
     * @param  iterable<int, object>  $checklists
     * @param  list<string>  $templateNames
     * @return Collection<int, object>
     */
    public static function forPortalDocumentsTab($checklists, array $templateNames): Collection
    {
        $lookup = [];
        foreach ($templateNames as $name) {
            $normalized = strtolower(trim((string) $name));
            if ($normalized !== '') {
                $lookup[$normalized] = true;
            }
        }

        return collect($checklists)->filter(function ($item) use ($lookup) {
            $userId = $item->user_id ?? null;
            if ($userId !== null && $userId !== '') {
                return true;
            }

            $name = strtolower(trim((string) ($item->cp_checklist_name ?? '')));

            return $name === '' || ! isset($lookup[$name]);
        })->values();
    }

    /**
     * Client Portal → Documents: portal-sourced rows plus staff-added rows (user_id set).
     * Falls back to name-based hiding when the source column is absent.
     *
     * @param  iterable<int, object>  $checklists
     * @param  list<string>  $templateNames
     * @return Collection<int, object>
     */
    public static function forClientPortalDocuments($checklists, array $templateNames = [], ?bool $hasSourceColumn = null): Collection
    {
        $hasSourceColumn ??= Schema::hasColumn('cp_doc_checklists', 'source');
        if (! $hasSourceColumn) {
            return self::forPortalDocumentsTab($checklists, $templateNames);
        }

        $portalValues = ChecklistSource::portalValues();

        return collect($checklists)->filter(function ($item) use ($portalValues) {
            $source = strtolower(trim((string) ($item->source ?? '')));
            if (in_array($source, $portalValues, true)) {
                return true;
            }

            $userId = $item->user_id ?? null;

            return $userId !== null && $userId !== '';
        })->values();
    }

    /**
     * Workflow tab: workflow-sourced rows only. Portal copies stay on Client Portal → Documents.
     * Falls back to hiding portal template names when the source column is absent.
     *
     * @param  iterable<int, object>  $checklists
     * @param  list<string>  $portalTaskNames
     * @return Collection<int, object>
     */
    public static function forWorkflowTabChecklists($checklists, array $portalTaskNames = [], ?bool $hasSourceColumn = null): Collection
    {
        $hasSourceColumn ??= Schema::hasColumn('cp_doc_checklists', 'source');
        if (! $hasSourceColumn) {
            if ($portalTaskNames === []) {
                return collect($checklists)->values();
            }

            return self::forPortalDocumentsTab($checklists, $portalTaskNames);
        }

        $portalValues = ChecklistSource::portalValues();

        return collect($checklists)->filter(function ($item) use ($portalValues) {
            $source = strtolower(trim((string) ($item->source ?? ChecklistSource::Workflow->value)));

            return ! in_array($source, $portalValues, true);
        })->values();
    }

    /**
     * Stamp source=portal on existing copies of workflow_stage_portal_tasklists.
     */
    public static function backfillPortalSource(?int $clientMatterId = null): int
    {
        if (
            ! Schema::hasTable('cp_doc_checklists')
            || ! Schema::hasColumn('cp_doc_checklists', 'source')
            || ! Schema::hasTable('workflow_stage_portal_tasklists')
            || ! Schema::hasTable('workflow_stages')
            || ! Schema::hasTable('client_matters')
        ) {
            return 0;
        }

        $templates = DB::table('workflow_stage_portal_tasklists as t')
            ->join('workflow_stages as ws', 'ws.id', '=', 't.workflow_stage_id')
            ->get(['t.workflow_id', 't.name', 'ws.name as stage_name']);

        if ($templates->isEmpty()) {
            return 0;
        }

        $portal = ChecklistSource::Portal->value;
        $now = now();
        $updated = 0;

        foreach ($templates as $template) {
            $normalizedName = strtolower(trim((string) $template->name));
            if ($normalizedName === '' || empty($template->stage_name)) {
                continue;
            }

            $ids = DB::table('cp_doc_checklists as c')
                ->join('client_matters as m', 'm.id', '=', 'c.client_matter_id')
                ->where('m.workflow_id', $template->workflow_id)
                ->where('c.wf_stage', $template->stage_name)
                ->whereRaw('LOWER(TRIM(c.cp_checklist_name)) = ?', [$normalizedName])
                ->when($clientMatterId, function ($query) use ($clientMatterId) {
                    $query->where('c.client_matter_id', $clientMatterId);
                })
                ->where(function ($query) use ($portal) {
                    $query->whereNull('c.source')
                        ->orWhere('c.source', '!=', $portal);
                })
                ->pluck('c.id');

            if ($ids->isEmpty()) {
                continue;
            }

            foreach ($ids->chunk(500) as $chunk) {
                $updated += DB::table('cp_doc_checklists')
                    ->whereIn('id', $chunk->all())
                    ->update([
                        'source' => $portal,
                        'updated_at' => $now,
                    ]);
            }
        }

        return $updated;
    }

    /**
     * Where an Activities-tab row should be attributed.
     * Client uploads from the mobile app are "by Portal app" even when the row was seeded as portal.
     */
    public static function activityOrigin(object $item, bool $uploadedByClient = false): ChecklistSource
    {
        if ($uploadedByClient) {
            return ChecklistSource::PortalApp;
        }

        $raw = strtolower(trim((string) ($item->source ?? '')));
        $source = ChecklistSource::tryFrom($raw);

        return $source ?? ChecklistSource::Workflow;
    }

    /**
     * Client uploads from the mobile app are attributed as portal_app.
     */
    public static function markUploadedFromPortalApp(int $checklistId): void
    {
        if ($checklistId <= 0 || ! Schema::hasColumn('cp_doc_checklists', 'source')) {
            return;
        }

        DB::table('cp_doc_checklists')
            ->where('id', $checklistId)
            ->whereIn('source', ChecklistSource::portalValues())
            ->update([
                'source' => ChecklistSource::PortalApp->value,
                'updated_at' => now(),
            ]);
    }

    /**
     * Limit client-portal workflow APIs to portal-sourced checklists.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public static function constrainToPortalSource($query)
    {
        if (Schema::hasColumn('cp_doc_checklists', 'source')) {
            $query->whereIn('source', ChecklistSource::portalValues());
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private static function withSource(array $payload, ChecklistSource $source): array
    {
        if (Schema::hasColumn('cp_doc_checklists', 'source')) {
            $payload['source'] = $source->value;
        }

        return $payload;
    }

    /**
     * Seed templates onto every matter using the given workflow.
     */
    public static function ensureSeededForWorkflow(int $workflowId): void
    {
        if ($workflowId <= 0) {
            return;
        }

        ClientMatter::where('workflow_id', $workflowId)
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($matters) {
                foreach ($matters as $matter) {
                    self::ensureSeededForMatter($matter->id);
                }
            });
    }
}
