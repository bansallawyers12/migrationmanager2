<?php

namespace App\Mcp\Tools;

use App\Models\ActivitiesLog;
use App\Models\Admin;
use App\Models\Note;
use App\Models\Notification;
use App\Models\Staff;
use App\Support\ActionTaskGroup;
use App\Support\Mcp\CrmMcpAccess;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[Description('Create a Follow Up action on a client/lead (is_action=1, task_group=Follow Up). Defaults assigned_to to the authenticated staff; may assign another staff. Writes note, activities_logs, and a notification when assigning someone else.')]
#[IsDestructive]
class LogFollowUpTool extends Tool
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
            'description' => ['required', 'string', 'min:1', 'max:10000'],
            'action_date' => ['required', 'date'],
            'assigned_to' => ['nullable', 'integer', 'min:1'],
            'title' => ['nullable', 'string', 'max:255'],
        ], [
            'action_date.required' => 'Provide action_date (when the follow-up is due), e.g. 2026-09-15 09:00:00.',
            'description.required' => 'Provide a description for the follow-up.',
        ]);

        $contactId = (int) $validated['contact_id'];
        if ($denied = CrmMcpAccess::denyUnlessCanAccess($contactId, $staff)) {
            return $denied;
        }

        $contact = Admin::query()
            ->with('company')
            ->whereKey($contactId)
            ->whereIn('type', ['client', 'lead'])
            ->first();

        if (! $contact) {
            return Response::error('Contact not found.');
        }

        $assigneeId = (int) ($validated['assigned_to'] ?? $staff->id);
        $assignee = Staff::query()->whereKey($assigneeId)->where('status', 1)->first(['id', 'first_name', 'last_name']);
        if (! $assignee) {
            return Response::error('assigned_to staff not found or inactive.');
        }

        $assigneeName = CrmMcpAccess::staffDisplayName($assignee);
        $clientLabel = trim($contact->company_name_or_personal_name);
        if ($clientLabel === '') {
            $clientLabel = trim(($contact->first_name ?? '').' '.($contact->last_name ?? ''));
        }

        $defaultTitle = ($clientLabel !== '' ? $clientLabel.': ' : '').'Assigned to '.$assigneeName;
        $title = $validated['title'] ?? $defaultTitle;
        $actionDate = $validated['action_date'];

        $note = null;
        DB::transaction(function () use (
            $staff,
            $contact,
            $contactId,
            $assigneeId,
            $assigneeName,
            $clientLabel,
            $title,
            $actionDate,
            $validated,
            &$note
        ) {
            $note = new Note;
            $note->client_id = $contactId;
            $note->user_id = $staff->id;
            $note->description = $validated['description'];
            $note->unique_group_id = 'group_'.uniqid('', true);
            $note->title = $title;
            $note->is_action = 1;
            $note->pin = 0;
            $note->status = '0';
            $note->type = 'client';
            $note->task_group = ActionTaskGroup::FOLLOW_UP;
            $note->assigned_to = $assigneeId;
            $note->action_date = $actionDate;
            $note->note_deadline = null;
            $note->save();

            $contact->followup_date = $actionDate;
            $contact->save();

            if ($assigneeId !== (int) $staff->id) {
                $notification = new Notification;
                $notification->sender_id = $staff->id;
                $notification->receiver_id = $assigneeId;
                $notification->module_id = $contactId;
                $notification->url = url('/clients/detail/'.base64_encode(convert_uuencode((string) $contactId)));
                $notification->notification_type = 'client';
                $notification->receiver_status = 0;
                $notification->seen = 0;
                $formattedDate = date('d/M/Y h:i A', strtotime((string) $actionDate) ?: time());
                $notification->message = ($clientLabel !== '' ? 'Followup for '.$clientLabel.'. ' : '')
                    .'Assigned by '.CrmMcpAccess::staffDisplayName($staff).' on '.$formattedDate;
                $notification->save();
            }

            ActivitiesLog::create([
                'client_id' => $contactId,
                'created_by' => $staff->id,
                'subject' => ActionTaskGroup::assignActivitySubject($assigneeName, ActionTaskGroup::FOLLOW_UP),
                'description' => '<span class="text-semi-bold">'.e($title).'</span><p>'.e($validated['description']).'</p>',
                'task_status' => 0,
                'pin' => 0,
                'use_for' => $assigneeId !== (int) $staff->id ? (string) $assigneeId : '',
                'followup_date' => $actionDate,
                'task_group' => ActionTaskGroup::FOLLOW_UP,
            ]);
        });

        return Response::structured([
            'note_id' => (int) $note->id,
            'contact_id' => $contactId,
            'assigned_to' => $assigneeId,
            'assignee_name' => $assigneeName,
            'action_date' => $actionDate,
            'task_group' => ActionTaskGroup::FOLLOW_UP,
            'message' => 'Follow-up created successfully.',
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
            'description' => $schema->string()
                ->description('What needs to be followed up.')
                ->required(),
            'action_date' => $schema->string()
                ->description('Due datetime for the follow-up (ISO or Y-m-d H:i:s).')
                ->required(),
            'assigned_to' => $schema->integer()
                ->description('staff.id to assign (defaults to the authenticated staff).'),
            'title' => $schema->string()
                ->description('Optional action title.'),
        ];
    }
}
