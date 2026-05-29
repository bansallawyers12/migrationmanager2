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
            ['event' => 'unknown_event', 'email_log_id' => '1'],
        ]);

        $this->assertSame(0, $stats['processed']);
        $this->assertSame(1, $stats['skipped']);
    }

    #[Test]
    public function it_accepts_open_events_for_processing(): void
    {
        $service = new SendGridWebhookService();

        $stats = $service->processEvents([
            ['event' => 'open', 'email_log_id' => '999999999', 'timestamp' => time()],
        ]);

        $this->assertSame(0, $stats['processed']);
        $this->assertSame(0, $stats['updated']);
        $this->assertSame(1, $stats['skipped']);
    }

    #[Test]
    public function it_normalizes_spamreport_event_type(): void
    {
        $service = new SendGridWebhookService();

        $stats = $service->processEvents([
            ['event' => 'spamreport', 'email_log_id' => '999999999', 'timestamp' => time()],
        ]);

        $this->assertSame(0, $stats['processed']);
        $this->assertSame(1, $stats['skipped']);
    }

    #[Test]
    public function it_summarizes_common_user_agents(): void
    {
        $this->assertSame('Apple device', SendGridWebhookService::summarizeUserAgent('Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)'));
        $this->assertSame('Gmail', SendGridWebhookService::summarizeUserAgent('Mozilla/5.0 Gmail'));
        $this->assertNull(SendGridWebhookService::summarizeUserAgent(null));
    }
}
