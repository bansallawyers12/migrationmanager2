<?php

namespace App\Mcp\Tools;

use App\Models\ActivitiesLog;
use App\Models\Admin;
use App\Models\ClientMatter;
use App\Support\Mcp\CrmMcpAccess;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Get a CRM client/lead/company by id, including recent activity and open matters (stage + status). Respects staff visibility.')]
#[IsReadOnly]
class GetContactTool extends Tool
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
            'activity_limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ], [
            'contact_id.required' => 'Provide contact_id (admins.id from SearchContactsTool).',
        ]);

        $contactId = (int) $validated['contact_id'];
        if ($denied = CrmMcpAccess::denyUnlessCanAccess($contactId, $staff)) {
            return $denied;
        }

        $contact = Admin::query()
            ->with(['company:id,admin_id,company_name,ABN_number,ACN,trading_name'])
            ->whereKey($contactId)
            ->whereIn('type', ['client', 'lead'])
            ->first([
                'id', 'type', 'first_name', 'last_name', 'email', 'phone', 'country_code',
                'client_id', 'is_company', 'lead_status', 'followup_date', 'user_id',
                'status', 'is_archived', 'created_at', 'updated_at',
            ]);

        if (! $contact) {
            return Response::error('Contact not found.');
        }

        $activityLimit = (int) ($validated['activity_limit'] ?? 15);

        $activities = ActivitiesLog::query()
            ->where('client_id', $contactId)
            ->orderByDesc('created_at')
            ->limit($activityLimit)
            ->get(['id', 'subject', 'description', 'activity_type', 'task_group', 'followup_date', 'created_by', 'created_at'])
            ->map(static function (ActivitiesLog $log): array {
                return [
                    'id' => (int) $log->id,
                    'subject' => $log->subject,
                    'description' => trim(strip_tags((string) $log->description)),
                    'activity_type' => $log->activity_type,
                    'task_group' => $log->task_group,
                    'followup_date' => optional($log->followup_date)?->toDateTimeString(),
                    'created_by' => (int) $log->created_by,
                    'created_at' => optional($log->created_at)?->toDateTimeString(),
                ];
            })
            ->all();

        $matters = ClientMatter::query()
            ->with(['workflowStage:id,name,workflow_id', 'matter:id,title,nick_name'])
            ->where('client_id', $contactId)
            ->orderByDesc('matter_status')
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get([
                'id', 'client_id', 'sel_matter_id', 'workflow_id', 'workflow_stage_id',
                'matter_status', 'client_unique_matter_no', 'deadline', 'decision_outcome',
                'sel_migration_agent', 'sel_person_responsible', 'sel_person_assisting',
            ])
            ->map(static function (ClientMatter $matter): array {
                return [
                    'id' => (int) $matter->id,
                    'matter_no' => $matter->client_unique_matter_no,
                    'matter_status' => (int) $matter->matter_status,
                    'matter_status_label' => (int) $matter->matter_status === 1 ? 'open' : 'discontinued',
                    'workflow_id' => $matter->workflow_id ? (int) $matter->workflow_id : null,
                    'workflow_stage_id' => $matter->workflow_stage_id ? (int) $matter->workflow_stage_id : null,
                    'workflow_stage' => $matter->workflowStage?->name,
                    'matter_type' => $matter->matter?->title ?? $matter->matter?->nick_name,
                    'deadline' => optional($matter->deadline)?->toDateString(),
                    'decision_outcome' => $matter->decision_outcome,
                    'staff' => [
                        'migration_agent_id' => $matter->sel_migration_agent ? (int) $matter->sel_migration_agent : null,
                        'person_responsible_id' => $matter->sel_person_responsible ? (int) $matter->sel_person_responsible : null,
                        'person_assisting_id' => $matter->sel_person_assisting ? (int) $matter->sel_person_assisting : null,
                    ],
                ];
            })
            ->all();

        return Response::structured([
            'contact' => [
                'id' => (int) $contact->id,
                'type' => $contact->type,
                'is_company' => (bool) $contact->is_company,
                'name' => $contact->company_name_or_personal_name,
                'email' => $contact->email,
                'phone' => trim(($contact->country_code ? '+'.$contact->country_code.' ' : '').($contact->phone ?? '')),
                'crm_ref' => $contact->client_id,
                'lead_status' => $contact->lead_status,
                'followup_date' => optional($contact->followup_date)?->toDateTimeString(),
                'assignee_staff_id' => $contact->user_id ? (int) $contact->user_id : null,
                'company' => $contact->company ? [
                    'name' => $contact->company->company_name,
                    'trading_name' => $contact->company->trading_name,
                    'abn' => $contact->company->ABN_number,
                    'acn' => $contact->company->ACN,
                ] : null,
            ],
            'activities' => $activities,
            'matters' => $matters,
        ]);
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
            'activity_limit' => $schema->integer()
                ->description('How many recent activities_logs rows to return (default 15).'),
        ];
    }
}
