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

    public function test_patches_section4_summary_table_amount_cells_to_right_alignment(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_Job_Ready.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $before = $this->section4SummaryTableAmountRowStats($xml);
        $result = $this->patcher->patchDocumentXml($xml);
        $after = $this->section4SummaryTableAmountRowStats($result['xml']);

        $this->assertTrue($result['patched']);

        foreach (['professional_fees', 'authority_charges', 'estimated_costs', 'grand_total'] as $rowKey) {
            $this->assertSame('right', $after[$rowKey]['jc'], "Section 4 row {$rowKey} should be right-aligned");
            $this->assertSame(0, $after[$rowKey]['space_count'], "Section 4 row {$rowKey} should not use leading spaces");
            $this->assertTrue($after[$rowKey]['single_placeholder'], "Section 4 row {$rowKey} should use a single placeholder run");
        }

        $this->assertSame('right', $before['header']['jc']);
        $this->assertSame('right', $after['header']['jc']);
    }

    public function test_patches_section4_authority_row_after_clients_controller_placeholder_rename(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_Job_Ready.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $lastPos = strrpos($xml, 'TotalDoHASurcharges');
        $this->assertNotFalse($lastPos);
        $xml = substr_replace($xml, 'TotalDoHAChargesInclSurcharge', $lastPos, strlen('TotalDoHASurcharges'));

        $result = $this->patcher->patchDocumentXml($xml);
        $after = $this->section4SummaryTableAmountRowStats($result['xml'], 'TotalDoHAChargesInclSurcharge');

        $this->assertTrue($result['patched']);
        $this->assertSame('right', $after['authority_charges']['jc']);
        $this->assertSame(0, $after['authority_charges']['space_count']);
        $this->assertTrue($after['authority_charges']['single_placeholder']);
    }

    public function test_does_not_change_fee_table_blocktotal_row_alignment(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_Job_Ready.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $feeTablePos = strpos($xml, 'Total Professional Fee');
        $this->assertNotFalse($feeTablePos);

        $rowStart = strrpos(substr($xml, 0, $feeTablePos), '<w:tr');
        $rowEnd = strpos($xml, '</w:tr>', $feeTablePos) + 7;
        $feeTableTotalRowBefore = substr($xml, $rowStart, $rowEnd - $rowStart);

        $result = $this->patcher->patchDocumentXml($xml);

        $feeTablePosAfter = strpos($result['xml'], 'Total Professional Fee');
        $rowStartAfter = strrpos(substr($result['xml'], 0, $feeTablePosAfter), '<w:tr');
        $rowEndAfter = strpos($result['xml'], '</w:tr>', $feeTablePosAfter) + 7;
        $feeTableTotalRowAfter = substr($result['xml'], $rowStartAfter, $rowEndAfter - $rowStartAfter);

        $this->assertSame('center', $this->amountCellStats($this->lastTableCell($feeTableTotalRowAfter))['jc']);
        $this->assertNotSame(
            $this->amountCellStats($this->lastTableCell($feeTableTotalRowBefore))['jc'],
            'center'
        );
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
     * @return array{
     *     header: array{jc: string, split: bool, single_placeholder: bool, space_count: int, red_markup: bool},
     *     professional_fees: array{jc: string, split: bool, single_placeholder: bool, space_count: int, red_markup: bool},
     *     authority_charges: array{jc: string, split: bool, single_placeholder: bool, space_count: int, red_markup: bool},
     *     estimated_costs: array{jc: string, split: bool, single_placeholder: bool, space_count: int, red_markup: bool},
     *     grand_total: array{jc: string, split: bool, single_placeholder: bool, space_count: int, red_markup: bool}
     * }
     */
    private function section4SummaryTableAmountRowStats(string $xml, string $authorityPlaceholder = 'TotalDoHASurcharges'): array
    {
        $anchorPos = strpos($xml, 'GrandTotalFeesAndCosts');
        $this->assertNotFalse($anchorPos);

        $tblStart = strrpos(substr($xml, 0, $anchorPos), '<w:tbl>');
        $tblEnd = strpos($xml, '</w:tbl>', $anchorPos);
        $this->assertNotFalse($tblStart);
        $this->assertNotFalse($tblEnd);

        $section = substr($xml, $tblStart, $tblEnd - $tblStart + 8);
        preg_match_all('/<w:tr\b.*?<\/w:tr>/s', $section, $rows);
        $this->assertNotEmpty($rows[0]);

        $headerRow = null;
        $professionalFeesRow = null;
        $authorityChargesRow = null;
        $estimatedCostsRow = null;
        $grandTotalRow = null;

        foreach ($rows[0] as $row) {
            if (str_contains($row, 'GrandTotalFeesAndCosts')) {
                $grandTotalRow = $row;
            } elseif (str_contains($row, 'TotalEstimatedOthCosts') || str_contains($row, 'TotalEstimatedOth')) {
                $estimatedCostsRow = $row;
            } elseif (str_contains($row, 'TotalDoHAChargesInclSurcharge') || str_contains($row, 'TotalDoHASurcharges')) {
                $authorityChargesRow = $row;
            } elseif (str_contains($row, 'Blocktotalfeesincltax')) {
                $professionalFeesRow = $row;
            } elseif (str_contains($row, '>Amount</w:t>')) {
                $headerRow = $row;
            }
        }

        $this->assertNotNull($headerRow);
        $this->assertNotNull($professionalFeesRow);
        $this->assertNotNull($authorityChargesRow);
        $this->assertNotNull($estimatedCostsRow);
        $this->assertNotNull($grandTotalRow);

        return [
            'header' => $this->amountCellStats($this->lastTableCell($headerRow)),
            'professional_fees' => $this->section4AmountCellStats($this->lastTableCell($professionalFeesRow), 'Blocktotalfeesincltax'),
            'authority_charges' => $this->section4AmountCellStats($this->lastTableCell($authorityChargesRow), $authorityPlaceholder),
            'estimated_costs' => $this->section4AmountCellStats($this->lastTableCell($estimatedCostsRow), 'TotalEstimatedOthCosts'),
            'grand_total' => $this->section4AmountCellStats($this->lastTableCell($grandTotalRow), 'GrandTotalFeesAndCosts'),
        ];
    }

    /**
     * @return array{jc: string, split: bool, single_placeholder: bool, space_count: int, red_markup: bool}
     */
    private function section4AmountCellStats(string $cell, string $placeholder): array
    {
        $stats = $this->amountCellStats($cell);
        $stats['single_placeholder'] = str_contains($cell, '<w:t>$${' . $placeholder . '}</w:t>');

        return $stats;
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
