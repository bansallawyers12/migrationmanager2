<?php

namespace Tests\Unit\Services;

use App\Services\CompanyAgreementDocxPatcher;
use Tests\TestCase;

class CompanyAgreementDocxPatcherTest extends TestCase
{
    public function test_fixes_business_address_street_placeholder_typo(): void
    {
        $xml = '<w:t>${BusinessAddressStreet1and2)</w:t><w:t>${BusinessAddressSuburbPostcodeState}</w:t>';
        $patch = (new CompanyAgreementDocxPatcher())->patchDocumentXml($xml);

        $this->assertTrue($patch['patched']);
        $this->assertStringContainsString('${BusinessAddressStreet1and2}', $patch['xml']);
        $this->assertStringNotContainsString('${BusinessAddressStreet1and2)', $patch['xml']);
    }

    public function test_no_op_when_typo_not_present(): void
    {
        $xml = '<w:t>${BusinessAddressStreet1and2}</w:t>';
        $patch = (new CompanyAgreementDocxPatcher())->patchDocumentXml($xml);

        $this->assertFalse($patch['patched']);
        $this->assertSame($xml, $patch['xml']);
    }

    public function test_recognises_company_template_filenames(): void
    {
        $this->assertTrue(CompanyAgreementDocxPatcher::isCompanyAgreementTemplate('Service_Agreement_company_sponsorship.docx'));
        $this->assertTrue(CompanyAgreementDocxPatcher::isCompanyAgreementTemplate('Service_Agreement_company_nomination.docx'));
        $this->assertFalse(CompanyAgreementDocxPatcher::isCompanyAgreementTemplate('Service_Agreement_general.docx'));
    }
}
