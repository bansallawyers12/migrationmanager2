<?php

namespace Tests\Unit;

use App\Models\Email;
use App\Services\EmailService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailServiceComposeMailerTest extends TestCase
{
    #[Test]
    public function mailbox_with_password_uses_ses_then_that_zoho_account(): void
    {
        config(['mail.default' => 'failover']);

        $account = new Email([
            'email' => 'student2@bansalimmigration.com.au',
            'display_name' => 'Student 2',
            'password' => 'mailbox-secret',
            'smtp_host' => 'smtp.zoho.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
        ]);

        $mailer = (new EmailService)->composeMailerName($account);
        $chain = config("mail.mailers.{$mailer}.mailers");

        $this->assertSame('failover', config("mail.mailers.{$mailer}.transport"));
        $this->assertIsArray($chain);
        $this->assertSame('ses', $chain[0]);
        $this->assertSame('student2@bansalimmigration.com.au', config("mail.mailers.{$chain[1]}.username"));
        $this->assertSame('mailbox-secret', config("mail.mailers.{$chain[1]}.password"));
        $this->assertSame('smtp.zoho.com', config("mail.mailers.{$chain[1]}.host"));
        $this->assertSame(587, (int) config("mail.mailers.{$chain[1]}.port"));
        $this->assertSame('tls', config("mail.mailers.{$chain[1]}.encryption"));
        $this->assertSame(['ses', 'zoho'], config('mail.mailers.failover.mailers'));
    }

    #[Test]
    public function mailbox_without_password_keeps_default_mailer(): void
    {
        config(['mail.default' => 'failover']);

        $account = new Email([
            'email' => 'tr@bansalimmigration.com.au',
            'password' => null,
        ]);

        $this->assertSame('failover', (new EmailService)->composeMailerName($account));
        $this->assertSame(['ses', 'zoho'], config('mail.mailers.failover.mailers'));
    }

    #[Test]
    public function array_mailer_is_not_replaced_when_mailbox_has_password(): void
    {
        config(['mail.default' => 'array']);

        $account = new Email([
            'email' => 'student2@bansalimmigration.com.au',
            'password' => 'mailbox-secret',
        ]);

        $this->assertSame('array', (new EmailService)->composeMailerName($account));
    }
}
