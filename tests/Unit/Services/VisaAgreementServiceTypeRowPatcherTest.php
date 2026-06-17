<?php

namespace Tests\Unit\Services;

use App\Services\VisaAgreementServiceTypeRowPatcher;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use ZipArchive;

class VisaAgreementServiceTypeRowPatcherTest extends TestCase
{
    private VisaAgreementServiceTypeRowPatcher $patcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->patcher = new VisaAgreementServiceTypeRowPatcher();
    }

    #[DataProvider('serviceTypeRowTemplateProvider')]
    public function test_patches_category_subclass_row_with_right_tab(string $templateFile): void
    {
        $path = storage_path('app/templates/' . $templateFile);
        if (! is_file($path)) {
            $this->markTestSkipped("{$templateFile} not present in storage/app/templates");
        }

        $xml = $this->readDocumentXml($path);
        $before = $this->categorySubclassParagraph($xml);
        if ($before === null) {
            $this->markTestSkipped("{$templateFile} has no Category/Subclass service type row");
        }

        $result = $this->patcher->patchDocumentXml($xml);
        $after = $this->categorySubclassParagraph($result['xml']);

        $this->assertTrue($result['patched'], "Expected {$templateFile} to require service type row patch");
        $this->assertNotNull($after);
        $this->assertStringContainsString('Category:', $after['text']);
        $this->assertStringContainsString('Subclass:', $after['text']);
        $this->assertTrue($after['has_right_tab_stop'], "{$templateFile} should define a right tab stop");
        $this->assertTrue($after['has_tab_before_subclass'], "{$templateFile} should insert a tab before Subclass");
        $this->assertSame(0, $after['padding_space_count'], "{$templateFile} should not use padding spaces");
        $this->assertSame($before['category_value'], $after['category_value'], "{$templateFile} category value should be unchanged");
        $this->assertSame($before['placeholder'], $after['placeholder'], "{$templateFile} placeholder should be unchanged");
    }

    public function test_patch_is_idempotent_for_general_template(): void
    {
        $path = storage_path('app/templates/Service_Agreement_general.docx');
        if (! is_file($path)) {
            $this->markTestSkipped('Service_Agreement_general.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($path);
        $first = $this->patcher->patchDocumentXml($xml);
        $second = $this->patcher->patchDocumentXml($first['xml']);

        $this->assertTrue($first['patched']);
        $this->assertFalse($second['patched']);
        $this->assertSame($first['xml'], $second['xml']);
    }

    public function test_supports_all_service_agreement_templates(): void
    {
        $this->assertTrue(VisaAgreementServiceTypeRowPatcher::supportsTemplate('Service_Agreement_general.docx'));
        $this->assertTrue(VisaAgreementServiceTypeRowPatcher::supportsTemplate('Service_Agreement_parents.docx'));
        $this->assertTrue(VisaAgreementServiceTypeRowPatcher::supportsTemplate('Service_Agreement_408.docx'));
        $this->assertFalse(VisaAgreementServiceTypeRowPatcher::supportsTemplate('agreement_template.docx'));
    }

    public function test_does_not_change_unrelated_paragraphs(): void
    {
        $xml = '<w:document><w:body><w:p><w:r><w:t>Other content</w:t></w:r></w:p></w:body></w:document>';

        $result = $this->patcher->patchDocumentXml($xml);

        $this->assertFalse($result['patched']);
        $this->assertSame($xml, $result['xml']);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function serviceTypeRowTemplateProvider(): array
    {
        return [
            'general' => ['Service_Agreement_general.docx'],
            'parents' => ['Service_Agreement_parents.docx'],
            '408' => ['Service_Agreement_408.docx'],
            'art' => ['Service_Agreement_ART.docx'],
            'citizenship' => ['Service_Agreement_citizenship.docx'],
            'eoi_roi' => ['Service_Agreement_EOI_ROI.docx'],
            'job_ready' => ['Service_Agreement_Job_Ready.docx'],
            'company_nomination' => ['Service_Agreement_company_nomination.docx'],
            'company_sponsorship' => ['Service_Agreement_company_sponsorship.docx'],
        ];
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

    /**
     * @return array{text: string, category_value: string, placeholder: string, has_right_tab_stop: bool, has_tab_before_subclass: bool, padding_space_count: int}|null
     */
    private function categorySubclassParagraph(string $xml): ?array
    {
        preg_match_all('/<w:p\b.*?<\/w:p>/s', $xml, $paragraphMatches);
        foreach ($paragraphMatches[0] as $paragraph) {
            $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $paragraph))));
            if (! str_contains($text, 'Category:') || ! str_contains($text, 'Subclass:')) {
                continue;
            }

            if (! preg_match('/Category:\s*(.*?)\s*Subclass:\s*(\$\{(?:visa_apply|Visa_apply)\})/si', $text, $matches)) {
                continue;
            }

            $paddingSpaceCount = 0;
            if (preg_match_all('/xml:space="preserve">(\s+)</', $paragraph, $spaces)) {
                foreach ($spaces[1] as $chunk) {
                    $paddingSpaceCount += strlen($chunk);
                }
            }

            $subclassPos = strpos($paragraph, 'Subclass:');
            $tabBeforeSubclass = $subclassPos !== false
                && preg_match('/<w:tab\/>/', substr($paragraph, 0, $subclassPos)) === 1;

            return [
                'text' => $text,
                'category_value' => trim($matches[1]),
                'placeholder' => $matches[2],
                'has_right_tab_stop' => preg_match('/<w:tab[^>]*w:val="right"/', $paragraph) === 1,
                'has_tab_before_subclass' => $tabBeforeSubclass,
                'padding_space_count' => $paddingSpaceCount,
            ];
        }

        return null;
    }
}
