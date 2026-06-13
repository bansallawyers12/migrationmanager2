<?php

namespace App\Services;

/**
 * Runtime DOCX XML patch for Service_Agreement_Job_Ready.docx table amount alignment.
 *
 * Professional-fee table: amount cells used center alignment with manual leading spaces;
 * header was right-aligned while block rows were not, so digits did not line up vertically.
 *
 * Section 4 summary table ("Total Fees, Charges and Costs"): amount cells used justify
 * alignment and leading spaces while the Amount header is right-aligned.
 *
 * Authority charges stage rows (JRE/JRWA/JRFA): Amount columns used center alignment with
 * inconsistent manual space padding, so digits did not line up vertically.
 */
class JobReadyAgreementFeeTablePatcher
{
    private const ROW_ANCHOR_TOTAL = 'Total Professional Fee';

    private const PLACEHOLDER_BLOCK1 = 'Block1feesincltax';

    private const PLACEHOLDER_BLOCK2 = 'Block2feesincltax';

    private const PLACEHOLDER_BLOCK3 = 'Block3feesincltax';

    private const PLACEHOLDER_TOTAL = 'Blocktotalfeesincltax';

    private const SECTION4_TABLE_ANCHOR = 'GrandTotalFeesAndCosts';

    private const AUTHORITY_CHARGES_SECTION_ANCHOR = 'Relevant Authority Charges';

    /** Section 4 authority row after ClientsController renames TotalDoHASurcharges for merge. */
    private const PLACEHOLDER_SECTION4_AUTHORITY = 'TotalDoHAChargesInclSurcharge';

    /**
     * @return array{xml: string, patched: bool}
     */
    public function patchDocumentXml(string $xml): array
    {
        $patched = false;

        $professionalFeePatch = $this->patchProfessionalFeeTable($xml);
        $xml = $professionalFeePatch['xml'];
        $patched = $patched || $professionalFeePatch['patched'];

        $section4Patch = $this->patchSection4SummaryTable($xml);
        $xml = $section4Patch['xml'];
        $patched = $patched || $section4Patch['patched'];

        $authorityPatch = $this->patchAuthorityChargesTable($xml);
        $xml = $authorityPatch['xml'];
        $patched = $patched || $authorityPatch['patched'];

        return ['xml' => $xml, 'patched' => $patched];
    }

    /**
     * @return array{xml: string, patched: bool}
     */
    private function patchProfessionalFeeTable(string $xml): array
    {
        $bounds = $this->professionalFeeTableBounds($xml);
        if ($bounds === null) {
            return ['xml' => $xml, 'patched' => false];
        }

        [$tblStart, $tblEnd] = $bounds;
        $table = substr($xml, $tblStart, $tblEnd - $tblStart);
        $patched = false;

        preg_match_all('/<w:tr\b.*?<\/w:tr>/s', $table, $rowMatches);
        foreach ($rowMatches[0] as $row) {
            if ($this->isProfessionalFeeHeaderRow($row)) {
                $patchedRow = $this->patchProfessionalFeeHeaderRow($row);
            } else {
                $placeholder = $this->professionalFeePlaceholderInRow($row);
                if ($placeholder === null) {
                    continue;
                }

                $patchedRow = $this->patchProfessionalFeePlaceholderRow($row, $placeholder);
            }

            if ($patchedRow === $row) {
                continue;
            }

            $table = str_replace($row, $patchedRow, $table);
            $patched = true;
        }

        if (! $patched) {
            return ['xml' => $xml, 'patched' => false];
        }

        $newXml = substr($xml, 0, $tblStart) . $table . substr($xml, $tblEnd);

        return ['xml' => $newXml, 'patched' => true];
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    private function professionalFeeTableBounds(string $xml): ?array
    {
        $block1Pos = strpos($xml, self::PLACEHOLDER_BLOCK1);
        if ($block1Pos === false) {
            return null;
        }

        $tblStart = strrpos(substr($xml, 0, $block1Pos), '<w:tbl>');
        $totalRowPos = strpos($xml, self::ROW_ANCHOR_TOTAL);
        if ($tblStart === false || $totalRowPos === false) {
            return null;
        }

        $totalPlaceholderPos = strpos($xml, self::PLACEHOLDER_TOTAL, $totalRowPos);
        if ($totalPlaceholderPos === false) {
            return null;
        }

        $tblEnd = strpos($xml, '</w:tbl>', $totalPlaceholderPos);
        if ($tblEnd === false) {
            return null;
        }

        return [$tblStart, $tblEnd + strlen('</w:tbl>')];
    }

    private function isProfessionalFeeHeaderRow(string $row): bool
    {
        return str_contains($row, 'Fee Type') && str_contains($row, '>Amount</w:t>');
    }

    private function professionalFeePlaceholderInRow(string $row): ?string
    {
        if (str_contains($row, self::PLACEHOLDER_BLOCK1)) {
            return self::PLACEHOLDER_BLOCK1;
        }

        if (str_contains($row, self::PLACEHOLDER_BLOCK2)) {
            return self::PLACEHOLDER_BLOCK2;
        }

        if (str_contains($row, self::PLACEHOLDER_BLOCK3)) {
            return self::PLACEHOLDER_BLOCK3;
        }

        if (str_contains($row, self::ROW_ANCHOR_TOTAL) && str_contains($row, self::PLACEHOLDER_TOTAL)) {
            return self::PLACEHOLDER_TOTAL;
        }

        return null;
    }

    private function patchProfessionalFeeHeaderRow(string $row): string
    {
        if (! preg_match_all('/<w:tc>.*?<\/w:tc>/s', $row, $cellMatches) || count($cellMatches[0]) < 2) {
            return $row;
        }

        $amountCell = $cellMatches[0][count($cellMatches[0]) - 1];
        $text = preg_replace('/\s+/', ' ', $this->extractCellPlainText($amountCell));
        $patchedCell = $this->patchAuthorityChargeTextCell($amountCell, $text);

        if ($patchedCell === $amountCell) {
            return $row;
        }

        return str_replace($amountCell, $patchedCell, $row);
    }

    private function patchProfessionalFeePlaceholderRow(string $row, string $placeholder): string
    {
        if (! preg_match_all('/<w:tc>.*?<\/w:tc>/s', $row, $cellMatches) || empty($cellMatches[0])) {
            return $row;
        }

        $amountCell = $cellMatches[0][count($cellMatches[0]) - 1];
        $patchedAmountCell = $this->patchProfessionalFeeAmountCell($amountCell, $placeholder);
        if ($patchedAmountCell === $amountCell) {
            return $row;
        }

        return str_replace($amountCell, $patchedAmountCell, $row);
    }

    private function patchProfessionalFeeAmountCell(string $amountCell, string $placeholder): string
    {
        if ($this->professionalFeeAmountCellAlreadyAligned($amountCell, $placeholder)) {
            return $amountCell;
        }

        $amountCell = $this->stripEditorColorMarkup($amountCell);
        $amountCell = $this->ensureRightParagraphAlignment($amountCell);

        $runs = $this->section4AmountRuns($placeholder);
        $patched = preg_replace(
            '/(<w:pPr>.*?<\/w:pPr>).*?(<\/w:p><\/w:tc>)/s',
            '$1' . $runs . '$2',
            $amountCell,
            1
        );

        return is_string($patched) ? $patched : $amountCell;
    }

    private function professionalFeeAmountCellAlreadyAligned(string $amountCell, string $placeholder): bool
    {
        return str_contains($amountCell, '<w:jc w:val="right"/>')
            && str_contains($amountCell, '<w:t>$${' . $placeholder . '}</w:t>')
            && ! preg_match('/xml:space="preserve">\s+<\/w:t>/', $amountCell)
            && ! str_contains($amountCell, 'spellStart');
    }

    /**
     * @return array{xml: string, patched: bool}
     */
    private function patchSection4SummaryTable(string $xml): array
    {
        $anchorPos = strpos($xml, self::SECTION4_TABLE_ANCHOR);
        if ($anchorPos === false) {
            return ['xml' => $xml, 'patched' => false];
        }

        $tblStart = strrpos(substr($xml, 0, $anchorPos), '<w:tbl>');
        if ($tblStart === false) {
            return ['xml' => $xml, 'patched' => false];
        }

        $tblEnd = strpos($xml, '</w:tbl>', $anchorPos);
        if ($tblEnd === false) {
            return ['xml' => $xml, 'patched' => false];
        }
        $tblEnd += strlen('</w:tbl>');

        $table = substr($xml, $tblStart, $tblEnd - $tblStart);
        $patched = false;

        preg_match_all('/<w:tr\b.*?<\/w:tr>/s', $table, $rowMatches);
        foreach ($rowMatches[0] as $row) {
            $placeholder = $this->section4PlaceholderInRow($row);
            if ($placeholder === null) {
                continue;
            }

            if (! preg_match_all('/<w:tc>.*?<\/w:tc>/s', $row, $cellMatches) || empty($cellMatches[0])) {
                continue;
            }

            $amountCell = $cellMatches[0][count($cellMatches[0]) - 1];
            $patchedAmountCell = $this->patchSection4AmountCell($amountCell, $placeholder);
            if ($patchedAmountCell === $amountCell) {
                continue;
            }

            $table = str_replace($row, substr($row, 0, strrpos($row, $amountCell)) . $patchedAmountCell . substr($row, strrpos($row, $amountCell) + strlen($amountCell)), $table);
            $patched = true;
        }

        if (! $patched) {
            return ['xml' => $xml, 'patched' => false];
        }

        $newXml = substr($xml, 0, $tblStart) . $table . substr($xml, $tblEnd);

        return ['xml' => $newXml, 'patched' => true];
    }

    /**
     * @return array{xml: string, patched: bool}
     */
    private function patchAuthorityChargesTable(string $xml): array
    {
        $anchorPos = strpos($xml, self::AUTHORITY_CHARGES_SECTION_ANCHOR);
        if ($anchorPos === false) {
            return ['xml' => $xml, 'patched' => false];
        }

        $tblStart = strrpos(substr($xml, 0, $anchorPos), '<w:tbl>');
        if ($tblStart === false) {
            return ['xml' => $xml, 'patched' => false];
        }

        $tblEnd = $this->authorityChargesTableEnd($xml, $anchorPos);
        if ($tblEnd === false) {
            return ['xml' => $xml, 'patched' => false];
        }

        $table = substr($xml, $tblStart, $tblEnd - $tblStart);
        $patched = false;

        preg_match_all('/<w:tr\b.*?<\/w:tr>/s', $table, $rowMatches);
        foreach ($rowMatches[0] as $row) {
            if ($this->isAuthorityChargesHeaderRow($row)) {
                $patchedRow = $this->patchAuthorityChargesHeaderRow($row);
            } elseif ($this->isAuthorityStageChargeRow($row)) {
                $patchedRow = $this->patchAuthorityStageChargeRow($row);
            } else {
                continue;
            }

            if ($patchedRow === $row) {
                continue;
            }

            $table = str_replace($row, $patchedRow, $table);
            $patched = true;
        }

        if (! $patched) {
            return ['xml' => $xml, 'patched' => false];
        }

        $newXml = substr($xml, 0, $tblStart) . $table . substr($xml, $tblEnd);

        return ['xml' => $newXml, 'patched' => true];
    }

    private function isAuthorityChargesHeaderRow(string $row): bool
    {
        return str_contains($row, 'Charge Type') && str_contains($row, 'Amount incl Surcharge');
    }

    private function isAuthorityStageChargeRow(string $row): bool
    {
        if (! str_contains($row, 'Application Charge')) {
            return false;
        }

        return preg_match('/JRE\s+Stage/u', $row) === 1
            || preg_match('/JRWA\s+Stage/u', $row) === 1
            || preg_match('/JRFA\s+Stage/u', $row) === 1;
    }

    private function patchAuthorityChargesHeaderRow(string $row): string
    {
        if (! preg_match_all('/<w:tc>.*?<\/w:tc>/s', $row, $cellMatches) || count($cellMatches[0]) < 3) {
            return $row;
        }

        $patchedRow = $row;
        foreach ([1, 2] as $cellIndex) {
            $cell = $cellMatches[0][$cellIndex];
            $text = $this->extractCellPlainText($cell);
            $patchedCell = $this->patchAuthorityChargeTextCell($cell, $text);
            if ($patchedCell !== $cell) {
                $patchedRow = str_replace($cell, $patchedCell, $patchedRow);
            }
        }

        return $patchedRow;
    }

    private function patchAuthorityStageChargeRow(string $row): string
    {
        if (! preg_match_all('/<w:tc>.*?<\/w:tc>/s', $row, $cellMatches) || count($cellMatches[0]) < 3) {
            return $row;
        }

        $patchedRow = $row;
        foreach ([1, 2] as $cellIndex) {
            $cell = $cellMatches[0][$cellIndex];
            $text = trim($this->extractCellPlainText($cell));
            if ($text === '' || ! preg_match('/\$[\d,.]+/', $text)) {
                continue;
            }

            $patchedCell = $this->patchAuthorityChargeTextCell($cell, $text);
            if ($patchedCell !== $cell) {
                $patchedRow = str_replace($cell, $patchedCell, $patchedRow);
            }
        }

        return $patchedRow;
    }

    private function patchAuthorityChargeTextCell(string $cell, string $text): string
    {
        if ($this->authorityChargeTextCellAlreadyAligned($cell, $text)) {
            return $cell;
        }

        $cell = $this->stripEditorColorMarkup($cell);
        $cell = $this->ensureRightParagraphAlignment($cell);

        $patched = preg_replace_callback(
            '/(<w:pPr>.*?<\/w:pPr>).*?(<\/w:p><\/w:tc>)/s',
            fn (array $matches): string => $matches[1] . $this->plainTextRuns($text) . $matches[2],
            $cell,
            1
        );

        return is_string($patched) ? $patched : $cell;
    }

    private function authorityChargeTextCellAlreadyAligned(string $cell, string $text): bool
    {
        return str_contains($cell, '<w:jc w:val="right"/>')
            && trim($this->extractCellPlainText($cell)) === $text
            && ! preg_match('/xml:space="preserve">\s+/', $cell)
            && ! str_contains($cell, 'spellStart')
            && str_contains($cell, '<w:t>' . $text . '</w:t>');
    }

    private function extractCellPlainText(string $cell): string
    {
        if (! preg_match_all('/<w:t[^>]*>([^<]*)<\/w:t>/', $cell, $matches)) {
            return '';
        }

        return trim(implode('', $matches[1]));
    }

    private function plainTextRuns(string $text): string
    {
        return '<w:r><w:rPr><w:rFonts w:ascii="Franklin Gothic Book" w:hAnsi="Franklin Gothic Book"/>'
            . '<w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>' . $text . '</w:t></w:r>';
    }

    private function authorityChargesTableEnd(string $xml, int $anchorPos): int|false
    {
        $nextMajorSection = strpos($xml, 'Total Fees, Charges', $anchorPos);
        if ($nextMajorSection !== false) {
            $relativeEnd = strrpos(substr($xml, 0, $nextMajorSection), '</w:tbl>');
            if ($relativeEnd !== false) {
                return $relativeEnd + strlen('</w:tbl>');
            }
        }

        $relativeEnd = strpos($xml, '</w:tbl>', $anchorPos);
        if ($relativeEnd === false) {
            return false;
        }

        return $relativeEnd + strlen('</w:tbl>');
    }

    private function section4PlaceholderInRow(string $row): ?string
    {
        if (str_contains($row, 'GrandTotalFeesAndCosts')) {
            return 'GrandTotalFeesAndCosts';
        }

        if (str_contains($row, 'TotalEstimatedOthCosts') || str_contains($row, 'TotalEstimatedOth')) {
            return 'TotalEstimatedOthCosts';
        }

        if (str_contains($row, self::PLACEHOLDER_SECTION4_AUTHORITY)) {
            return self::PLACEHOLDER_SECTION4_AUTHORITY;
        }

        if (str_contains($row, 'TotalDoHASurcharges')) {
            return 'TotalDoHASurcharges';
        }

        if (str_contains($row, 'Blocktotalfeesincltax')) {
            return 'Blocktotalfeesincltax';
        }

        return null;
    }

    private function patchSection4AmountCell(string $amountCell, string $placeholder): string
    {
        if ($this->section4AmountCellAlreadyAligned($amountCell, $placeholder)) {
            return $amountCell;
        }

        $amountCell = $this->stripEditorColorMarkup($amountCell);
        $amountCell = $this->ensureRightParagraphAlignment($amountCell);

        $patched = preg_replace(
            '/(<w:pPr>.*?<\/w:pPr>).*?(<\/w:p><\/w:tc>)/s',
            '$1' . $this->section4AmountRuns($placeholder) . '$2',
            $amountCell,
            1
        );

        return is_string($patched) ? $patched : $amountCell;
    }

    private function section4AmountRuns(string $placeholder): string
    {
        return '<w:r><w:rPr><w:rFonts w:ascii="Franklin Gothic Book" w:hAnsi="Franklin Gothic Book"/>'
            . '<w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>$${' . $placeholder . '}</w:t></w:r>';
    }

    private function section4AmountCellAlreadyAligned(string $amountCell, string $placeholder): bool
    {
        return str_contains($amountCell, '<w:jc w:val="right"/>')
            && str_contains($amountCell, '<w:t>$${' . $placeholder . '}</w:t>')
            && ! preg_match('/xml:space="preserve">\s+<\/w:t>/', $amountCell)
            && ! str_contains($amountCell, 'spellStart');
    }

    private function stripEditorColorMarkup(string $amountCell): string
    {
        return (string) preg_replace('/<w:color w:val="FF0000"\/>/', '', $amountCell);
    }

    private function ensureRightParagraphAlignment(string $amountCell): string
    {
        if (str_contains($amountCell, '<w:jc w:val="both"/>')) {
            return str_replace('<w:jc w:val="both"/>', '<w:jc w:val="right"/>', $amountCell);
        }

        if (str_contains($amountCell, '<w:jc w:val="center"/>')) {
            return str_replace('<w:jc w:val="center"/>', '<w:jc w:val="right"/>', $amountCell);
        }

        if (str_contains($amountCell, '<w:jc w:val="right"/>')) {
            return $amountCell;
        }

        $withRight = preg_replace(
            '/(<w:pPr>.*?<w:spacing w:before="80" w:after="(?:40|80)"\/>)/s',
            '$1<w:jc w:val="right"/>',
            $amountCell,
            1
        );

        return is_string($withRight) ? $withRight : $amountCell;
    }
}
