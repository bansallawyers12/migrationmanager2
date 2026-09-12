<?php

namespace App\Mcp\Tools;

use App\Models\ActivitiesLog;
use App\Models\Admin;
use App\Models\ClientMatter;
use App\Models\WorkflowStage;
use App\Services\MatterEmailBodyCleanupService;
use App\Support\Mcp\CrmMcpAccess;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[Description('Update lead pipeline status (admins.lead_status, including converted) and/or a matter open/discontinued status plus workflow stage. Requires visibility to the contact; matter discontinue/reopen requires an allowed role.')]
#[IsDestructive]
class UpdateContactOrMatterStatusTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): mixed
    {
        $staff = CrmMcpAccess::staff($request);
        if ($staff instanceof Response) {
            return $staff;
        }

        $validated = $request->validate([
            'contact_id' => ['required', 'integer', 'min:1'],
            'lead_status' => ['nullable', 'string', 'in:new,follow_up,not_qualified,hostile,converted'],
            'followup_date' => ['nullable', 'date'],
            'matter_id' => ['nullable', 'integer', 'min:1'],
            'matter_status' => ['nullable', 'integer', 'in:0,1'],
            'workflow_stage_id' => ['nullable', 'integer', 'min:1'],
            'discontinue_reason' => ['nullable', 'string', 'max:500'],
        ], [
            'lead_status.in' => 'lead_status must be one of: new, follow_up, not_qualified, hostile, converted.',
            'matter_status.in' => 'matter_status must be 1 (open) or 0 (discontinued).',
        ]);

        $hasLeadUpdate = array_key_exists('lead_status', $validated) && $validated['lead_status'] !== null;
        $hasMatterUpdate = ! empty($validated['matter_id']) && (
            (array_key_exists('matter_status', $validated) && $validated['matter_status'] !== null)
            || ! empty($validated['workflow_stage_id'])
        );

        if (! $hasLeadUpdate && ! $hasMatterUpdate) {
            return Response::error('Provide lead_status and/or matter_id with matter_status and/or workflow_stage_id.');
        }

        $contactId = (int) $validated['contact_id'];
        if ($denied = CrmMcpAccess::denyUnlessCanAccess($contactId, $staff)) {
            return $denied;
        }

        $contact = Admin::query()
            ->whereKey($contactId)
            ->whereIn('type', ['client', 'lead'])
            ->first();

        if (! $contact) {
            return Response::error('Contact not found.');
        }

        $result = [
            'contact_id' => $contactId,
            'lead' => null,
            'matter' => null,
        ];

        try {
            DB::transaction(function () use ($validated, $staff, $contact, $contactId, $hasLeadUpdate, $hasMatterUpdate, &$result) {
                if ($hasLeadUpdate) {
                    $oldStatus = $contact->lead_status;
                    $newStatus = $validated['lead_status'];
                    $contact->lead_status = $newStatus;

                    if ($newStatus === 'follow_up' && ! empty($validated['followup_date'])) {
                        $contact->followup_date = $validated['followup_date'];
                    }

                    if ($newStatus === 'converted' && $contact->type === 'lead') {
                        // Status only — full LeadConversionController flow is out of scope for MCP.
                        $contact->type = 'client';
                    }

                    $contact->save();

                    ActivitiesLog::create([
                        'client_id' => $contactId,
                        'created_by' => $staff->id,
                        'subject' => 'updated lead status',
                        'description' => '<p>Lead status: <strong>'.e((string) $oldStatus).'</strong> → <strong>'.e($newStatus).'</strong></p>',
                        'activity_type' => 'activity',
                        'task_status' => 0,
                        'pin' => 0,
                    ]);

                    $result['lead'] = [
                        'lead_status' => $contact->lead_status,
                        'type' => $contact->type,
                        'followup_date' => CrmMcpAccess::formatDateTime($contact->followup_date),
                    ];
                }

                if ($hasMatterUpdate) {
                    $matterId = (int) $validated['matter_id'];
                    $matter = ClientMatter::query()
                        ->with('workflowStage:id,name')
                        ->whereKey($matterId)
                        ->where('client_id', $contactId)
                        ->first();

                    if (! $matter) {
                        throw new \InvalidArgumentException('matter_id does not belong to this contact.');
                    }

                    $changes = [];
                    $shouldCleanupEmailBodies = false;

                    if (array_key_exists('matter_status', $validated) && $validated['matter_status'] !== null) {
                        $newMatterStatus = (int) $validated['matter_status'];
                        $oldMatterStatus = (int) $matter->matter_status;

                        if ($newMatterStatus !== $oldMatterStatus) {
                            if (! CrmMcpAccess::mayDiscontinueOrReopenMatter($staff)) {
                                throw new \RuntimeException('Only Super Admin, Admin, or Migration Agent roles can open/discontinue matters.');
                            }

                            $matter->matter_status = $newMatterStatus;
                            $changes[] = 'matter_status '.$oldMatterStatus.' → '.$newMatterStatus;
                            $shouldCleanupEmailBodies = $newMatterStatus === 0;

                            if ($newMatterStatus === 0) {
                                $reason = trim((string) ($validated['discontinue_reason'] ?? 'Updated via MCP'));
                                ActivitiesLog::create([
                                    'client_id' => $contactId,
                                    'created_by' => $staff->id,
                                    'subject' => 'Matter Discontinued',
                                    'description' => 'Discontinued matter. Reason: <b>'.e($reason).'</b>',
                                    'activity_type' => 'stage',
                                    'use_for' => 'matter',
                                    'task_status' => 0,
                                    'pin' => 0,
                                    'source' => 'mcp',
                                ]);
                            } elseif ($newMatterStatus === 1) {
                                ActivitiesLog::create([
                                    'client_id' => $contactId,
                                    'created_by' => $staff->id,
                                    'subject' => 'Matter Reopened',
                                    'description' => 'Matter reopened via MCP.',
                                    'activity_type' => 'stage',
                                    'use_for' => 'matter',
                                    'task_status' => 0,
                                    'pin' => 0,
                                    'source' => 'mcp',
                                ]);
                            }
                        }
                    }

                    if (! empty($validated['workflow_stage_id'])) {
                        $stageId = (int) $validated['workflow_stage_id'];
                        $stage = WorkflowStage::query()->whereKey($stageId)->first(['id', 'name', 'workflow_id']);

                        if (! $stage) {
                            throw new \InvalidArgumentException('workflow_stage_id not found.');
                        }

                        if ($matter->workflow_id && (int) $stage->workflow_id !== (int) $matter->workflow_id) {
                            throw new \InvalidArgumentException('workflow_stage_id does not belong to this matter\'s workflow.');
                        }

                        $oldStageId = $matter->workflow_stage_id;
                        $oldStageName = $matter->workflowStage?->name ?? (string) $oldStageId;
                        $matter->workflow_stage_id = $stageId;
                        $changes[] = 'stage '.$oldStageName.' → '.$stage->name;

                        ActivitiesLog::create([
                            'client_id' => $contactId,
                            'created_by' => $staff->id,
                            'subject' => 'updated workflow stage',
                            'description' => '<p>Stage: <strong>'.e((string) $oldStageName).'</strong> → <strong>'.e($stage->name).'</strong></p>',
                            'activity_type' => 'stage',
                            'use_for' => 'matter',
                            'task_status' => 0,
                            'pin' => 0,
                            'source' => 'mcp',
                        ]);
                    }

                    if ($changes !== []) {
                        $matter->save();

                        if ($shouldCleanupEmailBodies) {
                            app(MatterEmailBodyCleanupService::class)->clearBodiesForMatter((int) $matter->id);
                        }
                    }

                    $matter->load('workflowStage:id,name');

                    $result['matter'] = [
                        'id' => (int) $matter->id,
                        'matter_status' => (int) $matter->matter_status,
                        'workflow_stage_id' => $matter->workflow_stage_id ? (int) $matter->workflow_stage_id : null,
                        'workflow_stage' => $matter->workflowStage?->name,
                        'changes' => $changes,
                    ];
                }
            });
        } catch (\InvalidArgumentException|\RuntimeException $e) {
            return Response::error($e->getMessage());
        }

        return Response::structured(array_merge($result, [
            'message' => 'Status updated successfully.',
        ]));
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'contact_id' => $schema->integer()
                ->description('admins.id of the client or lead.')
                ->required(),
            'lead_status' => $schema->string()
                ->enum(['new', 'follow_up', 'not_qualified', 'hostile', 'converted'])
                ->description('Lead pipeline status. Setting converted also promotes type lead→client.'),
            'followup_date' => $schema->string()
                ->description('Optional follow-up datetime when lead_status is follow_up.'),
            'matter_id' => $schema->integer()
                ->description('client_matters.id to update.'),
            'matter_status' => $schema->integer()
                ->description('1 = open, 0 = discontinued.'),
            'workflow_stage_id' => $schema->integer()
                ->description('workflow_stages.id belonging to the matter workflow.'),
            'discontinue_reason' => $schema->string()
                ->description('Reason text when setting matter_status to 0.'),
        ];
    }
}
