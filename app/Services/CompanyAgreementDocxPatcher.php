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

    /**
     * @return array{xml: string, patched: bool, fixes: list<string>}
     */
    public function patchDocumentXml(string $xml): array
    {
        $fixes = [];
        $patched = false;

        if (str_contains($xml, self::STREET_PLACEHOLDER_TYPO)) {
            $xml = str_replace(self::STREET_PLACEHOLDER_TYPO, self::STREET_PLACEHOLDER_FIXED, $xml);
            $fixes[] = 'BusinessAddressStreet1and2_closing_brace';
            $patched = true;
        }

        return [
            'xml' => $xml,
            'patched' => $patched,
            'fixes' => $fixes,
        ];
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

        $result = $this->patchDocumentXml($xml);
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
