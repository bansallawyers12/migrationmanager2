@props(['case'])

@php
    $client = $case->client;
    $lastUpdated = new DateTime($case->updated_at);
    $today = new DateTime();
    $interval = $today->diff($lastUpdated);
    $daysStalled = $interval->days;
    
    // Safety check for null client
    if (!$client) {
        $client = (object) [
            'id' => null,
            'first_name' => null,
            'last_name' => null,
            'client_id' => null
        ];
    }
    
    if ($daysStalled < 1) {
        $daysStalledText = 'Today';
    } else {
        $daysStalledText = $daysStalled . ' days ago';
    }
    
    $daysStalledClass = $daysStalled > 14 ? 'text-danger' : ($daysStalled > 7 ? 'text-warning' : 'text-info');
    
    // Get matter name
    if ($case->sel_matter_id == 1) {
        $matter_name = 'General matter';
    } else {
        $matter = $case->matter ?? null;
        $matter_name = $matter ? $matter->title : 'NA';
    }
@endphp

<li>
    <div class="case-details">
        <span class="client-name">
            {{ $client->first_name ?: config('constants.empty') }} {{ $client->last_name ?: config('constants.empty') }}
            (<a href="{{ route('admin.clients.detail', [base64_encode(convert_uuencode($client->id)), $case->client_unique_matter_no]) }}">
                {{ $client->client_id ?: config('constants.empty') }}
            </a>)
        </span>
        <span class="case-info">
            <a href="{{ route('admin.clients.detail', [base64_encode(convert_uuencode($client->id)), $case->client_unique_matter_no]) }}">
                {{ $matter_name }} ({{ $case->client_unique_matter_no }})
            </a>
            <span style="display: inline-block;" class="stalled-days {{ $daysStalledClass }}">
                ({{ $daysStalledText }})
            </span>
        </span>
    </div>
    <span class="case-attention-reason reason-stalled">
        {{ $case->updated_at_type ?: 'NA' }}
    </span>
</li>
