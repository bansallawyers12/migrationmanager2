<?php

namespace Tests\Unit\Services;

use App\Services\PsaAgreementFeeTablePatcher;
use Tests\TestCase;
use ZipArchive;

class PsaAgreementFeeTablePatcherTest extends TestCase
{
    private PsaAgreementFeeTablePatcher $patcher;

    private string $psaTemplatePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->patcher = new PsaAgreementFeeTablePatcher();
        $this->psaTemplatePath = storage_path('app/templates/Service_Agreement_PSA.docx');
    }

    public function test_patches_psa_total_row_to_match_block_fee_alignment(): void
    {
        if (! is_file($this->psaTemplatePath)) {
            $this->markTestSkipped('Service_Agreement_PSA.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->psaTemplatePath);
        $before = $this->professionalFeeTableAmountRowStats($xml);

        $result = $this->patcher->patchDocumentXml($xml);

        $this->assertTrue($result['patched']);
        $after = $this->professionalFeeTableAmountRowStats($result['xml']);

        $this->assertSame('center', $after['total']['jc']);
        $this->assertFalse($after['total']['split']);
        $this->assertTrue($after['total']['single_placeholder']);
        $this->assertGreaterThanOrEqual(20, $after['total']['space_count']);
        $this->assertSame($before['block1']['jc'], $after['total']['jc']);
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

    public function test_does_not_modify_xml_without_total_professional_fee_anchor(): void
    {
        $xml = '<w:document><w:body><w:tbl><w:tr><w:tc><w:p><w:r><w:t>Other</w:t></w:r></w:p></w:tc></w:tr></w:tbl></w:body></w:document>';

        $result = $this->patcher->patchDocumentXml($xml);

        $this->assertFalse($result['patched']);
        $this->assertSame($xml, $result['xml']);
    }

    public function test_does_not_touch_second_blocktotal_placeholder_outside_fee_table(): void
    {
        if (! is_file($this->psaTemplatePath)) {
            $this->markTestSkipped('Service_Agreement_PSA.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->psaTemplatePath);
        $firstPos = strpos($xml, 'Blocktotalfeesincltax');
        $secondPos = strpos($xml, 'Blocktotalfeesincltax', $firstPos + 1);
        $this->assertNotFalse($secondPos, 'PSA template should contain a second Blocktotalfeesincltax placeholder');

        $secondSnippetBefore = $this->placeholderSnippet($xml, $secondPos);
        $result = $this->patcher->patchDocumentXml($xml);
        $secondPosAfter = strpos($result['xml'], 'Blocktotalfeesincltax', strpos($result['xml'], 'Blocktotalfeesincltax') + 1);
        $this->assertNotFalse($secondPosAfter);

        $this->assertSame($secondSnippetBefore, $this->placeholderSnippet($result['xml'], $secondPosAfter));
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
     * @return array{block1: array{jc: string, split: bool, single_placeholder: bool, space_count: int}, total: array{jc: string, split: bool, single_placeholder: bool, space_count: int}}
     */
    private function professionalFeeTableAmountRowStats(string $xml): array
    {
        $tblStart = strrpos(substr($xml, 0, strpos($xml, 'Block1feesincltax') ?: 0), '<w:tbl>');
        $tblEnd = strpos($xml, '</w:tbl>', strpos($xml, 'Blocktotalfeesincltax') ?: 0);
        $this->assertNotFalse($tblStart);
        $this->assertNotFalse($tblEnd);

        $section = substr($xml, $tblStart, $tblEnd - $tblStart + 8);
        preg_match_all('/<w:tr\b.*?<\/w:tr>/s', $section, $rows);
        $this->assertNotEmpty($rows[0]);

        $block1Row = null;
        $totalRow = null;
        foreach ($rows[0] as $row) {
            if (str_contains($row, 'Block1feesincltax')) {
                $block1Row = $row;
            }
            if (str_contains($row, 'Total Professional Fee')) {
                $totalRow = $row;
            }
        }

        $this->assertNotNull($block1Row);
        $this->assertNotNull($totalRow);

        return [
            'block1' => $this->amountCellStats($this->lastTableCell($block1Row)),
            'total' => $this->amountCellStats($this->lastTableCell($totalRow)),
        ];
    }

    private function lastTableCell(string $row): string
    {
        preg_match_all('/<w:tc>.*?<\/w:tc>/s', $row, $cells);
        $this->assertNotEmpty($cells[0]);

        return $cells[0][count($cells[0]) - 1];
    }

    /**
     * @return array{jc: string, split: bool, single_placeholder: bool, space_count: int}
     */
    private function placeholderSnippet(string $xml, int $position): string
    {
        return substr($xml, $position - 80, 200);
    }

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

        return [
            'jc' => $jc,
            'split' => str_contains($cell, 'spellStart'),
            'single_placeholder' => str_contains($cell, '<w:t>$${Blocktotalfeesincltax}</w:t>')
                || str_contains($cell, '<w:t>$${Block1feesincltax}</w:t>'),
            'space_count' => $spaceCount,
        ];
    }
}
