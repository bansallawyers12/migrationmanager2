<?php

namespace Tests\Unit;

use App\Http\Middleware\TrackStaffCrmActivity;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Document;
use App\Models\DocumentChecklist;
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

class DibpReceiptChecklistTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            VerifyCsrfToken::class,
            TrackStaffCrmActivity::class,
        ]);

        $this->createChecklistSchema();
    }

    #[Test]
    public function add_checklist_always_saves_dibp_receipt_and_general(): void
    {
        $document = ClientDetailDocumentsTab::addChecklist(20, 3, 'DIBP lodgement', 4);

        Assert::assertInstanceOf(Document::class, $document);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE, $document->doc_type);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME, $document->folder_name);
        Assert::assertSame('client', $document->type);
        Assert::assertSame('DIBP lodgement', $document->checklist);
        Assert::assertSame(20, (int) $document->client_id);
        Assert::assertSame(4, (int) $document->client_matter_id);

        $visa = ClientDetailDocumentsTab::visaDocumentsByFolder(20);
        Assert::assertTrue($visa->isEmpty());
        $personal = ClientDetailDocumentsTab::personalDocumentsByFolder(20);
        Assert::assertTrue($personal->isEmpty());
    }

    #[Test]
    public function store_endpoint_creates_dibp_receipt_row_and_ignores_spoofed_visa_type(): void
    {
        $staff = Staff::query()->create([
            'first_name' => 'Dibp',
            'last_name' => 'Tester',
            'email' => 'dibp-checklist@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        DocumentChecklist::query()->create([
            'name' => 'Receipt scan',
            'doc_type' => DocumentChecklist::DOC_TYPE_DIBP_RECEIPT,
            'status' => 1,
        ]);

        $response = $this->actingAs($staff, 'admin')
            ->postJson('/documents/add-dibp-receipt-checklist', [
                'clientid' => 20,
                'checklist' => 'Receipt scan',
                'client_matter_id' => 8,
                'doctype' => 'visa',
                'folder_name' => '1',
            ]);

        $response->assertOk();
        $payload = $response->json();
        Assert::assertTrue($payload['status'] ?? false);
        Assert::assertSame('Receipt scan', $payload['document']['checklist'] ?? null);

        $document = Document::query()->find($payload['document']['id']);
        Assert::assertNotNull($document);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE, $document->doc_type);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME, $document->folder_name);
        Assert::assertSame(8, (int) $document->client_matter_id);
        Assert::assertNotSame('visa', $document->doc_type);
    }

    #[Test]
    public function store_endpoint_rejects_personal_visa_nomination_and_inactive_catalog_names(): void
    {
        $staff = Staff::query()->create([
            'first_name' => 'Dibp',
            'last_name' => 'Rejector',
            'email' => 'dibp-reject@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        DocumentChecklist::query()->create(['name' => 'Passport', 'doc_type' => DocumentChecklist::DOC_TYPE_PERSONAL, 'status' => 1]);
        DocumentChecklist::query()->create(['name' => 'Visa form', 'doc_type' => DocumentChecklist::DOC_TYPE_VISA, 'status' => 1]);
        DocumentChecklist::query()->create(['name' => 'Nomination form', 'doc_type' => DocumentChecklist::DOC_TYPE_NOMINATION, 'status' => 1]);
        DocumentChecklist::query()->create(['name' => 'Hidden lodgement', 'doc_type' => DocumentChecklist::DOC_TYPE_DIBP_RECEIPT, 'status' => 0]);

        foreach (['Passport', 'Visa form', 'Nomination form', 'Hidden lodgement', 'Not in catalog'] as $name) {
            $response = $this->actingAs($staff, 'admin')
                ->postJson('/documents/add-dibp-receipt-checklist', [
                    'clientid' => 20,
                    'checklist' => $name,
                    'client_matter_id' => 8,
                ]);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['checklist']);
        }

        Assert::assertSame(0, Document::query()->where('doc_type', ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE)->count());
    }

    #[Test]
    public function visa_add_checklist_endpoint_and_markup_still_use_visa_doc_type(): void
    {
        $controller = file_get_contents($this->projectPath('app/Http/Controllers/CRM/Clients/ClientDocumentsController.php'));
        Assert::assertNotFalse($controller);
        Assert::assertStringContainsString('public function addvisadocchecklist', $controller);
        Assert::assertStringContainsString('$obj->doc_type = $doctype;', $controller);
        Assert::assertStringContainsString('name="doctype" value="visa"', file_get_contents(
            $this->projectPath('resources/views/crm/clients/modals/checklists.blade.php')
        ) ?: '');

        $visa = file_get_contents($this->projectPath('resources/views/crm/clients/tabs/visa_documents.blade.php'));
        Assert::assertNotFalse($visa);
        Assert::assertStringContainsString('add_migration_doc', $visa);
        Assert::assertStringContainsString('data-type="visa"', $visa);
        Assert::assertStringNotContainsString('dibp-receipts-add-checklist', $visa);
    }

    #[Test]
    public function dibp_receipt_upload_stores_under_dibp_receipt_and_rejects_visa_rows(): void
    {
        Storage::fake('s3');

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
            ['id' => 20],
            [
                'client_id' => 'C20',
                'first_name' => 'Test',
                'is_company' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $staff = Staff::query()->create([
            'first_name' => 'Dibp',
            'last_name' => 'Uploader',
            'email' => 'dibp-upload@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $dibp = ClientDetailDocumentsTab::addChecklist(20, (int) $staff->id, 'Lodgement', 4);
        $visa = Document::query()->create([
            'client_id' => 20,
            'user_id' => $staff->id,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => '1',
            'checklist' => 'Visa form',
        ]);

        $ok = $this->actingAs($staff, 'admin')
            ->post('/documents/upload-dibp-receipt-document', [
                'clientid' => 20,
                'fileid' => $dibp->id,
                'doctype' => 'visa',
                'document_upload' => UploadedFile::fake()->create('receipt.pdf', 20, 'application/pdf'),
            ]);

        $ok->assertOk();
        $payload = $ok->json();
        Assert::assertTrue($payload['status'] ?? false);
        $dibp->refresh();
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE, $dibp->doc_type);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME, $dibp->folder_name);
        Assert::assertNotEmpty($dibp->file_name);
        Storage::disk('s3')->assertExists('C20/dibp_receipt/'.$dibp->myfile_key);

        $rejected = $this->actingAs($staff, 'admin')
            ->post('/documents/upload-dibp-receipt-document', [
                'clientid' => 20,
                'fileid' => $visa->id,
                'document_upload' => UploadedFile::fake()->create('visa.pdf', 20, 'application/pdf'),
            ]);

        $rejected->assertStatus(422);
        $visa->refresh();
        Assert::assertSame('visa', $visa->doc_type);
        Assert::assertNull($visa->file_name);
    }

    #[Test]
    public function visa_and_personal_upload_methods_still_use_request_doctype_path(): void
    {
        $controller = file_get_contents($this->projectPath('app/Http/Controllers/CRM/Clients/ClientDocumentsController.php'));
        Assert::assertNotFalse($controller);
        Assert::assertStringContainsString('public function uploadedudocument', $controller);
        Assert::assertStringContainsString('public function uploadvisadocument', $controller);
        Assert::assertStringContainsString('$client_unique_id.\'/\'.$doctype.\'/\'.$name', $controller);
        Assert::assertStringNotContainsString('dibp_receipt', substr($controller, 0, strpos($controller, 'function addDibpReceiptChecklist') ?: 0) ?: $controller);

        $router = app('router');
        $visa = $router->getRoutes()->match(Request::create('/documents/upload-visa-document', 'POST'));
        Assert::assertSame('clients.documents.uploadvisadocument', $visa->getName());
        Assert::assertSame('uploadvisadocument', $visa->getActionMethod());

        $personal = $router->getRoutes()->match(Request::create('/documents/upload-edu-document', 'POST'));
        Assert::assertSame('clients.documents.uploadedudocument', $personal->getName());
        Assert::assertSame('uploadedudocument', $personal->getActionMethod());

        $dibp = $router->getRoutes()->match(Request::create('/documents/upload-dibp-receipt-document', 'POST'));
        Assert::assertSame('clients.documents.uploadDibpReceiptDocument', $dibp->getName());
        Assert::assertSame('uploadDibpReceiptDocument', $dibp->getActionMethod());

        $visaRename = $router->getRoutes()->match(Request::create('/documents/rename', 'POST'));
        Assert::assertSame('clients.documents.renamedoc', $visaRename->getName());
        Assert::assertSame('renamedoc', $visaRename->getActionMethod());

        $visaChecklistRename = $router->getRoutes()->match(Request::create('/documents/rename-checklist', 'POST'));
        Assert::assertSame('clients.documents.renamechecklistdoc', $visaChecklistRename->getName());
        Assert::assertSame('renamechecklistdoc', $visaChecklistRename->getActionMethod());

        $visaDownload = $router->getRoutes()->match(Request::create('/documents/download', 'POST'));
        Assert::assertSame('clients.documents.download', $visaDownload->getName());
        Assert::assertSame('download_document', $visaDownload->getActionMethod());
    }

    #[Test]
    public function dibp_receipt_rename_and_download_reject_visa_rows_and_keep_dibp_path(): void
    {
        Storage::fake('s3');
        $this->ensureAdminsTable();

        DB::table('admins')->updateOrInsert(
            ['id' => 20],
            [
                'client_id' => 'C20',
                'first_name' => 'Test',
                'is_company' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $staff = Staff::query()->create([
            'first_name' => 'Dibp',
            'last_name' => 'Renamer',
            'email' => 'dibp-rename@test.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'status' => 1,
        ]);

        $dibp = ClientDetailDocumentsTab::addChecklist(20, (int) $staff->id, 'Lodgement', 4);
        Storage::disk('s3')->put('C20/dibp_receipt/Test_Lodgement_1.pdf', 'receipt');
        $dibp->file_name = 'Test_Lodgement';
        $dibp->filetype = 'pdf';
        $dibp->myfile_key = 'Test_Lodgement_1.pdf';
        $dibp->myfile = Storage::disk('s3')->url('C20/dibp_receipt/Test_Lodgement_1.pdf');
        $dibp->save();

        $visa = Document::query()->create([
            'client_id' => 20,
            'user_id' => $staff->id,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => '1',
            'checklist' => 'Visa form',
            'file_name' => 'Visa_form',
            'filetype' => 'pdf',
            'myfile_key' => 'Visa_form.pdf',
        ]);

        $renamed = $this->actingAs($staff, 'admin')
            ->postJson('/documents/rename-dibp-receipt-document', [
                'clientid' => 20,
                'id' => $dibp->id,
                'filename' => 'Updated lodgement',
            ]);

        $renamed->assertOk();
        Assert::assertTrue($renamed->json('status'));
        $dibp->refresh();
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE, $dibp->doc_type);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME, $dibp->folder_name);
        Assert::assertSame('Updated lodgement', $dibp->file_name);
        Assert::assertNotEmpty($dibp->myfile_key);
        Storage::disk('s3')->assertExists('C20/dibp_receipt/'.$dibp->myfile_key);

        $checklist = $this->actingAs($staff, 'admin')
            ->postJson('/documents/rename-dibp-receipt-checklist', [
                'clientid' => 20,
                'id' => $dibp->id,
                'checklist' => 'Updated checklist',
            ]);
        $checklist->assertOk();
        Assert::assertTrue($checklist->json('status'));
        $dibp->refresh();
        Assert::assertSame('Updated checklist', $dibp->checklist);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE, $dibp->doc_type);

        $visaRename = $this->actingAs($staff, 'admin')
            ->postJson('/documents/rename-dibp-receipt-document', [
                'clientid' => 20,
                'id' => $visa->id,
                'filename' => 'Hacked visa',
            ]);
        $visaRename->assertStatus(422);
        $visa->refresh();
        Assert::assertSame('Visa_form', $visa->file_name);

        $visaChecklist = $this->actingAs($staff, 'admin')
            ->postJson('/documents/rename-dibp-receipt-checklist', [
                'clientid' => 20,
                'id' => $visa->id,
                'checklist' => 'Hacked checklist',
            ]);
        $visaChecklist->assertStatus(422);
        $visa->refresh();
        Assert::assertSame('Visa form', $visa->checklist);

        $visaDownload = $this->actingAs($staff, 'admin')
            ->post('/documents/download-dibp-receipt-document', [
                'clientid' => 20,
                'fileid' => $visa->id,
            ]);
        $visaDownload->assertStatus(422);
        Assert::assertSame('Visa_form', $visa->fresh()->file_name);

        $emptyDownload = $this->actingAs($staff, 'admin')
            ->post('/documents/download-dibp-receipt-document', [
                'clientid' => 20,
                'fileid' => ClientDetailDocumentsTab::addChecklist(20, (int) $staff->id, 'Empty', 4)->id,
            ]);
        $emptyDownload->assertStatus(422);
    }

    private function createChecklistSchema(): void
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
        } elseif (! Schema::hasColumn('documents', 'doc_type')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
                $table->string('doc_type')->nullable();
                $table->string('type')->nullable();
                $table->string('folder_name')->nullable();
                $table->string('file_name')->nullable();
                $table->string('checklist')->nullable();
                $table->string('status')->nullable();
                $table->unsignedTinyInteger('not_used_doc')->nullable();
            });
        }

        foreach (['password', 'role', 'status'] as $column) {
            if (! Schema::hasColumn('staff', $column)) {
                Schema::table('staff', function (Blueprint $table) use ($column) {
                    if ($column === 'password') {
                        $table->string($column)->nullable();
                    } else {
                        $table->unsignedInteger($column)->nullable();
                    }
                });
            }
        }

        foreach (['myfile', 'myfile_key', 'filetype'] as $column) {
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

        if (! Schema::hasTable('signers')) {
            Schema::create('signers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id')->nullable();
                $table->timestamps();
            });
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

        if (! Schema::hasTable('portal_document_checklists')) {
            Schema::create('portal_document_checklists', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->unsignedTinyInteger('doc_type')->nullable();
                $table->unsignedTinyInteger('status')->nullable();
                $table->timestamps();
            });
        }
    }

    private function ensureAdminsTable(): void
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
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
