<?php

namespace Tests\Unit\Services;

use App\Services\BansalAppointmentSync\ConsultantAssignmentService;
use PHPUnit\Framework\TestCase;

class ConsultantAssignmentServiceMelbournePunjabiTest extends TestCase
{
    private function calendarType(array $data): ?string
    {
        $svc = new class extends ConsultantAssignmentService {
            public function exposeDetermine(array $a): ?string
            {
                return $this->determineCalendarType($a);
            }
        };

        return $svc->exposeDetermine($data);
    }

    public function test_melbourne_punjabi_gsm_free_goes_jrp(): void
    {
        $this->assertSame('jrp', $this->calendarType([
            'noe_id' => 1,
            'location' => 'melbourne',
            'inperson_address' => 2,
            'service_id' => 2,
            'specific_service' => 'consultation',
            'preferred_language' => 'Punjabi',
            'service_type' => 'GSM Visas: 491, 190, 189, 191',
        ]));
    }

    public function test_melbourne_punjabi_eoi_free_goes_jrp(): void
    {
        $this->assertSame('jrp', $this->calendarType([
            'noe_id' => 9,
            'location' => 'melbourne',
            'inperson_address' => 2,
            'service_id' => 2,
            'specific_service' => 'consultation',
            'preferred_language' => 'punjabi',
            'service_type' => 'EOI/ROI',
        ]));
    }

    public function test_melbourne_punjabi_gsm_paid_goes_employer_sponsored(): void
    {
        $this->assertSame('paid', $this->calendarType([
            'noe_id' => 1,
            'location' => 'melbourne',
            'inperson_address' => 2,
            'service_id' => 1,
            'specific_service' => 'paid-consultation',
            'preferred_language' => 'Punjabi',
            'service_type' => 'GSM Visas: 491, 190, 189, 191',
        ]));
    }

    public function test_melbourne_english_gsm_free_still_kunal(): void
    {
        $this->assertSame('kunal', $this->calendarType([
            'noe_id' => 1,
            'location' => 'melbourne',
            'inperson_address' => 2,
            'service_id' => 2,
            'specific_service' => 'consultation',
            'preferred_language' => 'English',
            'service_type' => 'GSM Visas: 491, 190, 189, 191',
        ]));
    }

    public function test_adelaide_punjabi_gsm_free_still_adelaide(): void
    {
        $this->assertSame('adelaide', $this->calendarType([
            'noe_id' => 1,
            'location' => 'adelaide',
            'inperson_address' => 1,
            'service_id' => 2,
            'specific_service' => 'consultation',
            'preferred_language' => 'Punjabi',
            'service_type' => 'GSM Visas: 491, 190, 189, 191',
        ]));
    }

    public function test_melbourne_punjabi_tourist_unchanged(): void
    {
        $this->assertSame('tourist', $this->calendarType([
            'noe_id' => 4,
            'location' => 'melbourne',
            'inperson_address' => 2,
            'service_id' => 2,
            'specific_service' => 'consultation',
            'preferred_language' => 'Punjabi',
            'service_type' => 'Tourist Visa',
        ]));
    }
}
