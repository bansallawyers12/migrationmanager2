<?php

namespace App\Mcp\Tools;

use App\Models\ActivitiesLog;
use App\Models\Admin;
use App\Models\ClientMatter;
use App\Models\Note;
use App\Services\StaffWorkloadService;
use App\Support\Mcp\CrmMcpAccess;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[Description('Log a CRM file note on a client or lead (is_action=0). task_group must be Call, Email, In-Person, Others, or Attention. Writes a matching activities_logs row like the CRM note UI.')]
#[IsDestructive]
class LogNoteTool extends Tool
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
            'task_group' => ['required', 'string', 'in:Call,Email,In-Person,Others,Attention'],
            'matter_id' => ['nullable', 'integer', 'min:1'],
            'title' => ['nullable', 'string', 'max:255'],
        ], [
            'task_group.in' => 'task_group must be one of: Call, Email, In-Person, Others, Attention.',
            'description.required' => 'Provide the note description/body.',
        ]);

        $contactId = (int) $validated['contact_id'];
        if ($denied = CrmMcpAccess::denyUnlessCanAccess($contactId, $staff)) {
            return $denied;
        }

        $contact = Admin::query()
            ->whereKey($contactId)
            ->whereIn('type', ['client', 'lead'])
            ->first(['id', 'type']);

        if (! $contact) {
            return Response::error('Contact not found.');
        }

        $matterId = isset($validated['matter_id']) ? (int) $validated['matter_id'] : null;
        $matterReference = '';
        if ($matterId) {
            $matter = ClientMatter::query()
                ->whereKey($matterId)
                ->where('client_id', $contactId)
                ->first(['id', 'client_unique_matter_no']);

            if (! $matter) {
                return Response::error('matter_id does not belong to this contact.');
            }

            $matterReference = (string) ($matter->client_unique_matter_no ?? '');
        }

        $taskGroup = $validated['task_group'];
        $noteTypeFormatted = ucfirst(strtolower($taskGroup));
        $entityType = $contact->type === 'lead' ? 'Lead' : 'Client';

        $note = null;
        DB::transaction(function () use ($validated, $staff, $contact, $contactId, $matterId, $taskGroup, $noteTypeFormatted, $entityType, $matterReference, &$note) {
            $note = new Note;
            $note->client_id = $contactId;
            $note->user_id = $staff->id;
            $note->title = $validated['title'] ?? '';
            $note->description = $validated['description'];
            $note->type = $contact->type;
            $note->task_group = $taskGroup;
            $note->matter_id = $matterId;
            $note->pin = 0;
            $note->is_action = 0;
            $note->status = '0';
            $note->save();

            $subjectLine = $matterReference !== ''
                ? "added {$noteTypeFormatted} Notes - {$matterReference}"
                : "added {$entityType} {$noteTypeFormatted} Notes";

            ActivitiesLog::create([
                'client_id' => $contactId,
                'created_by' => $staff->id,
                'subject' => $subjectLine,
                'description' => $validated['description'],
                'activity_type' => 'note',
                'task_status' => 0,
                'pin' => 0,
            ]);

            if ($matterId) {
                ClientMatter::query()->whereKey($matterId)->update(['updated_at' => now()]);
            }
        });

        if (in_array($taskGroup, ['Call', 'In-Person'], true)) {
            StaffWorkloadService::forgetForStaff((int) $staff->id);
        }

        return Response::structured([
            'note_id' => (int) $note->id,
            'contact_id' => $contactId,
            'task_group' => $taskGroup,
            'message' => 'Note logged successfully.',
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
                ->description('Note body (what was discussed / observed).')
                ->required(),
            'task_group' => $schema->string()
                ->enum(['Call', 'Email', 'In-Person', 'Others', 'Attention'])
                ->description('File-note contact type.')
                ->required(),
            'matter_id' => $schema->integer()
                ->description('Optional client_matters.id to attach the note to.'),
            'title' => $schema->string()
                ->description('Optional note title (often left empty in CRM).'),
        ];
    }
}
