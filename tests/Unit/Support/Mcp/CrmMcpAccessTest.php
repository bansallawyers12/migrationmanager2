<?php

namespace Tests\Unit\Support\Mcp;

use App\Models\Staff;
use App\Support\Mcp\CrmMcpAccess;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CrmMcpAccessTest extends TestCase
{
    #[Test]
    public function is_super_admin_only_when_role_is_one(): void
    {
        $super = new Staff(['role' => 1]);
        $admin = new Staff(['role' => 17]);
        $agent = new Staff(['role' => 16]);

        $this->assertTrue(CrmMcpAccess::isSuperAdmin($super));
        $this->assertFalse(CrmMcpAccess::isSuperAdmin($admin));
        $this->assertFalse(CrmMcpAccess::isSuperAdmin($agent));
    }

    #[Test]
    public function may_discontinue_uses_crm_config_roles(): void
    {
        config(['crm.matter_discontinue_role_ids' => [1, 17, 16]]);

        $this->assertTrue(CrmMcpAccess::mayDiscontinueOrReopenMatter(new Staff(['role' => 1])));
        $this->assertTrue(CrmMcpAccess::mayDiscontinueOrReopenMatter(new Staff(['role' => 17])));
        $this->assertTrue(CrmMcpAccess::mayDiscontinueOrReopenMatter(new Staff(['role' => 16])));
        $this->assertFalse(CrmMcpAccess::mayDiscontinueOrReopenMatter(new Staff(['role' => 13])));
    }

    #[Test]
    public function staff_display_name_falls_back_to_id(): void
    {
        $named = new Staff(['first_name' => 'Ajay', 'last_name' => 'Test']);
        $named->id = 42;

        $blank = new Staff(['first_name' => '', 'last_name' => '']);
        $blank->id = 7;

        $this->assertSame('Ajay Test', CrmMcpAccess::staffDisplayName($named));
        $this->assertSame('Staff #7', CrmMcpAccess::staffDisplayName($blank));
    }
}
