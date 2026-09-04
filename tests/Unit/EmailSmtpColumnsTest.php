<?php

namespace Tests\Unit;

use App\Models\Email;
use App\Services\EmailConfigService;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class EmailSmtpColumnsTest extends TestCase
{
    #[Test]
    public function smtp_fields_are_fillable_and_password_is_hidden(): void
    {
        $email = new Email([
            'email' => 'tr@bansalimmigration.com.au',
            'password' => 'mailbox-secret',
            'smtp_host' => 'smtp.zoho.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
        ]);

        $this->assertSame('smtp.zoho.com', $email->smtp_host);
        $this->assertSame(587, $email->smtp_port);
        $this->assertSame('tls', $email->smtp_encryption);
        $this->assertSame('mailbox-secret', $email->password);
        $this->assertArrayNotHasKey('password', $email->toArray());
    }

    #[Test]
    public function sender_config_ignores_smtp_columns(): void
    {
        $email = new Email([
            'email' => 'tr@bansalimmigration.com.au',
            'display_name' => 'TR',
            'email_signature' => '<p>Sig</p>',
            'password' => 'mailbox-secret',
            'smtp_host' => 'smtp.zoho.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
        ]);

        $method = new ReflectionMethod(EmailConfigService::class, 'buildConfig');
        $method->setAccessible(true);
        $config = $method->invoke(new EmailConfigService, $email);

        $this->assertSame([
            'from_address' => 'tr@bansalimmigration.com.au',
            'from_name' => 'TR',
            'email_signature' => '<p>Sig</p>',
        ], $config);
    }
}
