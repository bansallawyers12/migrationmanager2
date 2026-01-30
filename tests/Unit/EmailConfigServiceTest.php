<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Email;
use App\Services\EmailConfigService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailConfigServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EmailConfigService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EmailConfigService();
    }

    /** @test */
    public function it_can_get_email_config_by_id()
    {
        $email = Email::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
            'display_name' => 'Test Sender',
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'status' => true
        ]);

        $config = $this->service->forAccountById($email->id);

        $this->assertEquals('smtp.example.com', $config['host']);
        $this->assertEquals(587, $config['port']);
        $this->assertEquals('tls', $config['encryption']);
        $this->assertEquals('test@example.com', $config['username']);
        $this->assertEquals('password123', $config['password']);
        $this->assertEquals('test@example.com', $config['from_address']);
        $this->assertEquals('Test Sender', $config['from_name']);
    }

    /** @test */
    public function it_can_get_email_config_by_email_address()
    {
        Email::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
            'display_name' => 'Test Sender',
            'status' => true
        ]);

        $config = $this->service->forAccount('test@example.com');

        $this->assertEquals('test@example.com', $config['username']);
        $this->assertEquals('test@example.com', $config['from_address']);
        $this->assertEquals('Test Sender', $config['from_name']);
    }

    /** @test */
    public function it_uses_default_zoho_settings_when_not_specified()
    {
        $email = Email::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
            'display_name' => 'Test Sender',
            'smtp_host' => null,
            'smtp_port' => null,
            'smtp_encryption' => null,
            'status' => true
        ]);

        $config = $this->service->forAccountById($email->id);

        $this->assertEquals('smtp.zoho.com', $config['host']);
        $this->assertEquals(587, $config['port']);
        $this->assertEquals('tls', $config['encryption']);
    }

    /** @test */
    public function it_throws_exception_when_email_not_found_by_id()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Email configuration not found for ID: 9999');

        $this->service->forAccountById(9999);
    }

    /** @test */
    public function it_throws_exception_when_email_not_found_by_address()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Email configuration not found for: nonexistent@example.com');

        $this->service->forAccount('nonexistent@example.com');
    }

    /** @test */
    public function it_only_finds_active_emails_when_searching_by_address()
    {
        Email::factory()->create([
            'email' => 'inactive@example.com',
            'status' => false // Inactive
        ]);

        $this->expectException(\Exception::class);

        $this->service->forAccount('inactive@example.com');
    }

    /** @test */
    public function it_can_apply_config_to_laravel_mail()
    {
        $config = [
            'host' => 'smtp.test.com',
            'port' => 465,
            'encryption' => 'ssl',
            'username' => 'user@test.com',
            'password' => 'secret',
            'from_address' => 'user@test.com',
            'from_name' => 'Test User'
        ];

        $this->service->applyConfig($config);

        $this->assertEquals('smtp.test.com', config('mail.mailers.smtp.host'));
        $this->assertEquals(465, config('mail.mailers.smtp.port'));
        $this->assertEquals('ssl', config('mail.mailers.smtp.encryption'));
        $this->assertEquals('user@test.com', config('mail.mailers.smtp.username'));
        $this->assertEquals('secret', config('mail.mailers.smtp.password'));
        $this->assertEquals('user@test.com', config('mail.from.address'));
        $this->assertEquals('Test User', config('mail.from.name'));
    }

    /** @test */
    public function it_can_get_all_active_accounts()
    {
        Email::factory()->create([
            'email' => 'active1@example.com',
            'status' => true
        ]);
        Email::factory()->create([
            'email' => 'active2@example.com',
            'status' => true
        ]);
        Email::factory()->create([
            'email' => 'inactive@example.com',
            'status' => false
        ]);

        $accounts = $this->service->getActiveAccounts();

        $this->assertCount(2, $accounts);
        $this->assertEquals('active1@example.com', $accounts[0]->email);
        $this->assertEquals('active2@example.com', $accounts[1]->email);
    }

    /** @test */
    public function it_can_get_default_account()
    {
        Email::factory()->create([
            'id' => 1,
            'email' => 'first@example.com',
            'status' => true
        ]);
        Email::factory()->create([
            'id' => 2,
            'email' => 'second@example.com',
            'status' => true
        ]);

        $config = $this->service->getDefaultAccount();

        $this->assertNotNull($config);
        $this->assertEquals('first@example.com', $config['from_address']);
    }

    /** @test */
    public function it_returns_null_when_no_active_accounts_exist()
    {
        Email::factory()->create([
            'email' => 'inactive@example.com',
            'status' => false
        ]);

        // Clear environment variables
        putenv('MAIL_FROM_ADDRESS');

        $config = $this->service->getDefaultAccount();

        $this->assertNull($config);
    }

    /** @test */
    public function it_falls_back_to_environment_config_when_no_active_accounts()
    {
        // Set environment variables
        putenv('MAIL_FROM_ADDRESS=env@example.com');
        putenv('MAIL_FROM_NAME=Environment Sender');
        putenv('MAIL_HOST=smtp.env.com');
        putenv('MAIL_PORT=2525');

        $config = $this->service->getDefaultAccount();

        $this->assertNotNull($config);
        $this->assertEquals('env@example.com', $config['from_address']);
        $this->assertEquals('Environment Sender', $config['from_name']);
        $this->assertEquals('smtp.env.com', $config['host']);
        $this->assertEquals(2525, $config['port']);

        // Clean up
        putenv('MAIL_FROM_ADDRESS');
        putenv('MAIL_FROM_NAME');
        putenv('MAIL_HOST');
        putenv('MAIL_PORT');
    }

    /** @test */
    public function build_config_creates_properly_structured_array()
    {
        $email = Email::factory()->create([
            'email' => 'test@example.com',
            'password' => 'secret123',
            'display_name' => 'Test User',
            'smtp_host' => 'smtp.custom.com',
            'smtp_port' => 465,
            'smtp_encryption' => 'ssl',
            'status' => true
        ]);

        $config = $this->service->forAccountById($email->id);

        $this->assertIsArray($config);
        $this->assertArrayHasKey('host', $config);
        $this->assertArrayHasKey('port', $config);
        $this->assertArrayHasKey('encryption', $config);
        $this->assertArrayHasKey('username', $config);
        $this->assertArrayHasKey('password', $config);
        $this->assertArrayHasKey('from_address', $config);
        $this->assertArrayHasKey('from_name', $config);
        $this->assertArrayHasKey('timeout', $config);
        $this->assertEquals(30, $config['timeout']);
    }
}

