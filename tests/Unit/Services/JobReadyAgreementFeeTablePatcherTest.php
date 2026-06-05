<?php

namespace Tests\Unit\Services;

use App\Services\JobReadyAgreementFeeTablePatcher;
use Tests\TestCase;
use ZipArchive;

class JobReadyAgreementFeeTablePatcherTest extends TestCase
{
    private JobReadyAgreementFeeTablePatcher $patcher;

    private string $templatePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->patcher = new JobReadyAgreementFeeTablePatcher();
        $this->templatePath = storage_path('app/templates/Service_Agreement_Job_Ready.docx');
    }

    public function test_patches_block2_and_total_rows_to_match_block1_alignment(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_Job_Ready.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $before = $this->professionalFeeTableAmountRowStats($xml);

        $result = $this->patcher->patchDocumentXml($xml);

        $this->assertTrue($result['patched']);
        $after = $this->professionalFeeTableAmountRowStats($result['xml']);

        $this->assertSame('center', $after['block2']['jc']);
        $this->assertSame($before['block1']['space_count'], $after['block2']['space_count']);
        $this->assertTrue($after['block2']['single_placeholder']);
        $this->assertFalse($after['block2']['red_markup']);
        $this->assertSame($before['block1']['jc'], $after['block2']['jc']);

        $this->assertSame('center', $after['total']['jc']);
        $this->assertSame($before['block1']['space_count'], $after['total']['space_count']);
        $this->assertTrue($after['total']['single_placeholder']);
        $this->assertSame($before['block1']['jc'], $after['total']['jc']);
    }

    public function test_patch_is_idempotent_for_job_ready_template(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_Job_Ready.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $first = $this->patcher->patchDocumentXml($xml);
        $second = $this->patcher->patchDocumentXml($first['xml']);

        $this->assertTrue($first['patched']);
        $this->assertFalse($second['patched']);
        $this->assertSame($first['xml'], $second['xml']);
    }

    public function test_does_not_modify_xml_without_fee_table_placeholders(): void
    {
        $xml = '<w:document><w:body><w:tbl><w:tr><w:tc><w:p><w:r><w:t>Other</w:t></w:r></w:p></w:tc></w:tr></w:tbl></w:body></w:document>';

        $result = $this->patcher->patchDocumentXml($xml);

        $this->assertFalse($result['patched']);
        $this->assertSame($xml, $result['xml']);
    }

    public function test_does_not_change_block1_or_block3_rows(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_Job_Ready.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $before = $this->professionalFeeTableAmountRowStats($xml);
        $result = $this->patcher->patchDocumentXml($xml);
        $after = $this->professionalFeeTableAmountRowStats($result['xml']);

        $this->assertSame($before['block1'], $after['block1']);
        $this->assertSame($before['block3'], $after['block3']);
    }

    public function test_does_not_touch_second_blocktotal_placeholder_outside_fee_table(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_Job_Ready.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $firstPos = strpos($xml, 'Blocktotalfeesincltax');
        $secondPos = strpos($xml, 'Blocktotalfeesincltax', $firstPos + 1);
        $this->assertNotFalse($secondPos);

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

    private function placeholderSnippet(string $xml, int $position): string
    {
        return substr($xml, $position - 80, 200);
    }

    /**
     * @return array{block1: array{jc: string, split: bool, single_placeholder: bool, space_count: int, red_markup: bool}, block2: array{jc: string, split: bool, single_placeholder: bool, space_count: int, red_markup: bool}, block3: array{jc: string, split: bool, single_placeholder: bool, space_count: int, red_markup: bool}, total: array{jc: string, split: bool, single_placeholder: bool, space_count: int, red_markup: bool}}
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
        $block2Row = null;
        $block3Row = null;
        $totalRow = null;
        foreach ($rows[0] as $row) {
            if (str_contains($row, 'Block1feesincltax')) {
                $block1Row = $row;
            }
            if (str_contains($row, 'Block2feesincltax')) {
                $block2Row = $row;
            }
            if (str_contains($row, 'Block3feesincltax')) {
                $block3Row = $row;
            }
            if (str_contains($row, 'Total Professional Fee')) {
                $totalRow = $row;
            }
        }

        $this->assertNotNull($block1Row);
        $this->assertNotNull($block2Row);
        $this->assertNotNull($block3Row);
        $this->assertNotNull($totalRow);

        return [
            'block1' => $this->amountCellStats($this->lastTableCell($block1Row)),
            'block2' => $this->amountCellStats($this->lastTableCell($block2Row)),
            'block3' => $this->amountCellStats($this->lastTableCell($block3Row)),
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
     * @return array{jc: string, split: bool, single_placeholder: bool, space_count: int, red_markup: bool}
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

        return [
            'jc' => $jc,
            'split' => str_contains($cell, 'spellStart'),
            'single_placeholder' => (bool) preg_match('/<w:t>\$\$\{Block[123]feesincltax\}<\/w:t>/', $cell)
                || str_contains($cell, '<w:t>$${Blocktotalfeesincltax}</w:t>'),
            'space_count' => $spaceCount,
            'red_markup' => str_contains($cell, 'w:color w:val="FF0000"'),
        ];
    }
}
