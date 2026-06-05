<?php

namespace App\Services;

/**
 * Runtime DOCX XML patch for Service_Agreement_Job_Ready.docx professional-fee table alignment.
 *
 * Block 2 (JRWA) amount cell was missing center alignment and used different leading spaces
 * than Block 1/3. This normalizes only that row's amount cell to match Block 1 formatting.
 */
class JobReadyAgreementFeeTablePatcher
{
    private const PLACEHOLDER = 'Block2feesincltax';

    private const REFERENCE_SPACES = '             ';

    /** Matches Block 1 amount runs in Job Ready: one space run + single PhpWord placeholder run. */
    private const FEE_AMOUNT_RUNS = '<w:r><w:rPr><w:rFonts w:ascii="Franklin Gothic Book" w:hAnsi="Franklin Gothic Book"/>'
        . '<w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t xml:space="preserve">' . self::REFERENCE_SPACES . '</w:t></w:r>'
        . '<w:r><w:rPr><w:rFonts w:ascii="Franklin Gothic Book" w:hAnsi="Franklin Gothic Book"/>'
        . '<w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>$${' . self::PLACEHOLDER . '}</w:t></w:r>';

    /**
     * @return array{xml: string, patched: bool}
     */
    public function patchDocumentXml(string $xml): array
    {
        $pos = strpos($xml, self::PLACEHOLDER);
        if ($pos === false) {
            return ['xml' => $xml, 'patched' => false];
        }

        $rowStart = strrpos(substr($xml, 0, $pos), '<w:tr');
        if ($rowStart === false) {
            return ['xml' => $xml, 'patched' => false];
        }

        $rowEnd = strpos($xml, '</w:tr>', $pos);
        if ($rowEnd === false) {
            return ['xml' => $xml, 'patched' => false];
        }
        $rowEnd += strlen('</w:tr>');

        $row = substr($xml, $rowStart, $rowEnd - $rowStart);
        if (! preg_match_all('/<w:tc>.*?<\/w:tc>/s', $row, $cellMatches) || empty($cellMatches[0])) {
            return ['xml' => $xml, 'patched' => false];
        }

        $amountCell = $cellMatches[0][count($cellMatches[0]) - 1];
        $patchedAmountCell = $this->patchAmountCell($amountCell);
        if ($patchedAmountCell === $amountCell) {
            return ['xml' => $xml, 'patched' => false];
        }

        $cellPos = strrpos($row, $amountCell);
        if ($cellPos === false) {
            return ['xml' => $xml, 'patched' => false];
        }

        $newRow = substr($row, 0, $cellPos) . $patchedAmountCell . substr($row, $cellPos + strlen($amountCell));
        $newXml = substr($xml, 0, $rowStart) . $newRow . substr($xml, $rowEnd);

        return ['xml' => $newXml, 'patched' => true];
    }

    private function patchAmountCell(string $amountCell): string
    {
        if ($this->amountCellAlreadyAligned($amountCell)) {
            return $amountCell;
        }

        $amountCell = $this->stripEditorColorMarkup($amountCell);
        $amountCell = $this->ensureCenterParagraphAlignment($amountCell);

        $patched = preg_replace(
            '/(<w:pPr>.*?<\/w:pPr>).*?(<\/w:p><\/w:tc>)/s',
            '$1' . self::FEE_AMOUNT_RUNS . '$2',
            $amountCell,
            1
        );

        return is_string($patched) ? $patched : $amountCell;
    }

    private function amountCellAlreadyAligned(string $amountCell): bool
    {
        return str_contains($amountCell, '<w:jc w:val="center"/>')
            && str_contains($amountCell, '<w:t xml:space="preserve">' . self::REFERENCE_SPACES . '</w:t>')
            && str_contains($amountCell, '<w:t>$${' . self::PLACEHOLDER . '}</w:t>')
            && ! str_contains($amountCell, 'w:color w:val="FF0000"')
            && ! str_contains($amountCell, 'spellStart');
    }

    private function stripEditorColorMarkup(string $amountCell): string
    {
        return (string) preg_replace('/<w:color w:val="FF0000"\/>/', '', $amountCell);
    }

    private function ensureCenterParagraphAlignment(string $amountCell): string
    {
        if (str_contains($amountCell, '<w:jc w:val="right"/>')) {
            return str_replace('<w:jc w:val="right"/>', '<w:jc w:val="center"/>', $amountCell);
        }

        if (str_contains($amountCell, '<w:jc w:val="center"/>')) {
            return $amountCell;
        }

        $withCenter = preg_replace(
            '/(<w:pPr>.*?<w:spacing w:before="80" w:after="80"\/>)/s',
            '$1<w:jc w:val="center"/>',
            $amountCell,
            1
        );

        return is_string($withCenter) ? $withCenter : $amountCell;
    }
}
