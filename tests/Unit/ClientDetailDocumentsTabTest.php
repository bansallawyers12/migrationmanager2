<?php

namespace Tests\Unit;

use App\Models\Document;
use App\Support\ClientDetailDocumentsTab;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @property mixed $app
 */
class ClientDetailDocumentsTabTest extends TestCase
{
    #[Test]
    public function personal_documents_are_grouped_by_folder_with_eager_staff(): void
    {
        $this->createDocumentsSchema();

        DB::table('staff')->insert([
            'id' => 5,
            'first_name' => 'Pat',
            'last_name' => 'Uploader',
            'email' => 'pat@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('documents')->insert([
            [
                'client_id' => 10,
                'user_id' => 5,
                'doc_type' => 'personal',
                'type' => 'client',
                'folder_name' => '1',
                'file_name' => 'a.pdf',
                'checklist' => 'Passport',
                'not_used_doc' => null,
                'created_at' => now()->subMinute(),
                'updated_at' => now()->subMinute(),
            ],
            [
                'client_id' => 10,
                'user_id' => 5,
                'doc_type' => 'personal',
                'type' => 'client',
                'folder_name' => '2',
                'file_name' => 'b.pdf',
                'checklist' => 'Resume',
                'not_used_doc' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 10,
                'user_id' => 5,
                'doc_type' => 'personal',
                'type' => 'client',
                'folder_name' => '1',
                'file_name' => null,
                'checklist' => 'Unused checklist',
                'not_used_doc' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $byFolder = ClientDetailDocumentsTab::personalDocumentsByFolder(10);

        Assert::assertTrue($byFolder->has('1'));
        Assert::assertTrue($byFolder->has('2'));
        Assert::assertCount(1, $byFolder->get('1'));
        Assert::assertCount(1, $byFolder->get('2'));

        /** @var Document $doc */
        $doc = $byFolder->get('1')->first();
        Assert::assertTrue($doc->relationLoaded('staff'));
        Assert::assertSame('Pat', $doc->staff->first_name);
    }

    #[Test]
    public function visa_documents_eager_load_staff_and_signers(): void
    {
        $this->createDocumentsSchema();

        DB::table('staff')->insert([
            'id' => 7,
            'first_name' => 'Visa',
            'last_name' => 'Staff',
            'email' => 'visa@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('documents')->insert([
            'client_id' => 11,
            'user_id' => 7,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => '3',
            'file_name' => 'form.pdf',
            'checklist' => 'Form 80',
            'not_used_doc' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $byFolder = ClientDetailDocumentsTab::visaDocumentsByFolder(11);
        Assert::assertCount(1, $byFolder->get('3'));

        /** @var Document $doc */
        $doc = $byFolder->get('3')->first();
        Assert::assertTrue($doc->relationLoaded('staff'));
        Assert::assertTrue($doc->relationLoaded('signers'));
        Assert::assertSame('Visa', $doc->staff->first_name);
    }

    #[Test]
    public function not_used_documents_eager_load_staff(): void
    {
        $this->createDocumentsSchema();

        DB::table('staff')->insert([
            'id' => 9,
            'first_name' => 'Not',
            'last_name' => 'Used',
            'email' => 'notused@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('documents')->insert([
            'client_id' => 12,
            'user_id' => 9,
            'doc_type' => 'personal',
            'type' => 'client',
            'folder_name' => '1',
            'file_name' => 'old.pdf',
            'checklist' => 'Old',
            'not_used_doc' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $docs = ClientDetailDocumentsTab::notUsedDocuments(12);
        Assert::assertCount(1, $docs);
        Assert::assertTrue($docs->first()->relationLoaded('staff'));
        Assert::assertSame('Not', $docs->first()->staff->first_name);
    }

    #[Test]
    public function dibp_receipt_documents_are_scoped_to_client_matter_and_exclude_other_doc_types(): void
    {
        $this->createDocumentsSchema();

        $now = now();
        DB::table('documents')->insert([
            [
                'client_id' => 20,
                'client_matter_id' => 4,
                'doc_type' => ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE,
                'type' => 'client',
                'folder_name' => ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME,
                'file_name' => 'dibp.pdf',
                'checklist' => 'DIBP Receipt',
                'not_used_doc' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'client_id' => 20,
                'client_matter_id' => 5,
                'doc_type' => ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE,
                'type' => 'client',
                'folder_name' => ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME,
                'file_name' => 'other-matter.pdf',
                'checklist' => 'Other matter',
                'not_used_doc' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'client_id' => 20,
                'client_matter_id' => 4,
                'doc_type' => ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE,
                'type' => 'client',
                'folder_name' => 'not-general',
                'file_name' => 'wrong-folder.pdf',
                'checklist' => 'Wrong folder',
                'not_used_doc' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'client_id' => 20,
                'client_matter_id' => 4,
                'doc_type' => 'visa',
                'type' => 'client',
                'folder_name' => '1',
                'file_name' => 'visa.pdf',
                'checklist' => 'Visa checklist',
                'not_used_doc' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'client_id' => 20,
                'client_matter_id' => null,
                'doc_type' => 'personal',
                'type' => 'client',
                'folder_name' => '1',
                'file_name' => 'personal.pdf',
                'checklist' => 'Personal checklist',
                'not_used_doc' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'client_id' => 20,
                'client_matter_id' => 4,
                'doc_type' => ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE,
                'type' => 'client',
                'folder_name' => ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME,
                'file_name' => 'unused.pdf',
                'checklist' => 'Unused',
                'not_used_doc' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $dibp = ClientDetailDocumentsTab::dibpReceiptDocuments(20, 4);
        Assert::assertCount(1, $dibp);
        Assert::assertSame('dibp.pdf', $dibp->first()->file_name);
        Assert::assertSame(ClientDetailDocumentsTab::DIBP_RECEIPT_DOC_TYPE, $dibp->first()->doc_type);

        $visa = ClientDetailDocumentsTab::visaDocumentsByFolder(20);
        Assert::assertTrue($visa->has('1'));
        Assert::assertCount(1, $visa->get('1'));
        Assert::assertSame('visa.pdf', $visa->get('1')->first()->file_name);
        Assert::assertFalse($visa->has(ClientDetailDocumentsTab::DIBP_RECEIPT_FOLDER_NAME));

        $personal = ClientDetailDocumentsTab::personalDocumentsByFolder(20);
        Assert::assertCount(1, $personal->get('1'));
        Assert::assertSame('personal.pdf', $personal->get('1')->first()->file_name);

        $notUsed = ClientDetailDocumentsTab::notUsedDocuments(20);
        Assert::assertCount(0, $notUsed);
    }

    #[Test]
    public function visa_bulk_resolve_prefers_empty_current_matter_then_legacy_null_matter(): void
    {
        $this->createDocumentsSchema();

        $legacyEmpty = Document::query()->create([
            'client_id' => 30,
            'user_id' => 2,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => '5',
            'checklist' => 'Payment Receipt',
            'client_matter_id' => null,
            'file_name' => null,
        ]);
        $currentEmpty = Document::query()->create([
            'client_id' => 30,
            'user_id' => 2,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => '5',
            'checklist' => 'Payment Receipt',
            'client_matter_id' => 8,
            'file_name' => null,
        ]);

        $resolved = ClientDetailDocumentsTab::resolveFolderChecklistForBulk(
            30,
            2,
            'visa',
            '5',
            'client',
            ['type' => 'existing', 'name' => 'Payment Receipt'],
            8
        );

        Assert::assertNotNull($resolved);
        Assert::assertSame($currentEmpty->id, $resolved->id);
        Assert::assertNotSame($legacyEmpty->id, $resolved->id);
    }

    #[Test]
    public function visa_bulk_resolve_uses_empty_legacy_row_when_current_matter_has_none(): void
    {
        $this->createDocumentsSchema();

        $legacyEmpty = Document::query()->create([
            'client_id' => 31,
            'user_id' => 2,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => '5',
            'checklist' => 'Extension Letter',
            'client_matter_id' => null,
            'file_name' => null,
        ]);

        $resolved = ClientDetailDocumentsTab::resolveFolderChecklistForBulk(
            31,
            2,
            'visa',
            '5',
            'client',
            ['type' => 'existing', 'name' => 'Extension Letter'],
            8
        );

        Assert::assertNotNull($resolved);
        Assert::assertSame($legacyEmpty->id, $resolved->id);
    }

    #[Test]
    public function visa_bulk_resolve_creates_on_current_matter_when_name_only_exists_elsewhere(): void
    {
        $this->createDocumentsSchema();

        Document::query()->create([
            'client_id' => 32,
            'user_id' => 2,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => '5',
            'checklist' => 'Payment Receipt',
            'client_matter_id' => 99,
            'file_name' => 'other.pdf',
        ]);
        Document::query()->create([
            'client_id' => 32,
            'user_id' => 2,
            'doc_type' => 'personal',
            'type' => 'client',
            'folder_name' => '5',
            'checklist' => 'Payment Receipt',
            'client_matter_id' => 8,
            'file_name' => null,
        ]);

        $resolved = ClientDetailDocumentsTab::resolveFolderChecklistForBulk(
            32,
            2,
            'visa',
            '5',
            'client',
            ['type' => 'existing', 'name' => 'Payment Receipt'],
            8
        );

        Assert::assertNotNull($resolved);
        Assert::assertSame('visa', $resolved->doc_type);
        Assert::assertSame('Payment Receipt', $resolved->checklist);
        Assert::assertSame(8, (int) $resolved->client_matter_id);
        Assert::assertNull($resolved->file_name);
        Assert::assertSame(3, Document::query()->where('client_id', 32)->count());
    }

    #[Test]
    public function visa_bulk_resolve_creates_another_row_when_current_matter_slots_are_full(): void
    {
        $this->createDocumentsSchema();

        $filled = Document::query()->create([
            'client_id' => 33,
            'user_id' => 2,
            'doc_type' => 'visa',
            'type' => 'client',
            'folder_name' => '5',
            'checklist' => 'Form 80',
            'client_matter_id' => 8,
            'file_name' => 'form80.pdf',
        ]);

        $resolved = ClientDetailDocumentsTab::resolveFolderChecklistForBulk(
            33,
            2,
            'visa',
            '5',
            'client',
            ['type' => 'existing', 'name' => 'Form 80'],
            8
        );

        Assert::assertNotNull($resolved);
        Assert::assertNotSame($filled->id, $resolved->id);
        Assert::assertSame('Form 80', $resolved->checklist);
        Assert::assertSame(8, (int) $resolved->client_matter_id);
        Assert::assertNull($resolved->file_name);
    }

    #[Test]
    public function visa_bulk_mapping_ui_only_lists_visible_current_matter_checklists(): void
    {
        $visaBlade = file_get_contents($this->projectPath('resources/views/crm/clients/tabs/visa_documents.blade.php'));
        Assert::assertNotFalse($visaBlade);
        Assert::assertStringContainsString('function visaBulkChecklistRowIsSelectable', $visaBlade);
        Assert::assertStringContainsString("#visadocuments-tab .migdocumnetlist_' + categoryId + ' .visachecklist-row", $visaBlade);
        Assert::assertStringContainsString('$row.is(\':visible\')', $visaBlade);
        Assert::assertStringContainsString('currentVisaMatterId', $visaBlade);
        Assert::assertStringContainsString('resolveFolderChecklistForBulk', file_get_contents($this->projectPath('app/Http/Controllers/CRM/Clients/ClientDocumentsController.php')));

        $nominationBlade = file_get_contents($this->projectPath('resources/views/crm/companies/tabs/nomination_documents.blade.php'));
        Assert::assertNotFalse($nominationBlade);
        Assert::assertStringContainsString("#nominationdocuments-tab .migdocumnetlist_' + categoryId + ' .visachecklist-row", $nominationBlade);
        Assert::assertStringContainsString('function visaBulkChecklistRowIsSelectable', $nominationBlade);

        $personalBlade = file_get_contents($this->projectPath('resources/views/crm/clients/tabs/personal_documents.blade.php'));
        Assert::assertNotFalse($personalBlade);
        Assert::assertStringNotContainsString('visaBulkChecklistRowIsSelectable', $personalBlade);
        Assert::assertStringNotContainsString('resolveFolderChecklistForBulk', $personalBlade);
    }

    #[Test]
    public function document_tab_blades_do_not_run_per_row_staff_lookups_or_per_category_document_queries(): void
    {
        foreach ([
            'resources/views/crm/clients/tabs/personal_documents.blade.php',
            'resources/views/crm/clients/tabs/visa_documents.blade.php',
            'resources/views/crm/clients/tabs/not_used_documents.blade.php',
        ] as $relative) {
            $contents = file_get_contents($this->projectPath($relative));
            Assert::assertNotFalse($contents);
            Assert::assertStringContainsString('ClientDetailDocumentsTab::', $contents);
            if (str_contains($contents, 'class="grid_data')) {
                Assert::assertMatchesRegularExpression(
                    '/class="grid_data[^"]*"[^>]*style="display:\s*none;"/',
                    $contents,
                    $relative.' must hide .grid_data by default so the checklist list view is not squeezed.'
                );
            }
            Assert::assertStringNotContainsString('Staff::where', $contents);
            Assert::assertStringNotContainsString('Staff::find', $contents);
            Assert::assertStringNotContainsString('Admin::find', $contents);
            Assert::assertStringNotContainsString('Admin::where', $contents);
            Assert::assertStringNotContainsString('\\App\\Models\\Document::', $contents);
        }
    }

    private function createDocumentsSchema(): void
    {
        if (! Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
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

        if (! Schema::hasTable('signature_activities')) {
            Schema::create('signature_activities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('document_id')->nullable();
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
