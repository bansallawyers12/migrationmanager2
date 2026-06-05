<?php

namespace App\Services;

/**
 * Runtime DOCX XML patch for Service_Agreement_Job_Ready.docx professional-fee table alignment.
 *
 * Block 2 (JRWA) and Total Professional Fee amount cells were inconsistent with Block 1/3
 * (center alignment + leading spaces). This normalizes only those rows in the fee table.
 */
class JobReadyAgreementFeeTablePatcher
{
    private const ROW_ANCHOR_TOTAL = 'Total Professional Fee';

    private const PLACEHOLDER_BLOCK2 = 'Block2feesincltax';

    private const PLACEHOLDER_TOTAL = 'Blocktotalfeesincltax';

    private const REFERENCE_SPACES = '             ';

    /**
     * @return array{xml: string, patched: bool}
     */
    public function patchDocumentXml(string $xml): array
    {
        $patched = false;

        $block2Patch = $this->patchRowByPlaceholder($xml, self::PLACEHOLDER_BLOCK2);
        $xml = $block2Patch['xml'];
        $patched = $patched || $block2Patch['patched'];

        $totalPatch = $this->patchTotalProfessionalFeeRow($xml);
        $xml = $totalPatch['xml'];
        $patched = $patched || $totalPatch['patched'];

        return ['xml' => $xml, 'patched' => $patched];
    }

    /**
     * @return array{xml: string, patched: bool}
     */
    private function patchTotalProfessionalFeeRow(string $xml): array
    {
        $pos = strpos($xml, self::ROW_ANCHOR_TOTAL);
        if ($pos === false) {
            return ['xml' => $xml, 'patched' => false];
        }

        return $this->patchRowAtAnchor($xml, $pos, self::PLACEHOLDER_TOTAL);
    }

    /**
     * @return array{xml: string, patched: bool}
     */
    private function patchRowByPlaceholder(string $xml, string $placeholder): array
    {
        $pos = strpos($xml, $placeholder);
        if ($pos === false) {
            return ['xml' => $xml, 'patched' => false];
        }

        return $this->patchRowAtAnchor($xml, $pos, $placeholder);
    }

    /**
     * @return array{xml: string, patched: bool}
     */
    private function patchRowAtAnchor(string $xml, int $pos, string $placeholder): array
    {
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
        if (! str_contains($row, $placeholder)) {
            return ['xml' => $xml, 'patched' => false];
        }

        if (! preg_match_all('/<w:tc>.*?<\/w:tc>/s', $row, $cellMatches) || empty($cellMatches[0])) {
            return ['xml' => $xml, 'patched' => false];
        }

        $amountCell = $cellMatches[0][count($cellMatches[0]) - 1];
        $patchedAmountCell = $this->patchAmountCell($amountCell, $placeholder);
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

    private function patchAmountCell(string $amountCell, string $placeholder): string
    {
        if ($this->amountCellAlreadyAligned($amountCell, $placeholder)) {
            return $amountCell;
        }

        $amountCell = $this->stripEditorColorMarkup($amountCell);
        $amountCell = $this->ensureCenterParagraphAlignment($amountCell);

        $patched = preg_replace(
            '/(<w:pPr>.*?<\/w:pPr>).*?(<\/w:p><\/w:tc>)/s',
            '$1' . $this->feeAmountRuns($placeholder) . '$2',
            $amountCell,
            1
        );

        return is_string($patched) ? $patched : $amountCell;
    }

    private function feeAmountRuns(string $placeholder): string
    {
        return '<w:r><w:rPr><w:rFonts w:ascii="Franklin Gothic Book" w:hAnsi="Franklin Gothic Book"/>'
            . '<w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t xml:space="preserve">' . self::REFERENCE_SPACES . '</w:t></w:r>'
            . '<w:r><w:rPr><w:rFonts w:ascii="Franklin Gothic Book" w:hAnsi="Franklin Gothic Book"/>'
            . '<w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>$${' . $placeholder . '}</w:t></w:r>';
    }

    private function amountCellAlreadyAligned(string $amountCell, string $placeholder): bool
    {
        return str_contains($amountCell, '<w:jc w:val="center"/>')
            && str_contains($amountCell, '<w:t xml:space="preserve">' . self::REFERENCE_SPACES . '</w:t>')
            && str_contains($amountCell, '<w:t>$${' . $placeholder . '}</w:t>')
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
