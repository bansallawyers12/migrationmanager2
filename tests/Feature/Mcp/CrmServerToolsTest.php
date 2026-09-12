<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Servers\CrmServer;
use App\Mcp\Tools\GetContactTool;
use App\Mcp\Tools\ListOverdueFollowUpsTool;
use App\Mcp\Tools\LogFollowUpTool;
use App\Mcp\Tools\LogNoteTool;
use App\Mcp\Tools\SearchContactsTool;
use App\Mcp\Tools\UpdateContactOrMatterStatusTool;
use App\Models\Admin;
use App\Models\Note;
use App\Models\Staff;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CrmServerToolsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->unsignedInteger('role')->default(0);
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country_code')->nullable();
            $table->string('client_id')->nullable();
            $table->unsignedTinyInteger('is_company')->default(0);
            $table->string('lead_status')->nullable();
            $table->timestamp('followup_date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->unsignedTinyInteger('is_archived')->default(0);
            $table->timestamps();
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('unique_group_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->unsignedTinyInteger('pin')->default(0);
            $table->timestamp('action_date')->nullable();
            $table->unsignedTinyInteger('is_action')->default(0);
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->string('status')->default('0');
            $table->string('task_group')->nullable();
            $table->unsignedBigInteger('matter_id')->nullable();
            $table->timestamp('note_deadline')->nullable();
            $table->timestamps();
        });

        Schema::create('activities_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->string('activity_type')->nullable();
            $table->string('source')->nullable();
            $table->string('use_for')->nullable();
            $table->timestamp('followup_date')->nullable();
            $table->string('task_group')->nullable();
            $table->unsignedTinyInteger('task_status')->default(0);
            $table->unsignedTinyInteger('pin')->default(0);
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('ABN_number')->nullable();
            $table->string('ACN')->nullable();
            $table->string('trading_name')->nullable();
            $table->timestamps();
        });

        Schema::create('client_matters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('workflow_id')->nullable();
            $table->unsignedBigInteger('workflow_stage_id')->nullable();
            $table->unsignedTinyInteger('matter_status')->default(1);
            $table->string('client_unique_matter_no')->nullable();
            $table->unsignedBigInteger('sel_matter_id')->nullable();
            $table->unsignedBigInteger('sel_migration_agent')->nullable();
            $table->unsignedBigInteger('sel_person_responsible')->nullable();
            $table->unsignedBigInteger('sel_person_assisting')->nullable();
            $table->date('deadline')->nullable();
            $table->string('decision_outcome')->nullable();
            $table->timestamps();
        });

        Schema::create('client_access_grants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('admin_id');
            $table->string('record_type')->nullable();
            $table->string('grant_type')->nullable();
            $table->string('access_type')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        config([
            'crm_access.strict_allocation' => false,
            'crm_access.exempt_role_ids' => [1, 17],
            'crm_access.exempt_staff_ids' => [],
            'crm.matter_discontinue_role_ids' => [1, 17, 16],
            'crm.super_admin_only_client_file_ids' => [],
        ]);
    }

    #[Test]
    public function crm_server_registers_expected_tools(): void
    {
        $defaults = (new \ReflectionClass(CrmServer::class))->getDefaultProperties();

        $this->assertSame([
            SearchContactsTool::class,
            GetContactTool::class,
            ListOverdueFollowUpsTool::class,
            LogNoteTool::class,
            LogFollowUpTool::class,
            UpdateContactOrMatterStatusTool::class,
        ], $defaults['tools']);
    }

    #[Test]
    public function log_note_tool_creates_note_and_activity_for_accessible_staff(): void
    {
        $staff = Staff::query()->create([
            'first_name' => 'Ajay',
            'last_name' => 'Agent',
            'email' => 'ajay-mcp-test@example.com',
            'password' => bcrypt('secret'),
            'role' => 1,
            'status' => 1,
        ]);

        Admin::query()->create([
            'id' => 100,
            'type' => 'client',
            'first_name' => 'Sam',
            'last_name' => 'Client',
            'email' => 'sam@example.com',
            'client_id' => 'TEST100',
            'is_company' => 0,
            'status' => 1,
            'user_id' => $staff->id,
        ]);

        $response = CrmServer::actingAs($staff)->tool(LogNoteTool::class, [
            'contact_id' => 100,
            'description' => 'Called about docs',
            'task_group' => 'Call',
        ]);

        $response->assertOk();
        $response->assertSee('Note logged successfully');

        $this->assertDatabaseHas('notes', [
            'client_id' => 100,
            'user_id' => $staff->id,
            'task_group' => 'Call',
            'is_action' => 0,
            'description' => 'Called about docs',
        ]);

        $this->assertDatabaseHas('activities_logs', [
            'client_id' => 100,
            'created_by' => $staff->id,
            'activity_type' => 'note',
        ]);
    }

    #[Test]
    public function list_overdue_scopes_non_super_admin_to_assigned_actions(): void
    {
        $staff = Staff::query()->create([
            'first_name' => 'Pat',
            'last_name' => 'Staff',
            'email' => 'pat-mcp@example.com',
            'password' => bcrypt('secret'),
            'role' => 12,
            'status' => 1,
        ]);

        $other = Staff::query()->create([
            'first_name' => 'Other',
            'last_name' => 'Staff',
            'email' => 'other-mcp@example.com',
            'password' => bcrypt('secret'),
            'role' => 12,
            'status' => 1,
        ]);

        Admin::query()->create([
            'id' => 200,
            'type' => 'client',
            'first_name' => 'Visible',
            'last_name' => 'Client',
            'client_id' => 'VIS200',
            'user_id' => $staff->id,
            'status' => 1,
        ]);

        Note::query()->create([
            'client_id' => 200,
            'user_id' => $staff->id,
            'assigned_to' => $staff->id,
            'title' => 'Mine',
            'description' => 'mine',
            'type' => 'client',
            'is_action' => 1,
            'status' => '0',
            'task_group' => 'Call',
            'action_date' => now()->subDay(),
            'pin' => 0,
        ]);

        Note::query()->create([
            'client_id' => 200,
            'user_id' => $other->id,
            'assigned_to' => $other->id,
            'title' => 'Theirs',
            'description' => 'theirs',
            'type' => 'client',
            'is_action' => 1,
            'status' => '0',
            'task_group' => 'Follow Up',
            'action_date' => now()->subDay(),
            'pin' => 0,
        ]);

        $response = CrmServer::actingAs($staff)->tool(ListOverdueFollowUpsTool::class, [
            'limit' => 20,
        ]);

        $response->assertOk();
        $response->assertSee('Mine');
        $response->assertSee('assigned_to_me');
        $response->assertDontSee('Theirs');
    }

    #[Test]
    public function get_contact_denies_inaccessible_record_for_restricted_staff(): void
    {
        config(['crm_access.strict_allocation' => true]);

        $staff = Staff::query()->create([
            'first_name' => 'Restricted',
            'last_name' => 'PA',
            'email' => 'pa-mcp@example.com',
            'password' => bcrypt('secret'),
            'role' => 13,
            'status' => 1,
        ]);

        Admin::query()->create([
            'id' => 300,
            'type' => 'client',
            'first_name' => 'Hidden',
            'last_name' => 'Client',
            'client_id' => 'HID300',
            'user_id' => 999,
            'status' => 1,
        ]);

        $response = CrmServer::actingAs($staff)->tool(GetContactTool::class, [
            'contact_id' => 300,
        ]);

        $response->assertHasErrors(['You do not have access to this client or lead.']);
    }
}
