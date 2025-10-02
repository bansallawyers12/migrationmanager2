@props(['note'])

@php
    $client = $note->client;
    $deadline = new DateTime($note->note_deadline);
    $today = new DateTime();
    $interval = $today->diff($deadline);
    $daysLeft = $interval->days;
    $daysLeftText = $daysLeft . ' day' . ($daysLeft != 1 ? 's' : '') . ' left';
    $daysLeftClass = $daysLeft <= 3 ? 'text-danger' : ($daysLeft <= 7 ? 'text-warning' : 'text-success');
@endphp

<li>
    <div class="task-details">
        <span class="client-name">
            {{ $client->first_name ?: config('constants.empty') }} {{ $client->last_name ?: config('constants.empty') }}
            (<a href="{{ route('admin.clients.detail', base64_encode(convert_uuencode($client->id))) }}">
                {{ $client->client_id ?: config('constants.empty') }}
            </a>)
        </span>
        <span class="task-desc">
            {{ strip_tags($note->description) }}
        </span>
    </div>
    <div class="task-deadline">
        <span class="date">{{ $deadline->format('d/m/Y') }}</span>
        <span class="days-left {{ $daysLeftClass }}">({{ $daysLeftText }})</span>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Action
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item has-icon" href="javascript:;" onclick="closeNotesDeadlineAction({{ $note->id }}, {{ $note->unique_group_id }})">
                    Close
                </a>
                <a class="dropdown-item has-icon btn-extend_note_deadline" 
                   data-noteid="{{ $note->id }}" 
                   data-uniquegroupid="{{ $note->unique_group_id }}" 
                   data-assignnote="{{ $note->description }}" 
                   data-deadlinedate="{{ $note->note_deadline }}" 
                   href="javascript:;">
                    Extend
                </a>
            </div>
        </div>
    </div>
</li>
