<?php

namespace Tests\Unit\Mail;

use App\Services\SesSenderService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OutboundMailerConfigTest extends TestCase
{
    #[Test]
    public function failover_mailer_tries_ses_then_zoho(): void
    {
        $this->assertSame('ses', config('mail.mailers.ses.transport'));
        $this->assertSame('smtp', config('mail.mailers.zoho.transport'));
        $this->assertSame('smtp.zoho.com', config('mail.mailers.zoho.host'));
        $this->assertSame('failover', config('mail.mailers.failover.transport'));
        $this->assertSame(['ses', 'zoho'], config('mail.mailers.failover.mailers'));
        $this->assertSame(['ses', 'zoho'], config('mail.mailers.sendgrid.mailers'));
    }

    #[Test]
    public function env_senders_include_info_and_noreply_on_allowed_domain(): void
    {
        config([
            'services.ses_crm.senders' => 'info@bansalimmigration.com.au,noreply@bansalimmigration.com.au',
            'services.ses_crm.from_allowed_domains' => 'bansalimmigration.com.au',
            'mail.from.address' => 'info@bansalimmigration.com.au',
            'mail.noreply.address' => 'noreply@bansalimmigration.com.au',
            'mail.info.address' => 'info@bansalimmigration.com.au',
        ]);

        $senders = (new SesSenderService)->envSenders();
        $emails = array_column($senders, 'email');

        $this->assertContains('info@bansalimmigration.com.au', $emails);
        $this->assertContains('noreply@bansalimmigration.com.au', $emails);
    }

    #[Test]
    public function env_senders_exclude_addresses_outside_allowed_domain(): void
    {
        config([
            'services.ses_crm.senders' => 'info@bansalimmigration.com.au,other@example.com',
            'services.ses_crm.from_allowed_domains' => 'bansalimmigration.com.au',
            'mail.from.address' => '',
            'mail.noreply.address' => '',
            'mail.info.address' => '',
        ]);

        $senders = (new SesSenderService)->envSenders();
        $emails = array_column($senders, 'email');

        $this->assertSame(['info@bansalimmigration.com.au'], $emails);
    }
}
