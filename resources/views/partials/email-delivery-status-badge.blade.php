@php
    $status = $status ?? 'pending';
    $label = \App\Services\SendGridWebhookService::statusLabel($status);
    $badgeClass = \App\Services\SendGridWebhookService::statusBadgeClass($status);
    $tip = $reason ?? null;
    if ($tip === null && $status === 'pending') {
        $tip = 'Waiting for SendGrid delivery confirmation.';
    }
@endphp
<span class="badge {{ $badgeClass }} email-delivery-badge"
      @if($tip) data-toggle="tooltip" title="{{ e($tip) }}" @endif>{{ $label }}</span>
