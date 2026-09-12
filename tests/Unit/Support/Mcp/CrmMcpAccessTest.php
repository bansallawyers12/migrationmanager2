<?php

namespace Tests\Unit\Support\Mcp;

use App\Models\Staff;
use App\Support\Mcp\CrmMcpAccess;
use Carbon\Carbon;
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
    public function format_date_time_accepts_plain_strings_without_casts(): void
    {
        $this->assertSame(
            '2026-09-01 10:30:00',
            CrmMcpAccess::formatDateTime('2026-09-01 10:30:00')
        );
        $this->assertSame(
            '2026-09-01 10:30:00',
            CrmMcpAccess::formatDateTime(Carbon::parse('2026-09-01 10:30:00'))
        );
        $this->assertNull(CrmMcpAccess::formatDateTime(null));
        $this->assertNull(CrmMcpAccess::formatDateTime(''));
    }
}
