<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureAdminConsoleAccess;
use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Email;
use App\Models\Staff;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminConsoleEmailManagementTest extends TestCase
{
    protected Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
            EnsureAdminConsoleAccess::class,
        ]);

        if (method_exists($this, 'withoutVite')) {
            $this->withoutVite();
        }

        $this->createEmailManagementSchema();

        config([
            'services.ses_crm.from_allowed_domains' => 'bansalimmigration.com.au',
            'services.ses_crm.senders' => '',
            'mail.from.address' => '',
            'mail.noreply.address' => '',
            'mail.info.address' => '',
        ]);

        $this->staff = Staff::query()->firstOrCreate(
            ['email' => 'admin-console-emails@test.com'],
            [
                'first_name' => 'Console',
                'last_name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 1,
                'status' => 1,
            ]
        );
    }

    #[Test]
    public function index_and_forms_expose_create_edit_and_password_fields_without_storing_password_in_html(): void
    {
        $index = file_get_contents($this->projectPath('resources/views/AdminConsole/features/emails/index.blade.php'));
        $create = file_get_contents($this->projectPath('resources/views/AdminConsole/features/emails/create.blade.php'));
        $edit = file_get_contents($this->projectPath('resources/views/AdminConsole/features/emails/edit.blade.php'));

        $this->assertNotFalse($index);
        $this->assertNotFalse($create);
        $this->assertNotFalse($edit);
        $this->assertStringContainsString("route('adminconsole.features.emails.create')", $index);
        $this->assertStringContainsString("route('adminconsole.features.emails.edit'", $index);
        $this->assertStringContainsString('emails-list-table', $index);
        $this->assertStringContainsString('dropdown-open', $index);
        $this->assertStringContainsString('show.bs.dropdown', $index);
        $this->assertStringContainsString('data-bs-display="static"', $index);
        $this->assertStringNotContainsString('<th>Password</th>', $index);
        $this->assertStringNotContainsString('has_password', $index);
        $this->assertStringNotContainsString('{{ $list->password }}', $index);
        $this->assertStringContainsString('name="password"', $create);
        $this->assertStringContainsString('name="password"', $edit);
        $this->assertStringContainsString('Leave blank to keep the current password', $edit);
    }

    #[Test]
    public function store_saves_plaintext_zoho_password(): void
    {
        $response = $this->actingAs($this->staff, 'admin')
            ->post(route('adminconsole.features.emails.store'), [
                'email' => 'student@bansalimmigration.com.au',
                'display_name' => 'Student',
                'status' => '1',
                'password' => 'zoho-mailbox-pass',
                'users' => [(string) $this->staff->id],
            ]);

        $response->assertRedirect(route('adminconsole.features.emails.index'));

        $email = Email::query()->where('email', 'student@bansalimmigration.com.au')->first();
        $this->assertNotNull($email);
        $this->assertSame('zoho-mailbox-pass', $email->password);
        $this->assertFalse(Hash::check('zoho-mailbox-pass', (string) $email->password));
    }

    #[Test]
    public function update_changes_password_only_when_provided(): void
    {
        $email = Email::query()->create([
            'email' => 'visitor@bansalimmigration.com.au',
            'display_name' => 'Visitor',
            'status' => true,
            'password' => 'keep-this-password',
            'user_id' => json_encode([(string) $this->staff->id]),
        ]);

        $this->actingAs($this->staff, 'admin')
            ->put(route('adminconsole.features.emails.update', $email->id), [
                'email' => 'visitor@bansalimmigration.com.au',
                'display_name' => 'Visitor Desk',
                'status' => '1',
                'password' => '',
                'users' => [(string) $this->staff->id],
            ])
            ->assertRedirect(route('adminconsole.features.emails.index'));

        $email->refresh();
        $this->assertSame('keep-this-password', $email->password);
        $this->assertSame('Visitor Desk', $email->display_name);

        $this->actingAs($this->staff, 'admin')
            ->put(route('adminconsole.features.emails.update', $email->id), [
                'email' => 'visitor@bansalimmigration.com.au',
                'display_name' => 'Visitor Desk',
                'status' => '1',
                'password' => 'new-zoho-pass',
                'users' => [(string) $this->staff->id],
            ])
            ->assertRedirect(route('adminconsole.features.emails.index'));

        $email->refresh();
        $this->assertSame('new-zoho-pass', $email->password);
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }

    private function createEmailManagementSchema(): void
    {
        if (! Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->unsignedInteger('role')->nullable();
                $table->unsignedInteger('status')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('emails')) {
            Schema::create('emails', function (Blueprint $table) {
                $table->id();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->string('smtp_host')->nullable()->default('smtp.zoho.com');
                $table->integer('smtp_port')->nullable()->default(587);
                $table->string('smtp_encryption', 10)->nullable()->default('tls');
                $table->string('display_name')->nullable();
                $table->boolean('status')->default(true);
                $table->text('email_signature')->nullable();
                $table->text('user_id')->nullable();
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
                    $table->integer('smtp_port')->nullable();
                });
            }
        }
    }
}
