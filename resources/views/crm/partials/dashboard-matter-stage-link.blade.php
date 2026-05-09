{{-- Current workflow stage (read-only) linking to the client record Workflow tab. Requires $matter with client_id and eager-loaded workflowStage where possible. --}}
@php
    $stageName = optional($matter->workflowStage)->name;
    $stageDisplay = ($stageName !== null && trim((string) $stageName) !== '')
        ? trim((string) $stageName)
        : config('constants.empty');
    $workflowTabParams = [
        'client_id' => base64_encode(convert_uuencode($matter->client_id)),
        'tab' => 'workflow',
    ];
    if (! empty($matter->client_unique_matter_no)) {
        $workflowTabParams['client_unique_matter_ref_no'] = $matter->client_unique_matter_no;
    }
    $matterRef = trim((string) ($matter->client_unique_matter_no ?? ''));
    $stageLinkLabel = $matterRef !== '' ? $matterRef : ('matter ID ' . (int) $matter->id);
@endphp
<a href="{{ route('clients.detail', $workflowTabParams) }}"
   class="stage-static-link"
   title="Open Workflow tab for this matter"
   aria-label="Open Workflow tab for {{ $stageLinkLabel }}">
    {{ $stageDisplay }}
</a>
