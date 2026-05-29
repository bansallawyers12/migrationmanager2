<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\EmailLogEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendGridWebhookService
{
    /** @var array<string, int> Higher rank = more final; never downgrade status. */
    private const STATUS_RANK = [
        'pending'     => 0,
        'processed'   => 10,
        'deferred'    => 20,
        'blocked'     => 25,
        'delivered'   => 30,
        'bounced'     => 40,
        'dropped'     => 45,
        'send_failed' => 50,
    ];

    private const DELIVERY_EVENT_TYPES = [
        'processed',
        'delivered',
        'deferred',
        'blocked',
        'bounced',
        'dropped',
    ];

    private const ENGAGEMENT_EVENT_TYPES = [
        'open',
        'click',
        'spamreport',
        'unsubscribe',
        'group_unsubscribe',
        'group_resubscribe',
    ];

    /**
     * @param  array<int, array<string, mixed>>  $events
     * @return array{processed: int, updated: int, skipped: int, events_recorded: int}
     */
    public function processEvents(array $events): array
    {
        $stats = ['processed' => 0, 'updated' => 0, 'skipped' => 0, 'events_recorded' => 0];

        foreach ($events as $event) {
            if (! is_array($event)) {
                $stats['skipped']++;
                continue;
            }

            $emailLogId = $this->resolveEmailLogId($event);
            if ($emailLogId === null) {
                $stats['skipped']++;
                continue;
            }

            $eventType = $this->normalizeEventType($event);
            if ($eventType === null) {
                $stats['skipped']++;
                continue;
            }

            $emailLog = EmailLog::find($emailLogId);
            if (! $emailLog) {
                Log::warning('SendGrid webhook: email_log not found', ['email_log_id' => $emailLogId]);
                $stats['skipped']++;
                continue;
            }

            if ((int) $emailLog->mail_type !== 1) {
                Log::warning('SendGrid webhook: ignoring non-outgoing email_log', ['email_log_id' => $emailLogId]);
                $stats['skipped']++;
                continue;
            }

            $stats['processed']++;

            $eventRecorded = $this->recordEvent($emailLog, $eventType, $event);
            if ($eventRecorded) {
                $stats['events_recorded']++;
            }

            $parentUpdated = false;
            if (in_array($eventType, self::DELIVERY_EVENT_TYPES, true)) {
                $mapped = $this->mapDeliveryEventToStatus($eventType, $event);
                $parentUpdated = $this->applyStatusUpdate($emailLog, $mapped, $event);
            } elseif (in_array($eventType, self::ENGAGEMENT_EVENT_TYPES, true)) {
                $parentUpdated = $this->applyEngagementUpdate($emailLog, $eventType, $event);
            }

            if ($parentUpdated) {
                $stats['updated']++;
            } elseif (! $eventRecorded && ! $parentUpdated) {
                $stats['skipped']++;
            }
        }

        return $stats;
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function normalizeEventType(array $event): ?string
    {
        $raw = strtolower((string) ($event['event'] ?? ''));

        if ($raw === 'bounce') {
            $bounceType = strtolower((string) ($event['type'] ?? 'bounce'));

            return $bounceType === 'blocked' ? 'blocked' : 'bounced';
        }

        if ($raw === 'spam report' || $raw === 'spamreport') {
            return 'spamreport';
        }

        $supported = array_merge(self::DELIVERY_EVENT_TYPES, self::ENGAGEMENT_EVENT_TYPES);

        return in_array($raw, $supported, true) ? $raw : null;
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function resolveEmailLogId(array $event): ?int
    {
        $candidates = [
            $event['email_log_id'] ?? null,
            $event['unique_args']['email_log_id'] ?? null,
            $event['custom_args']['email_log_id'] ?? null,
        ];

        foreach ($candidates as $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                return (int) $value;
            }
        }

        return null;
    }

    /**
     * @return array{status: string, reason: ?string}
     */
    private function mapDeliveryEventToStatus(string $eventType, array $event): array
    {
        return match ($eventType) {
            'processed' => ['status' => 'processed', 'reason' => null],
            'delivered' => ['status' => 'delivered', 'reason' => null],
            'deferred'  => ['status' => 'deferred', 'reason' => $this->extractReason($event)],
            'blocked'   => ['status' => 'blocked', 'reason' => $this->extractReason($event)],
            'bounced'   => ['status' => 'bounced', 'reason' => $this->extractReason($event)],
            'dropped'   => ['status' => 'dropped', 'reason' => $this->extractReason($event)],
            default     => ['status' => 'pending', 'reason' => null],
        };
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function extractReason(array $event): ?string
    {
        foreach (['reason', 'response', 'status'] as $key) {
            if (! empty($event[$key]) && is_string($event[$key])) {
                return trim($event[$key]);
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function recordEvent(EmailLog $emailLog, string $eventType, array $event): bool
    {
        $sendgridEventId = ! empty($event['sg_event_id']) && is_string($event['sg_event_id'])
            ? mb_substr($event['sg_event_id'], 0, 64)
            : null;

        if ($sendgridEventId !== null && EmailLogEvent::where('sendgrid_event_id', $sendgridEventId)->exists()) {
            return false;
        }

        EmailLogEvent::create([
            'email_log_id'       => $emailLog->id,
            'event_type'         => $eventType,
            'occurred_at'        => $this->eventTimestamp($event) ?? now(),
            'metadata'           => $this->buildEventMetadata($eventType, $event),
            'sendgrid_event_id'  => $sendgridEventId,
        ]);

        return true;
    }

    /**
     * @param  array<string, mixed>  $event
     * @return array<string, mixed>
     */
    private function buildEventMetadata(string $eventType, array $event): array
    {
        $metadata = array_filter([
            'email'          => $event['email'] ?? null,
            'sg_message_id'  => $event['sg_message_id'] ?? null,
            'reason'         => $this->extractReason($event),
        ], static fn ($value) => $value !== null && $value !== '');

        if ($eventType === 'click' && ! empty($event['url']) && is_string($event['url'])) {
            $metadata['url'] = mb_substr($event['url'], 0, 2048);
        }

        if (in_array($eventType, ['open', 'click'], true)) {
            foreach (['useragent', 'ip'] as $key) {
                if (! empty($event[$key]) && is_string($event[$key])) {
                    $metadata[$key] = mb_substr($event[$key], 0, 512);
                }
            }
        }

        return $metadata;
    }

    /**
     * @param  array{status: string, reason: ?string}  $mapped
     * @param  array<string, mixed>  $event
     */
    private function applyStatusUpdate(EmailLog $emailLog, array $mapped, array $event): bool
    {
        $emailLog->refresh();

        $newStatus = $mapped['status'];
        $currentStatus = $emailLog->delivery_status ?: 'pending';

        if (! $this->shouldUpdateStatus($currentStatus, $newStatus)) {
            return false;
        }

        $updates = ['delivery_status' => $newStatus];

        if (! empty($event['sg_message_id']) && is_string($event['sg_message_id'])) {
            $updates['sendgrid_message_id'] = mb_substr($event['sg_message_id'], 0, 255);
        }

        if ($newStatus === 'delivered') {
            $updates['delivered_at'] = $this->eventTimestamp($event) ?? now();
        }

        if ($mapped['reason'] !== null) {
            $updates['status_reason'] = mb_substr($mapped['reason'], 0, 65000);
        } elseif (in_array($newStatus, ['delivered', 'processed'], true)) {
            $updates['status_reason'] = null;
        }

        $emailLog->update($updates);

        Log::info('SendGrid delivery status updated', [
            'email_log_id' => $emailLog->id,
            'from'         => $currentStatus,
            'to'           => $newStatus,
            'event'        => $event['event'] ?? null,
        ]);

        return true;
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function applyEngagementUpdate(EmailLog $emailLog, string $eventType, array $event): bool
    {
        $emailLog->refresh();

        $occurredAt = $this->eventTimestamp($event) ?? now();
        $updates = [];

        if ($eventType === 'open' && $emailLog->opened_at === null) {
            $updates['opened_at'] = $occurredAt;
        }

        if ($eventType === 'click' && $emailLog->clicked_at === null) {
            $updates['clicked_at'] = $occurredAt;
        }

        if ($eventType === 'spamreport' && $emailLog->spam_reported_at === null) {
            $updates['spam_reported_at'] = $occurredAt;
        }

        if ($updates === []) {
            return false;
        }

        $emailLog->update($updates);

        Log::info('SendGrid engagement updated', [
            'email_log_id' => $emailLog->id,
            'event'        => $eventType,
        ]);

        return true;
    }

    private function shouldUpdateStatus(string $current, string $incoming): bool
    {
        if ($current === 'send_failed' && $incoming !== 'send_failed') {
            return in_array($incoming, ['processed', 'delivered', 'deferred', 'blocked', 'bounced', 'dropped'], true);
        }

        $currentRank = self::STATUS_RANK[$current] ?? 0;
        $incomingRank = self::STATUS_RANK[$incoming] ?? 0;

        return $incomingRank > $currentRank;
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function eventTimestamp(array $event): ?Carbon
    {
        if (! isset($event['timestamp'])) {
            return null;
        }

        $ts = (int) $event['timestamp'];

        return $ts > 0 ? Carbon::createFromTimestamp($ts) : null;
    }

    public static function statusLabel(?string $status): string
    {
        return match ($status) {
            'processed'   => 'Processing',
            'delivered'   => 'Delivered',
            'deferred'    => 'Delayed',
            'blocked'     => 'Delayed',
            'bounced'     => 'Undelivered',
            'dropped'     => 'Failed',
            'send_failed' => 'Send Failed',
            default       => 'Pending',
        };
    }

    public static function statusBadgeClass(?string $status): string
    {
        return match ($status) {
            'delivered'   => 'badge-success',
            'processed'   => 'badge-info',
            'deferred', 'blocked' => 'badge-warning',
            'bounced', 'dropped', 'send_failed' => 'badge-danger',
            default       => 'badge-secondary',
        };
    }

    public static function summarizeUserAgent(?string $userAgent): ?string
    {
        if ($userAgent === null || $userAgent === '') {
            return null;
        }

        $ua = strtolower($userAgent);

        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipad')) {
            return 'Apple device';
        }
        if (str_contains($ua, 'android')) {
            return 'Android device';
        }
        if (str_contains($ua, 'outlook')) {
            return 'Outlook';
        }
        if (str_contains($ua, 'gmail')) {
            return 'Gmail';
        }
        if (str_contains($ua, 'applemail') || str_contains($ua, 'mac os x mail')) {
            return 'Apple Mail';
        }

        return 'Email client';
    }
}
