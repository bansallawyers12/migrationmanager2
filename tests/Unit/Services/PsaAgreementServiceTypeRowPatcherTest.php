<?php

namespace Tests\Unit\Services;

use App\Services\PsaAgreementServiceTypeRowPatcher;
use Tests\TestCase;
use ZipArchive;

class PsaAgreementServiceTypeRowPatcherTest extends TestCase
{
    private PsaAgreementServiceTypeRowPatcher $patcher;

    private string $psaTemplatePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->patcher = new PsaAgreementServiceTypeRowPatcher();
        $this->psaTemplatePath = storage_path('app/templates/Service_Agreement_PSA.docx');
    }

    public function test_patches_category_stream_row_with_right_tab(): void
    {
        if (! is_file($this->psaTemplatePath)) {
            $this->markTestSkipped('Service_Agreement_PSA.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->psaTemplatePath);
        $before = $this->categoryStreamParagraph($xml);
        if ($before === null) {
            $this->markTestSkipped('Service_Agreement_PSA.docx has no Category/Stream service type row');
        }

        $result = $this->patcher->patchDocumentXml($xml);
        $after = $this->categoryStreamParagraph($result['xml']);

        $this->assertTrue($result['patched']);
        $this->assertNotNull($after);
        $this->assertStringContainsString('Category:', $after['text']);
        $this->assertStringContainsString('Stream:', $after['text']);
        $this->assertTrue($after['has_right_tab_stop']);
        $this->assertTrue($after['has_tab_before_stream']);
        $this->assertSame(0, $after['padding_space_count']);
        $this->assertSame($before['category_value'], $after['category_value']);
        $this->assertSame($before['placeholder'], $after['placeholder']);
    }

    public function test_patch_is_idempotent_for_psa_template(): void
    {
        if (! is_file($this->psaTemplatePath)) {
            $this->markTestSkipped('Service_Agreement_PSA.docx not present in storage/app/templates');
        }

        $xml = $this->readDocumentXml($this->psaTemplatePath);
        $first = $this->patcher->patchDocumentXml($xml);
        $second = $this->patcher->patchDocumentXml($first['xml']);

        $this->assertTrue($first['patched']);
        $this->assertFalse($second['patched']);
        $this->assertSame($first['xml'], $second['xml']);
    }

    public function test_supports_only_psa_template(): void
    {
        $this->assertTrue(PsaAgreementServiceTypeRowPatcher::supportsTemplate('Service_Agreement_PSA.docx'));
        $this->assertFalse(PsaAgreementServiceTypeRowPatcher::supportsTemplate('Service_Agreement_general.docx'));
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
     * @return array{text: string, category_value: string, placeholder: string, has_right_tab_stop: bool, has_tab_before_stream: bool, padding_space_count: int}|null
     */
    private function categoryStreamParagraph(string $xml): ?array
    {
        preg_match_all('/<w:p\b.*?<\/w:p>/s', $xml, $paragraphMatches);
        foreach ($paragraphMatches[0] as $paragraph) {
            $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $paragraph))));
            if (! str_contains($text, 'Category:') || ! str_contains($text, 'Stream:')) {
                continue;
            }

            if (! preg_match('/Category:\s*(.*?)\s*Stream:\s*(\$\{(?:visa_apply|Visa_apply)\})/si', $text, $matches)) {
                continue;
            }

            $paddingSpaceCount = 0;
            if (preg_match_all('/xml:space="preserve">(\s+)</', $paragraph, $spaces)) {
                foreach ($spaces[1] as $chunk) {
                    $paddingSpaceCount += strlen($chunk);
                }
            }

            $streamPos = strpos($paragraph, 'Stream');
            $tabBeforeStream = $streamPos !== false
                && preg_match('/<w:tab\/>/', substr($paragraph, 0, $streamPos)) === 1;

            return [
                'text' => $text,
                'category_value' => trim($matches[1]),
                'placeholder' => $matches[2],
                'has_right_tab_stop' => preg_match('/<w:tab[^>]*w:val="right"/', $paragraph) === 1,
                'has_tab_before_stream' => $tabBeforeStream,
                'padding_space_count' => $paddingSpaceCount,
            ];
        }

        return null;
    }
}
