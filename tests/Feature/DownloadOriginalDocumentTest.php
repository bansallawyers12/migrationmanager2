<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Models\Document;
use App\Models\Staff;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DownloadOriginalDocumentTest extends TestCase
{
    protected Staff $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([TrackStaffCrmActivity::class]);

        $this->createSchema();

        $this->staff = Staff::create([
            'first_name' => 'Akanksha',
            'last_name' => 'Sharma',
            'email' => 'original-download@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);
    }

    #[Test]
    public function original_download_route_is_registered_separately_from_signed(): void
    {
        $this->assertTrue(Route::has('documents.download.original'));
        $this->assertTrue(Route::has('documents.download.signed'));
        $this->assertNotSame(
            route('documents.download.original', 1),
            route('documents.download.signed', 1)
        );
    }

    #[Test]
    public function guests_cannot_download_the_original(): void
    {
        $document = $this->makeDocument('documents/guest.pdf', 'sent');
        Storage::fake('public');
        Storage::disk('public')->put('documents/guest.pdf', 'ORIGINAL');

        $this->get(route('documents.download.original', $document->id))
            ->assertRedirect();
    }

    #[Test]
    public function staff_can_download_original_without_changing_document_or_signed_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/jrp.pdf', 'UNSIGNED-PDF');
        Storage::disk('public')->put('signed/20_signed.pdf', 'SIGNED-PDF');

        $document = $this->makeDocument('documents/jrp.pdf', 'signed', 'https://example.com/storage/signed/20_signed.pdf');

        $response = $this->actingAs($this->staff, 'admin')
            ->get(route('documents.download.original', $document->id));

        $response->assertOk();
        $this->assertStringContainsString('UNSIGNED-PDF', $response->streamedContent());
        $this->assertStringContainsString('jrp.pdf', (string) $response->headers->get('content-disposition'));

        $fresh = $document->fresh();
        $this->assertSame('documents/jrp.pdf', $fresh->myfile);
        $this->assertSame('https://example.com/storage/signed/20_signed.pdf', $fresh->signed_doc_link);
        $this->assertSame('signed', $fresh->status);
    }

    #[Test]
    public function signed_download_still_returns_the_signed_copy(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/jrp.pdf', 'UNSIGNED-PDF');
        Storage::disk('public')->put('signed/21_signed.pdf', 'SIGNED-PDF');

        $document = $this->makeDocument('documents/jrp.pdf', 'signed', 'https://example.com/storage/signed/21_signed.pdf');

        $response = $this->actingAs($this->staff, 'admin')
            ->get(route('documents.download.signed', $document->id));

        $response->assertOk();
        $this->assertStringContainsString('SIGNED-PDF', $response->streamedContent());
        $this->assertStringNotContainsString('UNSIGNED-PDF', $response->streamedContent());
    }

    #[Test]
    public function missing_original_file_returns_not_found(): void
    {
        Storage::fake('public');

        $document = $this->makeDocument('documents/missing.pdf', 'sent');

        $this->actingAs($this->staff, 'admin')
            ->get(route('documents.download.original', $document->id))
            ->assertNotFound();
    }

    #[Test]
    public function signature_details_view_references_original_and_signed_download_routes(): void
    {
        $blade = file_get_contents(resource_path('views/crm/signatures/show.blade.php'));
        $this->assertNotFalse($blade);
        $this->assertStringContainsString("route('documents.download.original'", $blade);
        $this->assertStringContainsString("route('documents.download.signed'", $blade);
        $this->assertStringContainsString('Download Original', $blade);
    }

    private function makeDocument(string $myfile, string $status, ?string $signedDocLink = null): Document
    {
        $document = new Document;
        $document->file_name = 'jrp.pdf';
        $document->filetype = 'pdf';
        $document->myfile = $myfile;
        $document->status = $status;
        $document->signed_doc_link = $signedDocLink;
        $document->created_by = $this->staff->id;
        $document->save();

        return $document;
    }

    private function createSchema(): void
    {
        if (! Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->unsignedInteger('role')->nullable();
                $table->unsignedInteger('status')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->string('file_name')->nullable();
                $table->string('filetype')->nullable();
                $table->string('myfile')->nullable();
                $table->string('myfile_key')->nullable();
                $table->string('status')->nullable();
                $table->string('signed_doc_link')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('lead_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('doc_type')->nullable();
                $table->timestamps();
            });
        }
    }
}
