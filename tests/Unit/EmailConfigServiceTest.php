<?php

namespace Tests\Unit;

use App\Models\Branch;
use App\Models\Email;
use App\Models\Matter;
use App\Services\EmailConfigService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailConfigServiceTest extends TestCase
{
    protected EmailConfigService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createSchema();
        Email::query()->delete();
        DB::table('client_matters')->delete();
        $this->service = new EmailConfigService;
    }

    /** @test */
    public function it_can_get_email_config_by_email_address()
    {
        Email::factory()->create([
            'email' => 'test@example.com',
            'display_name' => 'Test Sender',
            'status' => true,
        ]);

        $config = $this->service->forAccount('test@example.com');

        $this->assertEquals('test@example.com', $config['from_address']);
        $this->assertEquals('Test Sender', $config['from_name']);
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
            'status' => false, // Inactive
        ]);

        $this->expectException(\Exception::class);

        $this->service->forAccount('inactive@example.com');
    }

    /** @test */
    public function it_can_get_all_active_accounts()
    {
        Email::factory()->create([
            'email' => 'active1@example.com',
            'status' => true,
        ]);
        Email::factory()->create([
            'email' => 'active2@example.com',
            'status' => true,
        ]);
        Email::factory()->create([
            'email' => 'inactive@example.com',
            'status' => false,
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
            'status' => true,
        ]);
        Email::factory()->create([
            'id' => 2,
            'email' => 'second@example.com',
            'status' => true,
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
            'status' => false,
        ]);

        config(['mail.from.address' => null, 'mail.from.name' => null]);

        $config = $this->service->getDefaultAccount();

        $this->assertNull($config);
    }

    #[Test]
    public function it_falls_back_to_environment_config_when_no_active_accounts()
    {
        config([
            'mail.from.address' => 'env@example.com',
            'mail.from.name' => 'Environment Sender',
        ]);

        $config = $this->service->getDefaultAccount();

        $this->assertNotNull($config);
        $this->assertEquals('env@example.com', $config['from_address']);
        $this->assertEquals('Environment Sender', $config['from_name']);
    }

    /** @test */
    public function build_config_returns_only_from_address_from_name_and_email_signature()
    {
        $email = Email::factory()->create([
            'email' => 'test@example.com',
            'display_name' => 'Test User',
            'email_signature' => '<p>Signature</p>',
            'status' => true,
        ]);

        $config = $this->service->forAccountById($email->id);

        $this->assertIsArray($config);
        $this->assertCount(3, $config);
        $this->assertArrayHasKey('from_address', $config);
        $this->assertArrayHasKey('from_name', $config);
        $this->assertArrayHasKey('email_signature', $config);
        $this->assertEquals('test@example.com', $config['from_address']);
        $this->assertEquals('Test User', $config['from_name']);
        $this->assertEquals('<p>Signature</p>', $config['email_signature']);
    }

    #[Test]
    public function get_default_account_falls_back_to_mail_from_address_env_var()
    {
        config([
            'mail.from.address' => 'fallback@example.com',
            'mail.from.name' => 'Fallback Sender',
        ]);

        $config = $this->service->getDefaultAccount();

        $this->assertNotNull($config);
        $this->assertEquals('fallback@example.com', $config['from_address']);
        $this->assertEquals('Fallback Sender', $config['from_name']);
    }

    #[Test]
    public function get_eoi_send_context_keeps_admin_sender_for_melbourne(): void
    {
        config(['mail.default' => 'failover']);

        Email::query()->create([
            'email' => 'admin@bansalimmigration.com.au',
            'display_name' => 'Admin Sender',
            'status' => true,
            'password' => 'admin-secret',
        ]);
        Email::query()->create([
            'email' => 'Adelaide@bansalimmigration.com.au',
            'display_name' => 'Adelaide Office',
            'status' => true,
            'password' => 'adelaide-secret',
            'smtp_host' => 'smtp.zoho.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
        ]);

        $this->createEoiMatter(2608774, 'MELBOURNE');

        $context = $this->service->getEoiSendContext(2608774);

        $this->assertSame('admin@bansalimmigration.com.au', $context['from']['from_address']);
        $this->assertNull($context['mailer']);
        $this->assertSame(['ses', 'zoho'], config('mail.mailers.failover.mailers'));
    }

    #[Test]
    public function get_eoi_send_context_uses_adelaide_mailbox_failover(): void
    {
        config(['mail.default' => 'failover']);

        Email::query()->create([
            'email' => 'admin@bansalimmigration.com.au',
            'display_name' => 'Admin Sender',
            'status' => true,
            'password' => 'admin-secret',
        ]);
        Email::query()->create([
            'email' => 'Adelaide@bansalimmigration.com.au',
            'display_name' => 'Adelaide Office',
            'status' => true,
            'password' => 'adelaide-secret',
            'smtp_host' => 'smtp.zoho.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
        ]);

        $this->createEoiMatter(2608773, 'ADELAIDE');

        $context = $this->service->getEoiSendContext(2608773);

        $this->assertSame('Adelaide@bansalimmigration.com.au', $context['from']['from_address']);
        $this->assertNotNull($context['mailer']);
        $this->assertNotSame('failover', $context['mailer']);

        $chain = config("mail.mailers.{$context['mailer']}.mailers");
        $this->assertSame('ses', $chain[0]);
        $this->assertSame('Adelaide@bansalimmigration.com.au', config("mail.mailers.{$chain[1]}.username"));
        $this->assertSame('adelaide-secret', config("mail.mailers.{$chain[1]}.password"));
        $this->assertSame(['ses', 'zoho'], config('mail.mailers.failover.mailers'));
    }

    #[Test]
    public function get_eoi_from_account_is_unchanged_without_client(): void
    {
        Email::query()->create([
            'email' => 'admin@bansalimmigration.com.au',
            'display_name' => 'Admin Sender',
            'status' => true,
        ]);

        $config = $this->service->getEoiFromAccount();

        $this->assertSame('admin@bansalimmigration.com.au', $config['from_address']);
    }

    private function createEoiMatter(int $clientId, string $officeName): int
    {
        $office = Branch::query()->create(['office_name' => $officeName]);
        $eoiType = Matter::query()->firstOrCreate(
            ['nick_name' => 'eoi'],
            ['title' => 'Expression Of Interest']
        );

        return (int) DB::table('client_matters')->insertGetId([
            'client_id' => $clientId,
            'office_id' => $office->id,
            'sel_matter_id' => $eoiType->id,
            'matter_status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createSchema(): void
    {
        if (! Schema::hasTable('emails')) {
            Schema::create('emails', function (Blueprint $table) {
                $table->id();
                $table->string('email')->nullable();
                $table->string('display_name')->nullable();
                $table->boolean('status')->default(true);
                $table->text('email_signature')->nullable();
                $table->string('password')->nullable();
                $table->string('smtp_host')->nullable();
                $table->unsignedInteger('smtp_port')->nullable();
                $table->string('smtp_encryption')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        } else {
            foreach (['password', 'smtp_host', 'smtp_encryption'] as $column) {
                if (! Schema::hasColumn('emails', $column)) {
                    Schema::table('emails', function (Blueprint $table) use ($column) {
                        $table->string($column)->nullable();
                    });
                }
            }
            if (! Schema::hasColumn('emails', 'smtp_port')) {
                Schema::table('emails', function (Blueprint $table) {
                    $table->unsignedInteger('smtp_port')->nullable();
                });
            }
        }

        if (! Schema::hasTable('branches')) {
            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->string('office_name')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('matters')) {
            Schema::create('matters', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('nick_name')->nullable();
                $table->timestamps();
            });
        } elseif (! Schema::hasColumn('matters', 'nick_name')) {
            Schema::table('matters', function (Blueprint $table) {
                $table->string('nick_name')->nullable();
            });
        }

        if (! Schema::hasTable('client_matters')) {
            Schema::create('client_matters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('office_id')->nullable();
                $table->unsignedBigInteger('sel_matter_id')->nullable();
                $table->unsignedTinyInteger('matter_status')->nullable();
                $table->timestamps();
            });
        } else {
            if (! Schema::hasColumn('client_matters', 'office_id')) {
                Schema::table('client_matters', function (Blueprint $table) {
                    $table->unsignedBigInteger('office_id')->nullable();
                });
            }
            if (! Schema::hasColumn('client_matters', 'sel_matter_id')) {
                Schema::table('client_matters', function (Blueprint $table) {
                    $table->unsignedBigInteger('sel_matter_id')->nullable();
                });
            }
        }
    }
}
