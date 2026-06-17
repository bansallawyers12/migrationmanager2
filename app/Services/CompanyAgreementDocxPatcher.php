<?php

namespace App\Services;

/**
 * Runtime DOCX XML fixes for company nomination / sponsorship service agreement templates.
 */
class CompanyAgreementDocxPatcher
{
    /** Known typo in uploaded company templates: ")" instead of "}" closing the merge field. */
    private const STREET_PLACEHOLDER_TYPO = '${BusinessAddressStreet1and2)';

    private const STREET_PLACEHOLDER_FIXED = '${BusinessAddressStreet1and2}';

    private const SAF_LEVY_CHARGE_TYPE_LABEL = 'Skilling Australians Fund (SAF) Levy';

    private const SAF_LEVY_CHARGE_TYPE_LABEL_WITH_RATE = 'Skilling Australians Fund (SAF) Levy ($1200 per year)';

    /** Section headings that must start on a new page in the company nomination template. */
    private const NOMINATION_SECTION_HEADINGS_WITH_PAGE_BREAK = [
        '3. Other Costs',
        '5. Payment Structure',
    ];

    /**
     * @return array{xml: string, patched: bool, fixes: list<string>}
     */
    public function patchDocumentXml(string $xml, ?string $templateFileName = null): array
    {
        $fixes = [];
        $patched = false;

        if (str_contains($xml, self::STREET_PLACEHOLDER_TYPO)) {
            $xml = str_replace(self::STREET_PLACEHOLDER_TYPO, self::STREET_PLACEHOLDER_FIXED, $xml);
            $fixes[] = 'BusinessAddressStreet1and2_closing_brace';
            $patched = true;
        }

        if ($this->isCompanyNominationTemplate($templateFileName)) {
            $safLevyPatch = $this->patchSafLevyChargeTypeLabel($xml);
            $xml = $safLevyPatch['xml'];
            if ($safLevyPatch['patched']) {
                $fixes[] = 'saf_levy_charge_type_label_rate';
                $patched = true;
            }

            $pageBreakPatch = $this->patchNominationSectionPageBreaks($xml);
            $xml = $pageBreakPatch['xml'];
            if ($pageBreakPatch['patched']) {
                $fixes[] = 'nomination_section_page_breaks';
                $patched = true;
            }
        }

        return [
            'xml' => $xml,
            'patched' => $patched,
            'fixes' => $fixes,
        ];
    }

    /**
     * @return array{xml: string, patched: bool}
     */
    private function patchNominationSectionPageBreaks(string $xml): array
    {
        $patched = false;

        preg_match_all('/<w:p\b.*?<\/w:p>/s', $xml, $paragraphMatches);
        foreach ($paragraphMatches[0] as $paragraph) {
            $text = $this->extractParagraphPlainText($paragraph);
            if (! in_array($text, self::NOMINATION_SECTION_HEADINGS_WITH_PAGE_BREAK, true)) {
                continue;
            }

            if (preg_match('/<w:pageBreakBefore\b/', $paragraph)) {
                continue;
            }

            $patchedParagraph = $this->addPageBreakBeforeToParagraph($paragraph);
            if ($patchedParagraph === $paragraph) {
                continue;
            }

            $xml = str_replace($paragraph, $patchedParagraph, $xml);
            $patched = true;
        }

        return ['xml' => $xml, 'patched' => $patched];
    }

    private function addPageBreakBeforeToParagraph(string $paragraph): string
    {
        if (preg_match('/<w:pPr>/', $paragraph)) {
            $updated = preg_replace('/(<w:pPr>)/', '$1<w:pageBreakBefore/>', $paragraph, 1);

            return is_string($updated) ? $updated : $paragraph;
        }

        $updated = preg_replace('/^(<w:p\b[^>]*>)/', '$1<w:pPr><w:pageBreakBefore/></w:pPr>', $paragraph, 1);

        return is_string($updated) ? $updated : $paragraph;
    }

    private function extractParagraphPlainText(string $paragraph): string
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $paragraph))));
    }

    /**
     * @return array{xml: string, patched: bool}
     */
    private function patchSafLevyChargeTypeLabel(string $xml): array
    {
        $oldRun = '<w:t>' . self::SAF_LEVY_CHARGE_TYPE_LABEL . '</w:t>';
        $newRun = '<w:t>' . self::SAF_LEVY_CHARGE_TYPE_LABEL_WITH_RATE . '</w:t>';

        if (! str_contains($xml, $oldRun) || str_contains($xml, $newRun)) {
            return ['xml' => $xml, 'patched' => false];
        }

        return [
            'xml' => str_replace($oldRun, $newRun, $xml),
            'patched' => true,
        ];
    }

    public static function isCompanyNominationTemplate(?string $templateFileName): bool
    {
        if ($templateFileName === null || $templateFileName === '') {
            return false;
        }

        return $templateFileName === config(
            'visa_agreement_templates.company_nomination',
            'Service_Agreement_company_nomination.docx'
        );
    }

    public static function isCompanyAgreementTemplate(?string $templateFileName): bool
    {
        if ($templateFileName === null || $templateFileName === '') {
            return false;
        }

        $companyTemplates = array_filter([
            config('visa_agreement_templates.company_sponsorship'),
            config('visa_agreement_templates.company_nomination'),
        ]);

        return in_array($templateFileName, $companyTemplates, true);
    }

    /**
     * Fix known XML issues in a stored .docx file (optional maintenance helper).
     */
    public function patchDocxFile(string $docxPath): bool
    {
        if (! is_file($docxPath)) {
            return false;
        }

        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return false;
        }

        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            $zip->close();

            return false;
        }

        $result = $this->patchDocumentXml($xml, basename($docxPath));
        if (! $result['patched']) {
            $zip->close();

            return false;
        }

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $result['xml']);
        $zip->close();

        return true;
    }
}
