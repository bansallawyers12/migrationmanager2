<?php

namespace Tests\Unit;

use App\Models\Staff;
use App\Services\CrmAccess\CrmAccessDeniedException;
use App\Services\CrmAccess\CrmAccessService;
use Tests\TestCase;

class CrmAccessServiceQuickOnlyTest extends TestCase
{
    // -----------------------------------------------------------------------
    // Role 14 (Calling Team) — supervisor path hard-blocked
    // -----------------------------------------------------------------------

    public function test_calling_team_cannot_request_supervisor_grant(): void
    {
        $staff = new Staff(['id' => 1, 'role' => 14, 'status' => 1]);
        $svc = new CrmAccessService();

        $this->expectException(CrmAccessDeniedException::class);
        $this->expectExceptionMessage('Your role only supports quick access.');

        $svc->requestSupervisorGrant($staff, 1, 'client', 1, null, '');
    }

    public function test_non_calling_team_role_may_reach_supervisor_validation(): void
    {
        // Role 13 (PA) passes the quick-only gate — but hits the office lookup next.
        // We confirm no CrmAccessDeniedException about "quick only" is thrown.
        $staff = new Staff(['id' => 2, 'role' => 13, 'status' => 1]);
        $svc = new CrmAccessService();

        try {
            $svc->requestSupervisorGrant($staff, 999, 'client', 9999, null, '');
        } catch (CrmAccessDeniedException $e) {
            $this->assertStringNotContainsString('quick access', $e->getMessage());
        } catch (\Throwable) {
            // DB exceptions expected in unit context — that is fine
        }

        $this->assertTrue(true);
    }

    // -----------------------------------------------------------------------
    // isExemptRole
    // -----------------------------------------------------------------------

    public function test_super_admin_is_exempt(): void
    {
        $staff = new Staff(['role' => 1]);
        $this->assertTrue((new CrmAccessService())->isExemptRole($staff));
    }

    public function test_admin_is_exempt(): void
    {
        $staff = new Staff(['role' => 17]);
        $this->assertTrue((new CrmAccessService())->isExemptRole($staff));
    }

    public function test_pa_is_not_exempt(): void
    {
        $staff = new Staff(['role' => 13]);
        $this->assertFalse((new CrmAccessService())->isExemptRole($staff));
    }

    // -----------------------------------------------------------------------
    // isApprover
    // -----------------------------------------------------------------------

    public function test_role_1_is_always_approver(): void
    {
        $staff = new Staff(['id' => 99999, 'role' => 1]);
        $this->assertTrue((new CrmAccessService())->isApprover($staff));
    }

    public function test_non_approver_non_admin_is_not_approver(): void
    {
        $staff = new Staff(['id' => 1, 'role' => 13]);
        // id 1 is not in configured approver list
        $this->assertFalse((new CrmAccessService())->isApprover($staff));
    }

    // -----------------------------------------------------------------------
    // hasActiveGrant — inactive staff always denied
    // -----------------------------------------------------------------------

    public function test_inactive_staff_always_has_no_active_grant(): void
    {
        $staff = new Staff(['id' => 1, 'role' => 13, 'status' => 0]);
        $this->assertFalse((new CrmAccessService())->hasActiveGrant($staff, 1));
    }

    // -----------------------------------------------------------------------
    // requestQuickGrant — quick_access_enabled guard
    // -----------------------------------------------------------------------

    public function test_quick_grant_blocked_when_flag_false(): void
    {
        $staff = new Staff(['id' => 1, 'role' => 13, 'status' => 1, 'quick_access_enabled' => false]);
        $svc = new CrmAccessService();

        $this->expectException(CrmAccessDeniedException::class);
        $this->expectExceptionMessage('Quick access is not enabled');

        $svc->requestQuickGrant($staff, 1, 'client', 1, null, 'calling');
    }

    public function test_quick_grant_blocked_with_invalid_reason(): void
    {
        $staff = new Staff(['id' => 1, 'role' => 13, 'status' => 1, 'quick_access_enabled' => true]);
        $svc = new CrmAccessService();

        $this->expectException(CrmAccessDeniedException::class);
        $this->expectExceptionMessage('Invalid reason');

        $svc->requestQuickGrant($staff, 1, 'client', 1, null, 'not_a_real_reason_code');
    }

    // -----------------------------------------------------------------------
    // StaffClientVisibility helpers (pure config-based, no DB)
    // -----------------------------------------------------------------------

    public function test_is_exempt_from_allocation_for_role_1(): void
    {
        $staff = new Staff(['role' => 1]);
        $this->assertTrue(\App\Support\StaffClientVisibility::isExemptFromAllocation($staff));
    }

    public function test_is_exempt_from_allocation_for_role_17(): void
    {
        $staff = new Staff(['role' => 17]);
        $this->assertTrue(\App\Support\StaffClientVisibility::isExemptFromAllocation($staff));
    }

    public function test_is_not_exempt_from_allocation_for_role_13(): void
    {
        $staff = new Staff(['role' => 13]);
        $this->assertFalse(\App\Support\StaffClientVisibility::isExemptFromAllocation($staff));
    }

    public function test_is_quick_access_only_for_role_14(): void
    {
        $staff = new Staff(['role' => 14]);
        $this->assertTrue(\App\Support\StaffClientVisibility::isQuickAccessOnly($staff));
    }

    public function test_is_not_quick_access_only_for_role_13(): void
    {
        $staff = new Staff(['role' => 13]);
        $this->assertFalse(\App\Support\StaffClientVisibility::isQuickAccessOnly($staff));
    }

    public function test_cross_access_ui_flags_for_exempt_role(): void
    {
        $staff = new Staff(['role' => 1]);
        $flags = \App\Support\StaffClientVisibility::crossAccessUiFlags($staff);
        $this->assertFalse($flags['show_quick']);
        $this->assertFalse($flags['show_supervisor']);
    }

    public function test_cross_access_ui_flags_for_calling_team_without_flag(): void
    {
        $staff = new Staff(['role' => 14, 'quick_access_enabled' => false]);
        $flags = \App\Support\StaffClientVisibility::crossAccessUiFlags($staff);
        $this->assertFalse($flags['show_quick']);
        $this->assertFalse($flags['show_supervisor']);
    }

    public function test_cross_access_ui_flags_for_calling_team_with_flag(): void
    {
        $staff = new Staff(['role' => 14, 'quick_access_enabled' => true]);
        $flags = \App\Support\StaffClientVisibility::crossAccessUiFlags($staff);
        $this->assertTrue($flags['show_quick']);
        $this->assertFalse($flags['show_supervisor']);
    }

    public function test_cross_access_ui_flags_for_standard_role_with_quick_enabled(): void
    {
        $staff = new Staff(['role' => 13, 'quick_access_enabled' => true]);
        $flags = \App\Support\StaffClientVisibility::crossAccessUiFlags($staff);
        $this->assertTrue($flags['show_quick']);
        $this->assertTrue($flags['show_supervisor']);
    }

    public function test_cross_access_ui_flags_for_standard_role_without_quick(): void
    {
        $staff = new Staff(['role' => 13, 'quick_access_enabled' => false]);
        $flags = \App\Support\StaffClientVisibility::crossAccessUiFlags($staff);
        $this->assertFalse($flags['show_quick']);
        $this->assertTrue($flags['show_supervisor']);
    }
}
