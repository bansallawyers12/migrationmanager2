<?php

namespace App\Mcp\Tools;

use App\Models\Note;
use App\Support\Mcp\CrmMcpAccess;
use App\Support\StaffClientVisibility;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('List incomplete overdue CRM actions (is_action=1, status not completed, action_date in the past). Non–super-admins see only actions assigned to them; super admin (role=1) sees all. Results are further limited to clients/leads the staff can access.')]
#[IsReadOnly]
class ListOverdueFollowUpsTool extends Tool
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
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int) ($validated['limit'] ?? 50);

        $query = Note::query()
            ->with([
                'client:id,type,first_name,last_name,client_id,is_company',
                'client.company:id,admin_id,company_name',
                'assignedStaff:id,first_name,last_name,email',
            ])
            ->where('is_action', 1)
            ->where('status', '<>', '1')
            ->whereNotNull('action_date')
            ->where('action_date', '<', now())
            ->where('type', 'client')
            ->whereNotNull('client_id')
            ->orderBy('action_date');

        if (! CrmMcpAccess::isSuperAdmin($staff)) {
            $query->where('assigned_to', $staff->id);
        }

        $rows = $query->limit(min(200, max($limit * 3, $limit)))->get();

        $accessMap = StaffClientVisibility::globalSearchCanAccessMap(
            $rows->pluck('client_id')->map(fn ($id) => (int) $id)->all(),
            $staff
        );

        $items = [];
        foreach ($rows as $note) {
            $clientId = (int) $note->client_id;
            if (! ($accessMap[$clientId] ?? false)) {
                continue;
            }

            $client = $note->client;
            $items[] = [
                'note_id' => (int) $note->id,
                'task_group' => $note->task_group,
                'title' => $note->title,
                'description' => trim(strip_tags((string) $note->description)),
                'action_date' => CrmMcpAccess::formatDateTime($note->action_date),
                'assigned_to' => $note->assigned_to ? (int) $note->assigned_to : null,
                'assignee_name' => $note->assignedStaff
                    ? trim($note->assignedStaff->first_name.' '.$note->assignedStaff->last_name)
                    : null,
                'contact' => $client ? [
                    'id' => (int) $client->id,
                    'type' => $client->type,
                    'name' => $client->company_name_or_personal_name,
                    'crm_ref' => $client->client_id,
                ] : null,
            ];

            if (count($items) >= $limit) {
                break;
            }
        }

        return Response::structured([
            'scope' => CrmMcpAccess::isSuperAdmin($staff) ? 'all' : 'assigned_to_me',
            'count' => count($items),
            'items' => $items,
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'limit' => $schema->integer()
                ->description('Max overdue actions to return (default 50, max 100).'),
        ];
    }
}
