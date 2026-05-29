<?php

namespace App\Services;

use App\Models\EmailLog;
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

    /**
     * @param  array<int, array<string, mixed>>  $events
     * @return array{processed: int, updated: int, skipped: int}
     */
    public function processEvents(array $events): array
    {
        $stats = ['processed' => 0, 'updated' => 0, 'skipped' => 0];

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

            $mapped = $this->mapEventToStatus($event);
            if ($mapped === null) {
                $stats['skipped']++;
                continue;
            }

            $stats['processed']++;

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

            if ($this->applyStatusUpdate($emailLog, $mapped, $event)) {
                $stats['updated']++;
            } else {
                $stats['skipped']++;
            }
        }

        return $stats;
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
     * @param  array<string, mixed>  $event
     * @return array{status: string, reason: ?string}|null
     */
    private function mapEventToStatus(array $event): ?array
    {
        $eventType = strtolower((string) ($event['event'] ?? ''));

        return match ($eventType) {
            'processed' => ['status' => 'processed', 'reason' => null],
            'delivered' => ['status' => 'delivered', 'reason' => null],
            'deferred'  => ['status' => 'deferred', 'reason' => $this->extractReason($event)],
            'dropped'   => ['status' => 'dropped', 'reason' => $this->extractReason($event)],
            'bounce'    => $this->mapBounceEvent($event),
            default     => null,
        };
    }

    /**
     * @param  array<string, mixed>  $event
     * @return array{status: string, reason: ?string}
     */
    private function mapBounceEvent(array $event): array
    {
        $bounceType = strtolower((string) ($event['type'] ?? 'bounce'));

        if ($bounceType === 'blocked') {
            return ['status' => 'blocked', 'reason' => $this->extractReason($event)];
        }

        return ['status' => 'bounced', 'reason' => $this->extractReason($event)];
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
     * @param  array{status: string, reason: ?string}  $mapped
     * @param  array<string, mixed>  $event
     */
    private function applyStatusUpdate(EmailLog $emailLog, array $mapped, array $event): bool
    {
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

    private function shouldUpdateStatus(string $current, string $incoming): bool
    {
        // Webhook events can override a CRM-side send_failed when SendGrid actually processed the mail.
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
}
