<?php

namespace Tests\Unit\Services;

use App\Services\VisaAgreementApplicantAddressResolver;
use Tests\TestCase;

class VisaAgreementApplicantAddressResolverTest extends TestCase
{
    private VisaAgreementApplicantAddressResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new VisaAgreementApplicantAddressResolver();
    }

    public function test_psa_structured_address_excludes_postcode_from_street_macro(): void
    {
        $row = (object) [
            'address_line_1' => '10 Olive Tree Court',
            'address_line_2' => '',
            'suburb' => 'Kilmore',
            'state' => 'VIC',
            'zip' => '3764',
            'address' => '10 Olive Tree Court, Kilmore, VIC, 3764',
        ];

        $resolved = $this->resolver->resolveForTemplate($row, 'Service_Agreement_PSA.docx');

        $this->assertSame('10 Olive Tree Court, Kilmore, VIC', $resolved['street']);
        $this->assertSame('3764', $resolved['postcode']);
    }

    public function test_psa_legacy_combined_address_strips_trailing_postcode(): void
    {
        $row = (object) [
            'address' => '10 Olive Tree Court, Kilmore, VIC, 3764',
            'zip' => '3764',
        ];

        $resolved = $this->resolver->resolveForTemplate($row, 'Service_Agreement_PSA.docx');

        $this->assertSame('10 Olive Tree Court, Kilmore, VIC', $resolved['street']);
        $this->assertSame('3764', $resolved['postcode']);
    }

    public function test_psa_legacy_address_without_postcode_in_combined_field_is_unchanged(): void
    {
        $row = (object) [
            'address' => '10 Olive Tree Court, Kilmore, VIC',
            'zip' => '3764',
        ];

        $resolved = $this->resolver->resolveForTemplate($row, 'Service_Agreement_PSA.docx');

        $this->assertSame('10 Olive Tree Court, Kilmore, VIC', $resolved['street']);
        $this->assertSame('3764', $resolved['postcode']);
    }

    public function test_non_psa_template_keeps_existing_combined_address_behavior(): void
    {
        $row = (object) [
            'address_line_1' => '10 Olive Tree Court',
            'suburb' => 'Kilmore',
            'state' => 'VIC',
            'zip' => '3764',
            'address' => '10 Olive Tree Court, Kilmore, VIC, 3764',
        ];

        $resolved = $this->resolver->resolveForTemplate($row, 'Service_Agreement_general.docx');

        $this->assertSame('10 Olive Tree Court, Kilmore, VIC, 3764', $resolved['street']);
        $this->assertSame('3764', $resolved['postcode']);
    }

    public function test_null_address_row_returns_null_macros(): void
    {
        $resolved = $this->resolver->resolveForTemplate(null, 'Service_Agreement_PSA.docx');

        $this->assertNull($resolved['street']);
        $this->assertNull($resolved['postcode']);
    }
}
