<?php

namespace Tests\Unit\Services;

use App\Services\PsaAgreementSection4SummaryTablePatcher;
use App\Services\VisaAgreementAmountTablePatcher;
use Tests\TestCase;
use ZipArchive;

class PsaAgreementSection4SummaryTablePatcherTest extends TestCase
{
    private PsaAgreementSection4SummaryTablePatcher $patcher;

    private string $psaTemplatePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->patcher = new PsaAgreementSection4SummaryTablePatcher();
        $this->psaTemplatePath = storage_path('app/templates/Service_Agreement_PSA.docx');
    }

    public function test_patches_authority_charges_row_to_single_dollar_and_right_alignment(): void
    {
        if (! is_file($this->psaTemplatePath)) {
            $this->markTestSkipped('Service_Agreement_PSA.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->psaTemplatePath);
        $xml = (new VisaAgreementAmountTablePatcher())->patchDocumentXml($xml)['xml'];
        $before = $this->authorityChargesRowStats($xml);

        $this->assertSame('center', $before['jc']);
        $this->assertFalse($before['single_placeholder']);
        $this->assertGreaterThan(0, $before['literal_dollar_runs']);

        $result = $this->patcher->patchDocumentXml($xml);
        $after = $this->authorityChargesRowStats($result['xml']);

        $this->assertTrue($result['patched']);
        $this->assertSame('right', $after['jc']);
        $this->assertTrue($after['single_placeholder']);
        $this->assertSame('TotalDoHAChargesInclSurcharge', $after['placeholder']);
        $this->assertSame(0, $after['literal_dollar_runs']);
        $this->assertSame(0, $after['space_count']);
    }

    public function test_patch_is_idempotent_for_psa_template(): void
    {
        if (! is_file($this->psaTemplatePath)) {
            $this->markTestSkipped('Service_Agreement_PSA.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->psaTemplatePath);
        $first = $this->patcher->patchDocumentXml($xml);
        $second = $this->patcher->patchDocumentXml($first['xml']);

        $this->assertTrue($first['patched']);
        $this->assertFalse($second['patched']);
        $this->assertSame($first['xml'], $second['xml']);
    }

    public function test_supports_only_psa_template(): void
    {
        $this->assertTrue(PsaAgreementSection4SummaryTablePatcher::supportsTemplate('Service_Agreement_PSA.docx'));
        $this->assertFalse(PsaAgreementSection4SummaryTablePatcher::supportsTemplate('Service_Agreement_general.docx'));
    }

    public function test_does_not_change_unrelated_xml(): void
    {
        $xml = '<w:document><w:body><w:tbl><w:tr><w:tc><w:p><w:r><w:t>Other</w:t></w:r></w:p></w:tc></w:tr></w:tbl></w:body></w:document>';

        $result = $this->patcher->patchDocumentXml($xml);

        $this->assertFalse($result['patched']);
        $this->assertSame($xml, $result['xml']);
    }

    private function readDocumentXml(string $docxPath): string
    {
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($docxPath));
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        $this->assertNotFalse($xml);

        return $xml;
    }

    /**
     * @return array{jc: string, single_placeholder: bool, placeholder: ?string, literal_dollar_runs: int, space_count: int}
     */
    private function authorityChargesRowStats(string $xml): array
    {
        $anchorPos = strpos($xml, 'GrandTotalFeesAndCosts');
        $this->assertNotFalse($anchorPos);

        $tblStart = strrpos(substr($xml, 0, $anchorPos), '<w:tbl>');
        $tblEnd = strpos($xml, '</w:tbl>', $anchorPos);
        $this->assertNotFalse($tblStart);
        $this->assertNotFalse($tblEnd);

        $section = substr($xml, $tblStart, $tblEnd - $tblStart + 8);
        preg_match_all('/<w:tr\b.*?<\/w:tr>/s', $section, $rows);

        $authorityRow = null;
        foreach ($rows[0] as $row) {
            if (str_contains($row, 'TotalDoHAChargesInclSurcharge')
                || (str_contains($row, 'TotalDoHACharges') && str_contains($row, 'relevant authority'))) {
                $authorityRow = $row;
                break;
            }
        }

        $this->assertNotNull($authorityRow);

        preg_match_all('/<w:tc>.*?<\/w:tc>/s', $authorityRow, $cells);
        $amountCell = $cells[0][count($cells[0]) - 1];

        $jc = 'none';
        if (preg_match('/<w:jc w:val="([^"]+)"/', $amountCell, $m)) {
            $jc = $m[1];
        }

        $spaceCount = 0;
        if (preg_match_all('/xml:space="preserve">(\s+)</', $amountCell, $spaces)) {
            foreach ($spaces[1] as $chunk) {
                $spaceCount += strlen($chunk);
            }
        }

        $literalDollarRuns = 0;
        if (preg_match_all('/<w:t>\$<\/w:t>/', $amountCell, $dollarMatches)) {
            $literalDollarRuns = count($dollarMatches[0]);
        }

        $placeholder = null;
        if (preg_match('/\$\\$\\{(TotalDoHAChargesInclSurcharge|TotalDoHACharges)\\}/', $amountCell, $placeholderMatch)) {
            $placeholder = $placeholderMatch[1];
        }

        return [
            'jc' => $jc,
            'single_placeholder' => str_contains($amountCell, '<w:t>$${TotalDoHAChargesInclSurcharge}</w:t>'),
            'placeholder' => $placeholder,
            'literal_dollar_runs' => $literalDollarRuns,
            'space_count' => $spaceCount,
        ];
    }
}
