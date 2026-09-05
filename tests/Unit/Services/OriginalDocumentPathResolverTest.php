<?php

namespace Tests\Unit\Services;

use App\Models\Document;
use App\Services\OriginalDocumentPathResolver;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OriginalDocumentPathResolverTest extends TestCase
{
    #[Test]
    public function it_locates_local_original_and_ignores_signed_copy(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('documents/agreement.pdf', 'ORIGINAL-BYTES');
        Storage::disk('public')->put('signed/10_signed.pdf', 'SIGNED-BYTES');

        $document = new Document([
            'file_name' => 'agreement.pdf',
            'myfile' => 'documents/agreement.pdf',
            'signed_doc_link' => 'https://example.com/storage/signed/10_signed.pdf',
        ]);
        $document->id = 10;

        $location = OriginalDocumentPathResolver::locateOriginalPdfFile($document);

        $this->assertNotNull($location);
        $this->assertSame('local', $location['disk']);
        $this->assertSame('ORIGINAL-BYTES', file_get_contents($location['path']));
    }

    #[Test]
    public function it_never_falls_back_to_signed_doc_link(): void
    {
        Storage::fake('public');
        Storage::fake('s3');

        Storage::disk('public')->put('signed/11_signed.pdf', 'SIGNED-BYTES');
        Storage::disk('s3')->put('DON123/agreement/signed/11_signed.pdf', 'SIGNED-S3');

        $document = new Document([
            'myfile' => null,
            'myfile_key' => null,
            'signed_doc_link' => 'https://example.com/storage/signed/11_signed.pdf',
            'doc_type' => 'agreement',
            'user_id' => 5,
        ]);
        $document->id = 11;

        $this->assertNull(OriginalDocumentPathResolver::locateOriginalPdfFile($document));
    }

    #[Test]
    public function it_locates_s3_original_from_virtual_hosted_url(): void
    {
        Storage::fake('s3');
        Config::set('filesystems.disks.s3.bucket', 'my-bucket');

        $s3Key = 'DON123/agreement/JRP_Agreement.pdf';
        Storage::disk('s3')->put($s3Key, 'ORIGINAL-S3');

        $document = new Document([
            'myfile' => 'https://my-bucket.s3.amazonaws.com/DON123/agreement/JRP_Agreement.pdf',
            'signed_doc_link' => 'https://my-bucket.s3.amazonaws.com/DON123/agreement/signed/12_signed.pdf',
        ]);
        $document->id = 12;

        Storage::disk('s3')->put('DON123/agreement/signed/12_signed.pdf', 'SIGNED-S3');

        $location = OriginalDocumentPathResolver::locateOriginalPdfFile($document);

        $this->assertNotNull($location);
        $this->assertSame('s3', $location['disk']);
        $this->assertSame($s3Key, $location['key']);
    }

    #[Test]
    public function it_uses_original_filename_without_signed_suffix(): void
    {
        $document = new Document([
            'file_name' => 'JRP Agreement_DEEP2503763.pdf',
            'filetype' => 'pdf',
        ]);
        $document->id = 13;

        $this->assertSame('JRP Agreement_DEEP2503763.pdf', $document->getOriginalDownloadFilename());
        $this->assertStringNotContainsString('_signed', $document->getOriginalDownloadFilename());
    }
}
