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

    public function test_patches_professional_fee_table_amount_cells_to_right_alignment(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_Job_Ready.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $result = $this->patcher->patchDocumentXml($xml);
        $after = $this->professionalFeeTableAmountRowStats($result['xml']);

        $this->assertTrue($result['patched']);

        foreach (['block1', 'block2', 'block3', 'total'] as $rowKey) {
            $this->assertSame('right', $after[$rowKey]['jc'], "Professional fee row {$rowKey} should be right-aligned");
            $this->assertSame(0, $after[$rowKey]['space_count'], "Professional fee row {$rowKey} should not use padding spaces");
            $this->assertTrue($after[$rowKey]['single_placeholder'], "Professional fee row {$rowKey} should use a single placeholder run");
        }
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

    public function test_patches_fee_table_and_section4_blocktotal_rows_independently(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_Job_Ready.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $result = $this->patcher->patchDocumentXml($xml);
        $feeTable = $this->professionalFeeTableAmountRowStats($result['xml']);
        $section4 = $this->section4SummaryTableAmountRowStats($result['xml']);

        $this->assertSame('right', $feeTable['total']['jc']);
        $this->assertSame('right', $section4['professional_fees']['jc']);
        $this->assertSame(0, $feeTable['total']['space_count']);
        $this->assertSame(0, $section4['professional_fees']['space_count']);
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

    public function test_patches_authority_stage_charge_amount_cells_to_right_alignment(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_Job_Ready.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $before = $this->authorityStageChargeRowStats($xml);
        $result = $this->patcher->patchDocumentXml($xml);
        $after = $this->authorityStageChargeRowStats($result['xml']);

        $this->assertTrue($result['patched']);

        foreach (['jre', 'jrwa', 'jrfa'] as $stage) {
            foreach (['amount', 'surcharge'] as $column) {
                $this->assertSame('right', $after[$stage][$column]['jc'], "{$stage} {$column} should be right-aligned");
                $this->assertSame(0, $after[$stage][$column]['space_count'], "{$stage} {$column} should not use padding spaces");
                $this->assertSame($before[$stage][$column]['text'], $after[$stage][$column]['text'], "{$stage} {$column} value should be unchanged");
            }
        }

        $this->assertSame('right', $after['header']['amount']['jc']);
        $this->assertSame('right', $after['header']['surcharge']['jc']);
    }

    public function test_does_not_change_other_cost_rows_in_authority_charges_table(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_Job_Ready.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $before = $this->otherCostRowSnippet($xml);
        $result = $this->patcher->patchDocumentXml($xml);

        $this->assertSame($before, $this->otherCostRowSnippet($result['xml']));
    }

    public function test_does_not_change_fee_table_blocktotal_row_alignment(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_Job_Ready.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $result = $this->patcher->patchDocumentXml($xml);
        $after = $this->professionalFeeTableAmountRowStats($result['xml']);

        $this->assertSame('right', $after['total']['jc']);
        $this->assertSame(0, $after['total']['space_count']);
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
     * @return array{
     *     header: array{amount: array{jc: string, space_count: int, text: string}, surcharge: array{jc: string, space_count: int, text: string}},
     *     jre: array{amount: array{jc: string, space_count: int, text: string}, surcharge: array{jc: string, space_count: int, text: string}},
     *     jrwa: array{amount: array{jc: string, space_count: int, text: string}, surcharge: array{jc: string, space_count: int, text: string}},
     *     jrfa: array{amount: array{jc: string, space_count: int, text: string}, surcharge: array{jc: string, space_count: int, text: string}}
     * }
     */
    private function authorityStageChargeRowStats(string $xml): array
    {
        $rows = $this->authorityChargesTableRows($xml);

        $headerRow = null;
        $jreRow = null;
        $jrwaRow = null;
        $jrfaRow = null;

        foreach ($rows as $row) {
            if ($this->patcherRowIsAuthorityHeader($row)) {
                $headerRow = $row;
            } elseif (preg_match('/JRE\s+Stage/u', $row)) {
                $jreRow = $row;
            } elseif (preg_match('/JRWA\s+Stage/u', $row)) {
                $jrwaRow = $row;
            } elseif (preg_match('/JRFA\s+Stage/u', $row)) {
                $jrfaRow = $row;
            }
        }

        $this->assertNotNull($headerRow);
        $this->assertNotNull($jreRow);
        $this->assertNotNull($jrwaRow);
        $this->assertNotNull($jrfaRow);

        return [
            'header' => [
                'amount' => $this->authorityAmountCellStats($this->tableCellAt($headerRow, 1)),
                'surcharge' => $this->authorityAmountCellStats($this->tableCellAt($headerRow, 2)),
            ],
            'jre' => [
                'amount' => $this->authorityAmountCellStats($this->tableCellAt($jreRow, 1)),
                'surcharge' => $this->authorityAmountCellStats($this->tableCellAt($jreRow, 2)),
            ],
            'jrwa' => [
                'amount' => $this->authorityAmountCellStats($this->tableCellAt($jrwaRow, 1)),
                'surcharge' => $this->authorityAmountCellStats($this->tableCellAt($jrwaRow, 2)),
            ],
            'jrfa' => [
                'amount' => $this->authorityAmountCellStats($this->tableCellAt($jrfaRow, 1)),
                'surcharge' => $this->authorityAmountCellStats($this->tableCellAt($jrfaRow, 2)),
            ],
        ];
    }

    private function otherCostRowSnippet(string $xml): string
    {
        $rows = $this->authorityChargesTableRows($xml);
        foreach ($rows as $row) {
            if (str_contains($row, 'Interpreter')) {
                return $row;
            }
        }

        $this->fail('Interpreter row not found in authority charges table');

        return '';
    }

    /**
     * @return list<string>
     */
    private function authorityChargesTableRows(string $xml): array
    {
        $anchorPos = strpos($xml, 'Relevant Authority Charges');
        $this->assertNotFalse($anchorPos);

        $tblStart = strrpos(substr($xml, 0, $anchorPos), '<w:tbl>');
        $nextMajorSection = strpos($xml, 'Total Fees, Charges', $anchorPos);
        if ($nextMajorSection !== false) {
            $tblEnd = strrpos(substr($xml, 0, $nextMajorSection), '</w:tbl>');
        } else {
            $tblEnd = strpos($xml, '</w:tbl>', $anchorPos);
        }
        $this->assertNotFalse($tblStart);
        $this->assertNotFalse($tblEnd);

        $section = substr($xml, $tblStart, $tblEnd - $tblStart + 8);
        preg_match_all('/<w:tr\b.*?<\/w:tr>/s', $section, $rows);

        return $rows[0];
    }

    private function patcherRowIsAuthorityHeader(string $row): bool
    {
        return str_contains($row, 'Charge Type') && str_contains($row, 'Amount incl Surcharge');
    }

    private function tableCellAt(string $row, int $index): string
    {
        preg_match_all('/<w:tc>.*?<\/w:tc>/s', $row, $cells);
        $this->assertNotEmpty($cells[0][$index] ?? null);

        return $cells[0][$index];
    }

    /**
     * @return array{jc: string, space_count: int, text: string}
     */
    private function authorityAmountCellStats(string $cell): array
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

        $text = trim(strip_tags(preg_replace('/<w:t[^>]*>/', '', $cell)));
        $text = preg_replace('/\s+/', ' ', $text);

        return [
            'jc' => $jc,
            'space_count' => $spaceCount,
            'text' => $text,
        ];
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
