<?php

namespace Tests\Unit\Services;

use App\Services\VisaAgreementAmountTablePatcher;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use ZipArchive;

class VisaAgreementAmountTablePatcherTest extends TestCase
{
    private VisaAgreementAmountTablePatcher $patcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->patcher = new VisaAgreementAmountTablePatcher();
    }

    #[DataProvider('supportedTemplateProvider')]
    public function test_patches_fee_and_charge_amount_cells_to_right_alignment(string $templateFile): void
    {
        $path = storage_path('app/templates/' . $templateFile);
        if (! is_file($path)) {
            $this->markTestSkipped("{$templateFile} not present in storage/app/templates");
        }

        $xml = $this->readDocumentXml($path);
        $before = $this->feeAmountCellStats($xml);
        $this->assertNotEmpty($before, "Expected fee/charge amount cells in {$templateFile}");

        $result = $this->patcher->patchDocumentXml($xml);
        $after = $this->feeAmountCellStats($result['xml']);

        $this->assertTrue($result['patched'], "Expected {$templateFile} to require amount alignment patch");

        foreach ($before as $label => $stats) {
            $this->assertSame('right', $after[$label]['jc'], "{$templateFile} {$label} should be right-aligned");
            $this->assertSame(0, $after[$label]['space_count'], "{$templateFile} {$label} should not use padding spaces");
            $this->assertSame($stats['text'], $after[$label]['text'], "{$templateFile} {$label} value should be unchanged");
        }
    }

    public function test_does_not_patch_client_detail_merge_fields(): void
    {
        $xml = '<w:document><w:body><w:tbl><w:tr>'
            . '<w:tc><w:p><w:r><w:t>Name</w:t></w:r></w:p></w:tc>'
            . '<w:tc><w:p><w:r><w:t>${ApplicantDOB}</w:t></w:r></w:p></w:tc>'
            . '</w:tr></w:tbl></w:body></w:document>';

        $result = $this->patcher->patchDocumentXml($xml);

        $this->assertFalse($result['patched']);
        $this->assertSame($xml, $result['xml']);
    }

    public function test_patch_is_idempotent_for_general_template(): void
    {
        $path = storage_path('app/templates/Service_Agreement_general.docx');
        if (! is_file($path)) {
            $this->markTestSkipped('Service_Agreement_general.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($path);
        $first = $this->patcher->patchDocumentXml($xml);
        $second = $this->patcher->patchDocumentXml($first['xml']);

        $this->assertTrue($first['patched']);
        $this->assertFalse($second['patched']);
        $this->assertSame($first['xml'], $second['xml']);
    }

    public function test_supports_template_excludes_job_ready(): void
    {
        $this->assertFalse(VisaAgreementAmountTablePatcher::supportsTemplate('Service_Agreement_Job_Ready.docx'));
        $this->assertTrue(VisaAgreementAmountTablePatcher::supportsTemplate('Service_Agreement_general.docx'));
        $this->assertTrue(VisaAgreementAmountTablePatcher::supportsTemplate('Service_Agreement_PSA.docx'));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function supportedTemplateProvider(): array
    {
        return [
            'general' => ['Service_Agreement_general.docx'],
            'psa' => ['Service_Agreement_PSA.docx'],
            'skill_assessment' => ['Service_Agreement_Skill_Assessment.docx'],
            '408' => ['Service_Agreement_408.docx'],
            'art' => ['Service_Agreement_ART.docx'],
            'citizenship' => ['Service_Agreement_citizenship.docx'],
            'eoi_roi' => ['Service_Agreement_EOI_ROI.docx'],
            'parents' => ['Service_Agreement_parents.docx'],
            'company_nomination' => ['Service_Agreement_company_nomination.docx'],
            'company_sponsorship' => ['Service_Agreement_company_sponsorship.docx'],
        ];
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
     * @return array<string, array{jc: string, space_count: int, text: string}>
     */
    private function feeAmountCellStats(string $xml): array
    {
        $stats = [];
        $index = 0;

        preg_match_all('/<w:tbl\b.*?<\/w:tbl>/s', $xml, $tables);
        foreach ($tables[0] as $table) {
            preg_match_all('/<w:tr\b.*?<\/w:tr>/s', $table, $rows);
            foreach ($rows[0] as $row) {
                if (! preg_match_all('/<w:tc>.*?<\/w:tc>/s', $row, $cells) || count($cells[0]) < 2) {
                    continue;
                }

                foreach ($cells[0] as $cellIndex => $cell) {
                    if ($cellIndex === 0) {
                        continue;
                    }

                    $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $cell))));
                    if ($text === '') {
                        continue;
                    }

                    if (preg_match('/^\$\{Applicant/i', $text) || preg_match('/^\$\{Contact/i', $text)) {
                        continue;
                    }

                    if (! preg_match('/^\$\$\{/', $text) && ! preg_match('/^\$[\d]/', $text) && ! preg_match('/^Amount(\s|\(|$)/i', $text)) {
                        continue;
                    }

                    if (preg_match('/^\$\$\{([^}]+)\}/', $text, $matches) && str_contains($matches[1], 'PersonCount')) {
                        continue;
                    }

                    $index++;
                    $stats['amount_' . $index] = $this->amountCellStats($cell);
                }
            }
        }

        return $stats;
    }

    /**
     * @return array{jc: string, space_count: int, text: string}
     */
    private function amountCellStats(string $cell): array
    {
        $jc = 'none';
        if (preg_match('/<w:jc w:val="([^"]+)"/', $cell, $m)) {
            $jc = $m[1];
        }

        $spaceCount = 0;
        if (preg_match_all('/xml:space="preserve">(\s+)</', $cell, $spaces)) {
            foreach ($spaces[1] as $chunk) {
                $spaceCount += strlen($chunk);
            }
        }

        $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $cell))));

        return [
            'jc' => $jc,
            'space_count' => $spaceCount,
            'text' => $text,
        ];
    }
}
