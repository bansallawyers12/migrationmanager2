<?php

namespace App\Services;

/**
 * Runtime DOCX XML patch for Service_Agreement_PSA.docx Section 4 summary table.
 *
 * The "Total relevant authority charges" amount cell uses a stray "$" run plus "$${TotalDoHACharges}",
 * which renders as "$$130.00" after merge. It is also center-aligned with manual padding instead of
 * right-aligned like the other Section 4 amount rows.
 *
 * The placeholder is normalized to {@see TotalDoHAChargesInclSurcharge} so PSA matches other templates:
 * department charges plus surcharge (same value used in the grand total).
 */
class PsaAgreementSection4SummaryTablePatcher
{
    private const SECTION4_TABLE_ANCHOR = 'GrandTotalFeesAndCosts';

    private const LEGACY_AUTHORITY_PLACEHOLDER = 'TotalDoHACharges';

    private const AUTHORITY_PLACEHOLDER = 'TotalDoHAChargesInclSurcharge';

    /**
     * @return array{xml: string, patched: bool}
     */
    public function patchDocumentXml(string $xml): array
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
            if (! $this->isAuthorityChargesRow($row)) {
                continue;
            }

            if (! preg_match_all('/<w:tc>.*?<\/w:tc>/s', $row, $cellMatches) || empty($cellMatches[0])) {
                continue;
            }

            $amountCell = $cellMatches[0][count($cellMatches[0]) - 1];
            $patchedAmountCell = $this->patchAuthorityAmountCell($amountCell);
            if ($patchedAmountCell === $amountCell) {
                continue;
            }

            $patchedRow = str_replace($amountCell, $patchedAmountCell, $row);
            $table = str_replace($row, $patchedRow, $table);
            $patched = true;
        }

        if (! $patched) {
            return ['xml' => $xml, 'patched' => false];
        }

        return [
            'xml' => substr($xml, 0, $tblStart) . $table . substr($xml, $tblEnd),
            'patched' => true,
        ];
    }

    public static function supportsTemplate(?string $templateFileName): bool
    {
        return $templateFileName === config('visa_agreement_templates.psa', 'Service_Agreement_PSA.docx');
    }

    private function isAuthorityChargesRow(string $row): bool
    {
        return str_contains($row, self::LEGACY_AUTHORITY_PLACEHOLDER)
            || str_contains($row, self::AUTHORITY_PLACEHOLDER);
    }

    private function patchAuthorityAmountCell(string $amountCell): string
    {
        if ($this->authorityAmountCellAlreadyAligned($amountCell)) {
            return $amountCell;
        }

        $amountCell = $this->stripEditorColorMarkup($amountCell);
        $amountCell = $this->ensureRightParagraphAlignment($amountCell);

        $patched = preg_replace(
            '/(<w:pPr>.*?<\/w:pPr>).*?(<\/w:p><\/w:tc>)/s',
            '$1' . $this->amountRuns() . '$2',
            $amountCell,
            1
        );

        return is_string($patched) ? $patched : $amountCell;
    }

    private function authorityAmountCellAlreadyAligned(string $amountCell): bool
    {
        return str_contains($amountCell, '<w:jc w:val="right"/>')
            && str_contains($amountCell, '<w:t>$${' . self::AUTHORITY_PLACEHOLDER . '}</w:t>')
            && ! preg_match('/xml:space="preserve">\s+<\/w:t>/', $amountCell)
            && ! str_contains($amountCell, 'spellStart');
    }

    private function amountRuns(): string
    {
        return '<w:r><w:rPr><w:rFonts w:ascii="Franklin Gothic Book" w:hAnsi="Franklin Gothic Book"/>'
            . '<w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>$${'
            . self::AUTHORITY_PLACEHOLDER
            . '}</w:t></w:r>';
    }

    private function stripEditorColorMarkup(string $cell): string
    {
        return (string) preg_replace('/<w:color w:val="FF0000"\/>/', '', $cell);
    }

    private function ensureRightParagraphAlignment(string $cell): string
    {
        if (str_contains($cell, '<w:jc w:val="both"/>')) {
            return str_replace('<w:jc w:val="both"/>', '<w:jc w:val="right"/>', $cell);
        }

        if (str_contains($cell, '<w:jc w:val="center"/>')) {
            return str_replace('<w:jc w:val="center"/>', '<w:jc w:val="right"/>', $cell);
        }

        if (str_contains($cell, '<w:jc w:val="right"/>')) {
            return $cell;
        }

        $withRight = preg_replace(
            '/(<w:pPr>.*?<w:spacing w:before="80" w:after="(?:40|80)"\/>)/s',
            '$1<w:jc w:val="right"/>',
            $cell,
            1
        );

        if (is_string($withRight) && $withRight !== $cell) {
            return $withRight;
        }

        $withRight = preg_replace(
            '/(<w:pPr>.*?<w:spacing w:before="120" w:after="120"\/>)/s',
            '$1<w:jc w:val="right"/>',
            $cell,
            1
        );

        return is_string($withRight) ? $withRight : $cell;
    }
}
