<?php

namespace App\Services;

/**
 * Runtime DOCX XML patch for visa service agreement templates (except Job Ready).
 *
 * Right-aligns fee/charge amount cells and removes manual space padding so currency
 * digits line up vertically. Only touches cells whose merged text looks like fee
 * placeholders ($${...}) or currency literals ($123.45), not client detail merge fields.
 */
class VisaAgreementAmountTablePatcher
{
    /**
     * @return array{xml: string, patched: bool}
     */
    public function patchDocumentXml(string $xml): array
    {
        $patched = false;

        preg_match_all('/<w:tbl\b.*?<\/w:tbl>/s', $xml, $tableMatches);
        foreach ($tableMatches[0] as $table) {
            $patchedTable = $this->patchTableAmountCells($table);
            if ($patchedTable === $table) {
                continue;
            }

            $xml = str_replace($table, $patchedTable, $xml);
            $patched = true;
        }

        return ['xml' => $xml, 'patched' => $patched];
    }

    private function patchTableAmountCells(string $table): string
    {
        $patchedTable = $table;

        preg_match_all('/<w:tr\b.*?<\/w:tr>/s', $table, $rowMatches);
        foreach ($rowMatches[0] as $row) {
            if (! preg_match_all('/<w:tc>.*?<\/w:tc>/s', $row, $cellMatches) || count($cellMatches[0]) < 2) {
                continue;
            }

            $patchedRow = $row;
            foreach ($cellMatches[0] as $index => $cell) {
                if ($index === 0) {
                    continue;
                }

                if (! $this->shouldPatchAmountCell($cell)) {
                    continue;
                }

                $patchedCell = $this->patchAmountCell($cell);
                if ($patchedCell === $cell) {
                    continue;
                }

                $patchedRow = str_replace($cell, $patchedCell, $patchedRow);
            }

            if ($patchedRow !== $row) {
                $patchedTable = str_replace($row, $patchedRow, $patchedTable);
            }
        }

        return $patchedTable;
    }

    private function shouldPatchAmountCell(string $cell): bool
    {
        $text = trim($this->extractCellPlainText($cell));
        if ($text === '') {
            return false;
        }

        if ($this->isClientDetailMergeField($text)) {
            return false;
        }

        if (preg_match('/^Amount(\s|\(|$)/i', $text)) {
            return true;
        }

        if (preg_match('/^\$\$\{([^}]+)\}/', $text, $matches)) {
            return $this->isFeeAmountPlaceholder($matches[1]);
        }

        return (bool) preg_match('/^\$[\d]/', $text);
    }

    private function isClientDetailMergeField(string $text): bool
    {
        if (preg_match('/^\$\{/', $text) && ! str_starts_with($text, '$${')) {
            return true;
        }

        if (preg_match('/PersonCount/i', $text)) {
            return true;
        }

        return false;
    }

    private function isFeeAmountPlaceholder(string $name): bool
    {
        if (str_ends_with($name, 'description')) {
            return false;
        }

        if (str_contains($name, 'PersonCount')) {
            return false;
        }

        return true;
    }

    private function patchAmountCell(string $cell): string
    {
        $text = trim($this->extractCellPlainText($cell));
        $outputText = $this->normalizedAmountText($text);
        if ($outputText === null) {
            return $cell;
        }

        if ($this->amountCellAlreadyAligned($cell, $outputText)) {
            return $cell;
        }

        $cell = $this->stripEditorColorMarkup($cell);
        $cell = $this->ensureRightParagraphAlignment($cell);

        $patched = preg_replace_callback(
            '/(<w:pPr>.*?<\/w:pPr>).*?(<\/w:p><\/w:tc>)/s',
            fn (array $matches): string => $matches[1] . $this->plainTextRuns($outputText) . $matches[2],
            $cell,
            1
        );

        return is_string($patched) ? $patched : $cell;
    }

    private function normalizedAmountText(string $text): ?string
    {
        if (preg_match('/^\$\$\{([^}]+)\}/', $text, $matches)) {
            if (! $this->isFeeAmountPlaceholder($matches[1])) {
                return null;
            }

            return '$${' . $matches[1] . '}';
        }

        return $text;
    }

    private function amountCellAlreadyAligned(string $cell, string $text): bool
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

    public static function supportsTemplate(?string $templateFileName): bool
    {
        if ($templateFileName === null || $templateFileName === '') {
            return false;
        }

        if ($templateFileName === 'Service_Agreement_Job_Ready.docx') {
            return false;
        }

        return str_starts_with($templateFileName, 'Service_Agreement_') && str_ends_with($templateFileName, '.docx');
    }
}
