<?php

namespace App\Services;

/**
 * Runtime DOCX XML patch for Service_Agreement_PSA.docx service type row.
 *
 * PSA uses "Category" + "Stream" on one line with manual spacing. This adds a right
 * tab stop and tab character so "Stream: ${visa_apply}" aligns to the right margin.
 */
class PsaAgreementServiceTypeRowPatcher
{
    private const RIGHT_TAB_POS = '9360';

    private const TEMPLATE = 'Service_Agreement_PSA.docx';

    /**
     * @return array{xml: string, patched: bool}
     */
    public function patchDocumentXml(string $xml): array
    {
        $patched = false;

        preg_match_all('/<w:p\b.*?<\/w:p>/s', $xml, $paragraphMatches);
        foreach ($paragraphMatches[0] as $paragraph) {
            if (! $this->isCategoryStreamParagraph($paragraph)) {
                continue;
            }

            $patchedParagraph = $this->patchCategoryStreamParagraph($paragraph);
            if ($patchedParagraph === $paragraph) {
                continue;
            }

            $xml = str_replace($paragraph, $patchedParagraph, $xml);
            $patched = true;
        }

        return ['xml' => $xml, 'patched' => $patched];
    }

    public static function supportsTemplate(?string $templateFileName): bool
    {
        return $templateFileName === config('visa_agreement_templates.psa', self::TEMPLATE);
    }

    private function isCategoryStreamParagraph(string $paragraph): bool
    {
        $text = $this->extractPlainText($paragraph);

        return str_contains($text, 'Category:')
            && str_contains($text, 'Stream:')
            && preg_match('/\$\{(?:visa_apply|Visa_apply)\}/', $text) === 1;
    }

    private function patchCategoryStreamParagraph(string $paragraph): string
    {
        if ($this->paragraphAlreadyPatched($paragraph)) {
            return $paragraph;
        }

        $plain = $this->extractPlainText($paragraph);
        if (! preg_match('/Category:\s*(.*?)\s*Stream:\s*(\$\{(?:visa_apply|Visa_apply)\})/si', $plain, $matches)) {
            return $paragraph;
        }

        $categoryValue = trim($matches[1]);
        $placeholder = $matches[2];

        preg_match_all('/<w:r\b[^>]*>.*?<\/w:r>/s', $paragraph, $runMatches);
        $runs = $runMatches[0];

        $categoryLabelProperties = $this->extractRunPropertiesFromLabel($runs, 'Category');
        $categoryValueProperties = $this->extractCategoryValueRunProperties($runs);
        $streamLabelProperties = $this->extractRunPropertiesFromLabel($runs, 'Stream');

        if ($categoryLabelProperties === '' || $streamLabelProperties === '') {
            return $paragraph;
        }

        $valueRunProperties = $categoryValueProperties !== '' ? $categoryValueProperties : $categoryLabelProperties;

        if (! preg_match('/^(<w:p\b[^>]*>)(<w:pPr>.*?<\/w:pPr>)?/s', $paragraph, $openMatch)) {
            return $paragraph;
        }

        $newParagraph = $openMatch[1]
            . ($openMatch[2] ?? '')
            . $this->buildTextRun($categoryLabelProperties, 'Category: ')
            . $this->buildTextRun($valueRunProperties, $categoryValue)
            . $this->buildTabRun($valueRunProperties)
            . $this->buildTextRun($streamLabelProperties, 'Stream: ')
            . $this->buildTextRun($streamLabelProperties, $placeholder)
            . '</w:p>';

        return $this->addRightTabToParagraphProperties($newParagraph);
    }

    private function paragraphAlreadyPatched(string $paragraph): bool
    {
        return preg_match('/<w:tab[^>]*w:val="right"/', $paragraph) === 1
            && preg_match('/<w:tab\/>/', $paragraph) === 1;
    }

    /**
     * @param  list<string>  $runs
     */
    private function extractRunPropertiesFromLabel(array $runs, string $label): string
    {
        foreach ($runs as $run) {
            $runText = $this->extractPlainText($run);
            if ($runText === $label || str_contains($runText, $label.':')) {
                return $this->extractRunProperties($run);
            }
        }

        return '';
    }

    /**
     * @param  list<string>  $runs
     */
    private function extractCategoryValueRunProperties(array $runs): string
    {
        $seenCategory = false;

        foreach ($runs as $run) {
            $runText = $this->extractPlainText($run);

            if (! $seenCategory) {
                if ($runText === 'Category' || str_contains($runText, 'Category:')) {
                    $seenCategory = true;
                }

                continue;
            }

            if ($runText === 'Stream' || str_contains($runText, 'Stream:')) {
                break;
            }

            if (trim($runText) === '' || $runText === ':') {
                continue;
            }

            return $this->extractRunProperties($run);
        }

        return '';
    }

    private function addRightTabToParagraphProperties(string $paragraph): string
    {
        if (preg_match('/<w:tab[^>]*w:val="right"/', $paragraph)) {
            return $paragraph;
        }

        $rightTab = '<w:tab w:val="right" w:pos="' . self::RIGHT_TAB_POS . '"/>';

        $updated = preg_replace_callback(
            '/(<w:pPr>)(.*?)(<\/w:pPr>)/s',
            static function (array $matches) use ($rightTab): string {
                $inner = $matches[2];

                if (preg_match('/<w:tabs>(.*?)<\/w:tabs>/s', $inner, $tabsMatch)) {
                    $inner = str_replace(
                        $tabsMatch[0],
                        '<w:tabs>' . $tabsMatch[1] . $rightTab . '</w:tabs>',
                        $inner
                    );
                } else {
                    $inner .= '<w:tabs>' . $rightTab . '</w:tabs>';
                }

                return $matches[1] . $inner . $matches[3];
            },
            $paragraph,
            1
        );

        return is_string($updated) ? $updated : $paragraph;
    }

    private function buildTextRun(string $runProperties, string $text): string
    {
        $escaped = htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return '<w:r>' . $runProperties . '<w:t xml:space="preserve">' . $escaped . '</w:t></w:r>';
    }

    private function buildTabRun(string $runProperties): string
    {
        return '<w:r>' . $runProperties . '<w:tab/></w:r>';
    }

    private function extractRunProperties(string $run): string
    {
        if (preg_match('/<w:rPr\b.*?<\/w:rPr>/s', $run, $match)) {
            return $match[0];
        }

        return '';
    }

    private function extractPlainText(string $xmlFragment): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $xmlFragment))));

        return html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
