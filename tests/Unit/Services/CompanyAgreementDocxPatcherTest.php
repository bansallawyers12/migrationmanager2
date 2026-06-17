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

    public function test_adds_saf_levy_rate_to_charge_type_label_for_company_nomination_template(): void
    {
        $path = storage_path('app/templates/Service_Agreement_company_nomination.docx');
        if (! is_file($path)) {
            $this->markTestSkipped('Service_Agreement_company_nomination.docx not present in storage/app/templates');
        }

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($path));
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        $this->assertNotFalse($xml);

        $patch = (new CompanyAgreementDocxPatcher())->patchDocumentXml(
            $xml,
            'Service_Agreement_company_nomination.docx'
        );

        $this->assertTrue($patch['patched']);
        $this->assertContains('saf_levy_charge_type_label_rate', $patch['fixes']);
        $this->assertSame(1, substr_count($patch['xml'], '>Skilling Australians Fund (SAF) Levy ($1200 per year)</w:t>'));
        $this->assertSame(0, substr_count($patch['xml'], '>Skilling Australians Fund (SAF) Levy</w:t>'));
        $this->assertStringContainsString(
            'Skilling Australians Fund (SAF) Levy: The SAF Levy is a government levy',
            $patch['xml']
        );
    }

    public function test_saf_levy_rate_patch_is_idempotent_for_company_nomination_template(): void
    {
        $path = storage_path('app/templates/Service_Agreement_company_nomination.docx');
        if (! is_file($path)) {
            $this->markTestSkipped('Service_Agreement_company_nomination.docx not present in storage/app/templates');
        }

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($path));
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        $this->assertNotFalse($xml);

        $patcher = new CompanyAgreementDocxPatcher();
        $first = $patcher->patchDocumentXml($xml, 'Service_Agreement_company_nomination.docx');
        $second = $patcher->patchDocumentXml($first['xml'], 'Service_Agreement_company_nomination.docx');

        $this->assertTrue($first['patched']);
        $this->assertFalse($second['patched']);
        $this->assertSame($first['xml'], $second['xml']);
    }

    public function test_does_not_patch_saf_levy_label_for_company_sponsorship_template(): void
    {
        $xml = '<w:t>Skilling Australians Fund (SAF) Levy</w:t>';
        $patch = (new CompanyAgreementDocxPatcher())->patchDocumentXml(
            $xml,
            'Service_Agreement_company_sponsorship.docx'
        );

        $this->assertFalse($patch['patched']);
        $this->assertSame($xml, $patch['xml']);
    }

    public function test_adds_page_breaks_before_sections_three_and_five_for_company_nomination_template(): void
    {
        $path = storage_path('app/templates/Service_Agreement_company_nomination.docx');
        if (! is_file($path)) {
            $this->markTestSkipped('Service_Agreement_company_nomination.docx not present in storage/app/templates');
        }

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($path));
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        $this->assertNotFalse($xml);

        $patch = (new CompanyAgreementDocxPatcher())->patchDocumentXml(
            $xml,
            'Service_Agreement_company_nomination.docx'
        );

        $this->assertTrue($patch['patched']);
        $this->assertContains('nomination_section_page_breaks', $patch['fixes']);
        $this->assertSame(1, $this->pageBreakBeforeCountForHeading($patch['xml'], '3. Other Costs'));
        $this->assertSame(1, $this->pageBreakBeforeCountForHeading($patch['xml'], '5. Payment Structure'));
    }

    public function test_section_page_break_patch_is_idempotent_for_company_nomination_template(): void
    {
        $path = storage_path('app/templates/Service_Agreement_company_nomination.docx');
        if (! is_file($path)) {
            $this->markTestSkipped('Service_Agreement_company_nomination.docx not present in storage/app/templates');
        }

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($path));
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        $this->assertNotFalse($xml);

        $patcher = new CompanyAgreementDocxPatcher();
        $first = $patcher->patchDocumentXml($xml, 'Service_Agreement_company_nomination.docx');
        $second = $patcher->patchDocumentXml($first['xml'], 'Service_Agreement_company_nomination.docx');

        $this->assertSame(1, $this->pageBreakBeforeCountForHeading($first['xml'], '3. Other Costs'));
        $this->assertSame(1, $this->pageBreakBeforeCountForHeading($first['xml'], '5. Payment Structure'));
        $this->assertSame(1, $this->pageBreakBeforeCountForHeading($second['xml'], '3. Other Costs'));
        $this->assertSame(1, $this->pageBreakBeforeCountForHeading($second['xml'], '5. Payment Structure'));
    }

    public function test_does_not_add_nomination_section_page_breaks_for_company_sponsorship_template(): void
    {
        $paragraph = '<w:p><w:pPr><w:spacing w:before="240"/></w:pPr><w:r><w:t>3. Other Costs</w:t></w:r></w:p>';
        $xml = '<w:document><w:body>' . $paragraph . '</w:body></w:document>';

        $patch = (new CompanyAgreementDocxPatcher())->patchDocumentXml(
            $xml,
            'Service_Agreement_company_sponsorship.docx'
        );

        $this->assertFalse(str_contains($patch['xml'], '<w:pageBreakBefore'));
    }

    private function pageBreakBeforeCountForHeading(string $xml, string $heading): int
    {
        $count = 0;
        preg_match_all('/<w:p\b.*?<\/w:p>/s', $xml, $paragraphMatches);
        foreach ($paragraphMatches[0] as $paragraph) {
            $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $paragraph))));
            if ($text !== $heading) {
                continue;
            }

            if (preg_match('/<w:pageBreakBefore\b/', $paragraph)) {
                $count++;
            }
        }

        return $count;
    }
}
