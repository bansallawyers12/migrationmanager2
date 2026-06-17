<?php

namespace Tests\Unit\Services;

use App\Services\ParentsAgreementDocxPatcher;
use Tests\TestCase;
use ZipArchive;

class ParentsAgreementDocxPatcherTest extends TestCase
{
    private ParentsAgreementDocxPatcher $patcher;

    private string $templatePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->patcher = new ParentsAgreementDocxPatcher();
        $this->templatePath = storage_path('app/templates/Service_Agreement_parents.docx');
    }

    public function test_patches_category_subclass_row_with_right_tab(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_parents.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $before = $this->categorySubclassParagraph($xml);
        $this->assertNotNull($before, 'Expected Category/Subclass service type paragraph');

        $result = $this->patcher->patchDocumentXml($xml);
        $after = $this->categorySubclassParagraph($result['xml']);

        $this->assertTrue($result['patched']);
        $this->assertNotNull($after);
        $this->assertStringContainsString('Category:', $before['text']);
        $this->assertStringContainsString('Visa Application', $before['text']);
        $this->assertStringContainsString('Subclass:', $before['text']);
        $this->assertStringContainsString('${visa_apply}', $before['text']);
        $this->assertStringContainsString('Category:', $after['text']);
        $this->assertStringContainsString('Visa Application', $after['text']);
        $this->assertStringContainsString('Subclass:', $after['text']);
        $this->assertStringContainsString('${visa_apply}', $after['text']);
        $this->assertTrue($after['has_right_tab_stop'], 'Paragraph should define a right tab stop');
        $this->assertTrue($after['has_tab_before_subclass'], 'Paragraph should insert a tab before Subclass');
        $this->assertSame(0, $after['padding_space_count'], 'Manual padding spaces should be removed');
    }

    public function test_patch_is_idempotent_for_parents_template(): void
    {
        if (! is_file($this->templatePath)) {
            $this->markTestSkipped('Service_Agreement_parents.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->templatePath);
        $first = $this->patcher->patchDocumentXml($xml);
        $second = $this->patcher->patchDocumentXml($first['xml']);

        $this->assertTrue($first['patched']);
        $this->assertFalse($second['patched']);
        $this->assertSame($first['xml'], $second['xml']);
    }

    public function test_supports_template_only_for_parents_agreement(): void
    {
        $this->assertTrue(ParentsAgreementDocxPatcher::supportsTemplate('Service_Agreement_parents.docx'));
        $this->assertFalse(ParentsAgreementDocxPatcher::supportsTemplate('Service_Agreement_general.docx'));
    }

    public function test_does_not_change_unrelated_paragraphs(): void
    {
        $xml = '<w:document><w:body><w:p><w:r><w:t>Other content</w:t></w:r></w:p></w:body></w:document>';

        $result = $this->patcher->patchDocumentXml($xml);

        $this->assertFalse($result['patched']);
        $this->assertSame($xml, $result['xml']);
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
     * @return array{text: string, has_right_tab_stop: bool, has_tab_before_subclass: bool, padding_space_count: int}|null
     */
    private function categorySubclassParagraph(string $xml): ?array
    {
        preg_match_all('/<w:p\b.*?<\/w:p>/s', $xml, $paragraphMatches);
        foreach ($paragraphMatches[0] as $paragraph) {
            $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $paragraph))));
            if (! str_contains($text, 'Category:') || ! str_contains($text, 'Subclass:')) {
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
                'has_right_tab_stop' => preg_match('/<w:tab[^>]*w:val="right"/', $paragraph) === 1,
                'has_tab_before_subclass' => $tabBeforeSubclass,
                'padding_space_count' => $paddingSpaceCount,
            ];
        }

        return null;
    }
}
