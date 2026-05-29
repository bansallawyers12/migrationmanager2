<?php

namespace Tests\Unit\Services;

use App\Services\SendGridWebhookService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendGridWebhookServiceTest extends TestCase
{
    #[Test]
    public function it_maps_delivery_status_labels(): void
    {
        $this->assertSame('Pending', SendGridWebhookService::statusLabel(null));
        $this->assertSame('Delivered', SendGridWebhookService::statusLabel('delivered'));
        $this->assertSame('Undelivered', SendGridWebhookService::statusLabel('bounced'));
        $this->assertSame('Send Failed', SendGridWebhookService::statusLabel('send_failed'));
    }

    #[Test]
    public function it_skips_events_without_email_log_id(): void
    {
        $service = new SendGridWebhookService();

        $stats = $service->processEvents([
            ['event' => 'delivered', 'sg_message_id' => 'abc'],
        ]);

        $this->assertSame(0, $stats['processed']);
        $this->assertSame(0, $stats['updated']);
        $this->assertSame(1, $stats['skipped']);
    }

    #[Test]
    public function it_skips_unsupported_event_types(): void
    {
        $service = new SendGridWebhookService();

        $stats = $service->processEvents([
            ['event' => 'open', 'email_log_id' => '1'],
        ]);

        $this->assertSame(0, $stats['processed']);
        $this->assertSame(1, $stats['skipped']);
    }
}
