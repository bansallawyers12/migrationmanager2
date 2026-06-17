<?php

namespace App\Services;

/**
 * Runtime DOCX XML patch for visa service agreement templates.
 *
 * Service Type rows place "Category" and "Subclass" on one line with manual spaces.
 * This adds a right tab stop and tab character so "Subclass: ${visa_apply}" aligns
 * to the right margin after merge.
 */
class VisaAgreementServiceTypeRowPatcher
{
    private const RIGHT_TAB_POS = '9360';

    /**
     * @return array{xml: string, patched: bool}
     */
    public function patchDocumentXml(string $xml): array
    {
        $patched = false;

        preg_match_all('/<w:p\b.*?<\/w:p>/s', $xml, $paragraphMatches);
        foreach ($paragraphMatches[0] as $paragraph) {
            if (! $this->isCategorySubclassParagraph($paragraph)) {
                continue;
            }

            $patchedParagraph = $this->patchCategorySubclassParagraph($paragraph);
            if ($patchedParagraph === $paragraph) {
                continue;
            }

            $xml = str_replace($paragraph, $patchedParagraph, $xml);
            $patched = true;
        }

        return ['xml' => $xml, 'patched' => $patched];
    }

    private function isCategorySubclassParagraph(string $paragraph): bool
    {
        $text = $this->extractPlainText($paragraph);

        return str_contains($text, 'Category:')
            && str_contains($text, 'Subclass:')
            && preg_match('/\$\{(?:visa_apply|Visa_apply)\}/', $text) === 1;
    }

    private function patchCategorySubclassParagraph(string $paragraph): string
    {
        if ($this->paragraphAlreadyPatched($paragraph)) {
            return $paragraph;
        }

        $plain = $this->extractPlainText($paragraph);
        if (! preg_match('/Category:\s*(.*?)\s*Subclass:\s*(\$\{(?:visa_apply|Visa_apply)\})/si', $plain, $matches)) {
            return $paragraph;
        }

        $categoryValue = trim($matches[1]);
        $placeholder = $matches[2];

        preg_match_all('/<w:r\b[^>]*>.*?<\/w:r>/s', $paragraph, $runMatches);
        $runs = $runMatches[0];

        $categoryLabelRun = null;
        $categoryValueRunProperties = null;
        $subclassLabelRun = null;
        $placeholderData = null;

        foreach ($runs as $run) {
            $runText = $this->extractPlainText($run);

            if ($categoryLabelRun === null && str_contains($runText, 'Category:')) {
                $categoryLabelRun = $run;
                continue;
            }

            if ($subclassLabelRun === null && str_contains($runText, 'Subclass:')) {
                $subclassLabelRun = $run;
                continue;
            }

            if ($categoryLabelRun !== null && $subclassLabelRun === null && trim($runText) !== '') {
                $categoryValueRunProperties = $this->extractRunProperties($run);
            }
        }

        if ($categoryLabelRun !== null && $subclassLabelRun !== null) {
            $placeholderData = $this->mergePlaceholderFromRuns(
                $this->extractPlaceholderRuns($runs, $subclassLabelRun)
            );
        }

        if ($categoryLabelRun === null || $subclassLabelRun === null || $placeholderData === null) {
            return $paragraph;
        }

        $placeholderRun = $placeholderData['run'];

        if (! preg_match('/^(<w:p\b[^>]*>)(<w:pPr>.*?<\/w:pPr>)?/s', $paragraph, $openMatch)) {
            return $paragraph;
        }

        $valueRunProperties = $categoryValueRunProperties ?? $this->extractRunProperties($categoryLabelRun);
        $newParagraph = $openMatch[1]
            . ($openMatch[2] ?? '')
            . $categoryLabelRun
            . $this->buildTextRun($valueRunProperties, $categoryValue)
            . $this->buildTabRun($valueRunProperties)
            . $subclassLabelRun
            . $placeholderRun
            . '</w:p>';

        return $this->addRightTabToParagraphProperties($newParagraph);
    }

    private function paragraphAlreadyPatched(string $paragraph): bool
    {
        return preg_match('/<w:tab[^>]*w:val="right"/', $paragraph) === 1
            && preg_match('/<w:tab\/>/', $paragraph) === 1;
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

    private function extractPlaceholderRuns(array $runs, string $subclassLabelRun): array
    {
        $placeholderRuns = [];
        $collect = false;

        foreach ($runs as $run) {
            if ($run === $subclassLabelRun) {
                $collect = true;
                continue;
            }

            if ($collect) {
                $placeholderRuns[] = $run;
            }
        }

        return $placeholderRuns;
    }

    /**
     * @param  list<string>  $placeholderRuns
     * @return array{text: string, run: string}|null
     */
    private function mergePlaceholderFromRuns(array $placeholderRuns): ?array
    {
        if ($placeholderRuns === []) {
            return null;
        }

        $text = '';
        $runProperties = '';

        foreach ($placeholderRuns as $run) {
            $chunk = $this->extractPlainText($run);
            $text .= $chunk;
            if ($runProperties === '' && $chunk !== '') {
                $runProperties = $this->extractRunProperties($run);
            }
        }

        $text = trim($text);
        if (! preg_match('/^(\$\{(?:visa_apply|Visa_apply)\})$/', $text, $matches)) {
            return null;
        }

        return [
            'text' => $matches[1],
            'run' => $this->buildTextRun($runProperties, $matches[1]),
        ];
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

    public static function supportsTemplate(?string $templateFileName): bool
    {
        if ($templateFileName === null || $templateFileName === '') {
            return false;
        }

        return str_starts_with($templateFileName, 'Service_Agreement_') && str_ends_with($templateFileName, '.docx');
    }
}
