<?php

namespace Tests\Unit\Support;

use App\Support\BansalSchedulingServiceType;
use PHPUnit\Framework\TestCase;

class BansalSchedulingServiceTypeTest extends TestCase
{
    public function test_melbourne_family_visas_uses_employer_sponsored_timeslots(): void
    {
        $this->assertSame(
            'employer-sponsored',
            BansalSchedulingServiceType::fromEnquiryItem(11, 'melbourne')
        );
    }

    public function test_melbourne_citizenship_uses_employer_sponsored_timeslots(): void
    {
        $this->assertSame(
            'employer-sponsored',
            BansalSchedulingServiceType::fromEnquiryItem(12, 'melbourne')
        );
    }

    public function test_adelaide_family_visas_keeps_own_service_type(): void
    {
        $this->assertSame(
            'family-visas',
            BansalSchedulingServiceType::fromEnquiryItem(11, 'adelaide')
        );
    }

    public function test_adelaide_citizenship_keeps_own_service_type(): void
    {
        $this->assertSame(
            'citizenship',
            BansalSchedulingServiceType::fromEnquiryItem(12, 'adelaide')
        );
    }

    public function test_employer_sponsored_unchanged_for_melbourne(): void
    {
        $this->assertSame(
            'employer-sponsored',
            BansalSchedulingServiceType::fromEnquiryItem(10, 'melbourne')
        );
    }

    public function test_melbourne_family_visas_bansal_sync_uses_employer_sponsored_enquiry_type(): void
    {
        $this->assertSame(
            'employer_sponsored',
            BansalSchedulingServiceType::bansalEnquiryTypeForApi(11, 'melbourne', 'family_visas')
        );
    }

    public function test_melbourne_citizenship_bansal_sync_uses_employer_sponsored_enquiry_type(): void
    {
        $this->assertSame(
            'employer_sponsored',
            BansalSchedulingServiceType::bansalEnquiryTypeForApi(12, 'melbourne', 'citizenship')
        );
    }

    public function test_adelaide_family_visas_bansal_sync_keeps_crm_enquiry_type(): void
    {
        $this->assertSame(
            'family_visas',
            BansalSchedulingServiceType::bansalEnquiryTypeForApi(11, 'adelaide', 'family_visas')
        );
    }

    public function test_melbourne_tr_bansal_sync_unchanged(): void
    {
        $this->assertSame(
            'tr',
            BansalSchedulingServiceType::bansalEnquiryTypeForApi(2, 'melbourne', 'tr')
        );
    }
}
