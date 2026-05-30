<?php

namespace Tests\Unit\Services;

use App\Services\SystemEmailLogService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SystemEmailLogServiceTest extends TestCase
{
    #[Test]
    public function it_normalizes_known_categories(): void
    {
        $service = new SystemEmailLogService();

        $this->assertSame('invoice', $service->normalizeCategory('invoice'));
        $this->assertSame('appointment_reminder', $service->normalizeCategory('appointment_reminder'));
        $this->assertSame('other', $service->normalizeCategory('unknown_type'));
    }

    #[Test]
    public function it_labels_categories_for_display(): void
    {
        $service = new SystemEmailLogService();

        $this->assertSame('Invoice', $service->categoryLabel('invoice'));
        $this->assertSame('Visa Expiry Reminder', $service->categoryLabel('visa_reminder'));
        $this->assertSame('Other', $service->categoryLabel(null));
    }

    #[Test]
    public function it_resolves_recipient_addresses(): void
    {
        $service = new SystemEmailLogService();

        $this->assertSame('a@example.com', $service->resolveRecipient('a@example.com'));
        $this->assertSame('a@example.com, b@example.com', $service->resolveRecipient(['a@example.com', 'b@example.com']));
    }

    #[Test]
    public function conversion_type_constant_is_system_generated(): void
    {
        $this->assertSame('system_generated', SystemEmailLogService::CONVERSION_TYPE);
    }
}
