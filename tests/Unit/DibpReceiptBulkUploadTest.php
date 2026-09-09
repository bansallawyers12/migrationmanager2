<?php

namespace Tests\Unit;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Requests\StoreDibpReceiptBulkUploadRequest;
use App\Models\Document;
use App\Models\Staff;
use App\Support\ClientDetailDocumentsTab;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DibpReceiptBulkUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
        ]);

        $this->createSchema();
    }

    #[Test]
    public function bulk_request_does_not_accept_visa_or_personal_doc_type(): void
    {
        $rules = (new StoreDibpReceiptBulkUploadRequest)->rules();

        Assert::assertArrayHasKey('clientid', $rules);
        Assert::assertArrayHasKey('files', $rules);
        Assert::assertArrayHasKey('files.*', $rules);
        Assert::assertArrayHasKey('mappings', $rules);
        Assert::assertArrayNotHasKey('doctype', $rules);
        Assert::assertArrayNotHasKey('doc_type', $rules);
        Assert::assertArrayNotHasKey('folder_name', $rules);
        Assert::assertArrayNotHasKey('categoryid', $rules);
    }

    #[Test]
    public function resolve_checklist_prefers_empty_receipt_row_and_ignores_visa(): void
    {
        $empty = ClientDetailDocumentsTab::addChecklist(20, 3, 'Lodgement', 4);
        Document::query()->create([
            'client_id' => 20,
            'user_id' => 3,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => '1',
            'checklist' => 'Lodgement',
            'client_matter_id' => 4,
        ]);

        $resolved = ClientDetailDocumentsTab::resolveChecklistForBulk(20, 3, [
            'type' => 'existing',
            'name' => 'Lodgement',
        ], 4);

        Assert::assertNotNull($resolved);
        Assert::assertSame($empty->id, $resolved->id);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE, $resolved->doc_type);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME, $resolved->folder_name);
        Assert::assertNull(ClientDetailDocumentsTab::resolveChecklistForBulk(20, 3, [
            'type' => 'existing',
            'name' => 'Visa only',
        ], 4));
    }

    #[Test]
    public function bulk_endpoint_stores_under_dibp_receipt_and_ignores_spoofed_visa_type(): void
    {
        Storage::fake('s3');
        $this->seedClient(20);

        $staff = Staff::query()->create([
            'first_name' => 'Dibp',
            'last_name' => 'Bulk',
            'email' => 'dibp-bulk@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $empty = ClientDetailDocumentsTab::addChecklist(20, (int) $staff->id, 'Lodgement', 4);
        $visa = Document::query()->create([
            'client_id' => 20,
            'user_id' => $staff->id,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => '1',
            'checklist' => 'Visa form',
            'client_matter_id' => 4,
        ]);

        $response = $this->actingAs($staff, 'admin')
            ->post('/documents/bulk-upload-dibp-receipt-documents', [
                'clientid' => 20,
                'client_matter_id' => 4,
                'doctype' => 'visa',
                'categoryid' => '1',
                'folder_name' => 'visa',
                'files' => [
                    UploadedFile::fake()->create('lodgement.pdf', 20, 'application/pdf'),
                    UploadedFile::fake()->create('new_receipt.pdf', 20, 'application/pdf'),
                ],
                'mappings' => [
                    json_encode(['type' => 'existing', 'name' => 'Lodgement']),
                    json_encode(['type' => 'new', 'name' => 'New receipt']),
                ],
            ]);

        $response->assertOk();
        $payload = $response->json();
        Assert::assertTrue($payload['status'] ?? false);
        Assert::assertSame(2, (int) ($payload['uploaded'] ?? 0));
        Assert::assertCount(2, $payload['documents'] ?? []);

        $empty->refresh();
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE, $empty->doc_type);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME, $empty->folder_name);
        Assert::assertNotEmpty($empty->file_name);
        Storage::disk('s3')->assertExists('C20/dibp_receipt/'.$empty->myfile_key);

        $created = Document::query()->where('checklist', 'New receipt')->first();
        Assert::assertNotNull($created);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE, $created->doc_type);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME, $created->folder_name);
        Assert::assertSame(4, (int) $created->client_matter_id);
        Storage::disk('s3')->assertExists('C20/dibp_receipt/'.$created->myfile_key);

        $visa->refresh();
        Assert::assertSame('visa', $visa->doc_type);
        Assert::assertNull($visa->file_name);
        Assert::assertSame(0, Document::query()->where('doc_type', 'visa')->whereNotNull('file_name')->count());
    }

    #[Test]
    public function visa_and_personal_bulk_routes_stay_on_their_own_endpoints(): void
    {
        $router = app('router');
        $visa = $router->getRoutes()->match(Request::create('/documents/bulk-upload-visa', 'POST'));
        Assert::assertSame('clients.documents.bulkUploadVisaDocuments', $visa->getName());
        Assert::assertSame('bulkUploadVisaDocuments', $visa->getActionMethod());

        $personal = $router->getRoutes()->match(Request::create('/documents/bulk-upload-personal', 'POST'));
        Assert::assertSame('clients.documents.bulkUploadPersonalDocuments', $personal->getName());
        Assert::assertSame('bulkUploadPersonalDocuments', $personal->getActionMethod());

        $dibp = $router->getRoutes()->match(Request::create('/documents/bulk-upload-dibp-receipt-documents', 'POST'));
        Assert::assertSame('clients.documents.bulkUploadDibpReceiptDocuments', $dibp->getName());
        Assert::assertSame('bulkUploadDibpReceiptDocuments', $dibp->getActionMethod());

        $controller = file_get_contents($this->projectPath('app/Http/Controllers/CRM/Clients/ClientDocumentsController.php'));
        Assert::assertNotFalse($controller);
        Assert::assertStringContainsString('public function bulkUploadVisaDocuments', $controller);
        Assert::assertStringContainsString('public function bulkUploadPersonalDocuments', $controller);
        Assert::assertStringContainsString('public function bulkUploadDibpReceiptDocuments', $controller);
    }

    private function seedClient(int $id): void
    {
        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('client_id')->nullable();
                $table->string('first_name')->nullable();
                $table->unsignedTinyInteger('is_company')->nullable();
                $table->timestamps();
            });
        }

        DB::table('admins')->updateOrInsert(
            ['id' => $id],
            [
                'client_id' => 'C'.$id,
                'first_name' => 'Test',
                'is_company' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
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
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
                $table->string('doc_type')->nullable();
                $table->string('type')->nullable();
                $table->string('folder_name')->nullable();
                $table->string('file_name')->nullable();
                $table->string('checklist')->nullable();
                $table->string('myfile')->nullable();
                $table->string('myfile_key')->nullable();
                $table->string('filetype')->nullable();
                $table->unsignedInteger('file_size')->nullable();
                $table->string('status')->nullable();
                $table->unsignedTinyInteger('not_used_doc')->nullable();
                $table->timestamps();
            });
        }

        foreach (['myfile', 'myfile_key', 'filetype', 'folder_name', 'checklist'] as $column) {
            if (! Schema::hasColumn('documents', $column)) {
                Schema::table('documents', function (Blueprint $table) use ($column) {
                    $table->string($column)->nullable();
                });
            }
        }
        if (! Schema::hasColumn('documents', 'file_size')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->unsignedInteger('file_size')->nullable();
            });
        }
        foreach (['password', 'role', 'status'] as $column) {
            if (Schema::hasTable('staff') && ! Schema::hasColumn('staff', $column)) {
                Schema::table('staff', function (Blueprint $table) use ($column) {
                    if ($column === 'password') {
                        $table->string($column)->nullable();
                    } else {
                        $table->unsignedInteger($column)->nullable();
                    }
                });
            }
        }

        if (! Schema::hasTable('client_matters')) {
            Schema::create('client_matters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('client_unique_matter_no')->nullable();
                $table->unsignedTinyInteger('matter_status')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('activities_logs')) {
            Schema::create('activities_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('subject')->nullable();
                $table->text('description')->nullable();
                $table->string('activity_type')->nullable();
                $table->unsignedTinyInteger('task_status')->nullable();
                $table->unsignedTinyInteger('pin')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('signers')) {
            Schema::create('signers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id')->nullable();
                $table->timestamps();
            });
        }
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
