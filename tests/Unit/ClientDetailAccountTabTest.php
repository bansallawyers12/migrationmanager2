<?php

namespace Tests\Unit;

use App\Models\DocumentChecklist;
use App\Support\ClientDetailAccountTab;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @property mixed $app
 */
class ClientDetailAccountTabTest extends TestCase
{
    #[Test]
    public function build_returns_empty_ledger_payload_when_the_client_has_no_receipts(): void
    {
        $this->createAccountTabSchema();

        $payload = ClientDetailAccountTab::build((object) [
            'id' => 1,
            'client_id' => 'C001',
        ]);

        Assert::assertNull($payload['client_selected_matter_id']);
        Assert::assertSame(0.0, $payload['calculated_balance']);
        Assert::assertSame(0.0, $payload['latest_outstanding_balance']);
        Assert::assertEmpty($payload['receipts_lists']);
        Assert::assertEmpty($payload['receipts_lists_office']);
        Assert::assertSame([], $payload['receipts_lists_invoice']);
        Assert::assertTrue($payload['dibp_receipts_lists']->isEmpty());
        Assert::assertTrue($payload['dibp_receipts_checklists']->isEmpty());
    }

    #[Test]
    public function build_keeps_latest_invoice_per_receipt_and_skips_voided_transfers_in_balance(): void
    {
        $this->createAccountTabSchema();

        $this->insertMoneyEntriesForClient(1);

        $payload = ClientDetailAccountTab::build((object) [
            'id' => 1,
            'client_id' => 'C001',
        ]);

        Assert::assertSame(40.0, $payload['calculated_balance']);
        Assert::assertSame(35.0, $payload['latest_outstanding_balance']);
        Assert::assertCount(3, $payload['receipts_lists']);
        Assert::assertCount(1, $payload['receipts_lists_invoice']);
        Assert::assertSame(25.0, (float) $payload['receipts_lists_invoice'][0]->balance_amount);
        Assert::assertCount(1, $payload['receipts_lists_office']);
        Assert::assertTrue($payload['dibp_receipts_lists']->isEmpty());
        Assert::assertTrue($payload['dibp_receipts_checklists']->isEmpty());
    }

    #[Test]
    public function dibp_receipt_documents_do_not_change_ledger_invoice_or_office_money_entries(): void
    {
        $this->createAccountTabSchema();

        $this->insertMoneyEntriesForClient(1);

        $before = ClientDetailAccountTab::build((object) [
            'id' => 1,
            'client_id' => 'C001',
        ]);

        DB::table('documents')->insert([
            [
                'client_id' => 1,
                'client_matter_id' => null,
                'doc_type' => 'dibp_receipt',
                'type' => 'client',
                'folder_name' => 'general',
                'file_name' => 'lodgement.pdf',
                'checklist' => 'DIBP lodgement',
                'not_used_doc' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 1,
                'client_matter_id' => null,
                'doc_type' => 'visa',
                'type' => 'client',
                'folder_name' => '1',
                'file_name' => 'visa.pdf',
                'checklist' => 'Visa form',
                'not_used_doc' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 1,
                'client_matter_id' => null,
                'doc_type' => 'personal',
                'type' => 'client',
                'folder_name' => '1',
                'file_name' => 'passport.pdf',
                'checklist' => 'Passport',
                'not_used_doc' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $after = ClientDetailAccountTab::build((object) [
            'id' => 1,
            'client_id' => 'C001',
        ]);

        Assert::assertSame($before['calculated_balance'], $after['calculated_balance']);
        Assert::assertSame($before['latest_outstanding_balance'], $after['latest_outstanding_balance']);
        Assert::assertCount($before['receipts_lists']->count(), $after['receipts_lists']);
        Assert::assertCount(count($before['receipts_lists_invoice']), $after['receipts_lists_invoice']);
        Assert::assertCount($before['receipts_lists_office']->count(), $after['receipts_lists_office']);
        Assert::assertSame(40.0, $after['calculated_balance']);
        Assert::assertSame(35.0, $after['latest_outstanding_balance']);
        Assert::assertCount(3, $after['receipts_lists']);
        Assert::assertCount(1, $after['receipts_lists_invoice']);
        Assert::assertSame(25.0, (float) $after['receipts_lists_invoice'][0]->balance_amount);
        Assert::assertCount(1, $after['receipts_lists_office']);
        Assert::assertCount(1, $after['dibp_receipts_lists']);
        Assert::assertSame('lodgement.pdf', $after['dibp_receipts_lists']->first()->file_name);
        Assert::assertSame('dibp_receipt', $after['dibp_receipts_lists']->first()->doc_type);
        Assert::assertNotSame('visa', $after['dibp_receipts_lists']->first()->doc_type);
        Assert::assertNotSame('personal', $after['dibp_receipts_lists']->first()->doc_type);
        Assert::assertTrue($after['dibp_receipts_checklists']->isEmpty());
    }

    #[Test]
    public function build_loads_only_active_dibp_receipt_checklists_and_not_personal_visa_or_nomination(): void
    {
        $this->createAccountTabSchema();

        DocumentChecklist::query()->create(['name' => 'Passport', 'doc_type' => DocumentChecklist::DOC_TYPE_PERSONAL, 'status' => 1]);
        DocumentChecklist::query()->create(['name' => 'Visa form', 'doc_type' => DocumentChecklist::DOC_TYPE_VISA, 'status' => 1]);
        DocumentChecklist::query()->create(['name' => 'Nomination form', 'doc_type' => DocumentChecklist::DOC_TYPE_NOMINATION, 'status' => 1]);
        DocumentChecklist::query()->create(['name' => 'Lodgement', 'doc_type' => DocumentChecklist::DOC_TYPE_DIBP_RECEIPT, 'status' => 1]);
        DocumentChecklist::query()->create(['name' => 'Hidden lodgement', 'doc_type' => DocumentChecklist::DOC_TYPE_DIBP_RECEIPT, 'status' => 0]);

        $payload = ClientDetailAccountTab::build((object) [
            'id' => 1,
            'client_id' => 'C001',
        ]);

        Assert::assertSame(['Lodgement'], $payload['dibp_receipts_checklists']->pluck('name')->all());
        Assert::assertTrue($payload['dibp_receipts_checklists']->every(
            fn (DocumentChecklist $checklist): bool => (int) $checklist->doc_type === DocumentChecklist::DOC_TYPE_DIBP_RECEIPT
        ));
        Assert::assertEmpty($payload['receipts_lists']);
    }

    private function createAccountTabSchema(): void
    {
        $schema = $this->app->make('db')->connection()->getSchemaBuilder();

        if (! $schema->hasTable('client_matters')) {
            $schema->create('client_matters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->string('client_unique_matter_no')->nullable();
                $table->unsignedInteger('matter_status')->nullable();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('account_client_receipts')) {
            $schema->create('account_client_receipts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
                $table->unsignedBigInteger('receipt_id')->nullable();
                $table->unsignedInteger('receipt_type')->nullable();
                $table->decimal('deposit_amount', 12, 2)->nullable();
                $table->decimal('withdraw_amount', 12, 2)->nullable();
                $table->decimal('balance_amount', 12, 2)->nullable();
                $table->unsignedInteger('void_fee_transfer')->nullable();
                $table->unsignedInteger('invoice_status')->nullable();
                $table->string('invoice_no')->nullable();
                $table->unsignedBigInteger('uploaded_doc_id')->nullable();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('documents')) {
            $schema->create('documents', function (Blueprint $table) {
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
                $table->unsignedTinyInteger('not_used_doc')->nullable();
                $table->timestamps();
            });
        } elseif (! $schema->hasColumn('documents', 'doc_type')) {
            $schema->table('documents', function (Blueprint $table) {
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
                $table->string('doc_type')->nullable();
                $table->string('type')->nullable();
                $table->string('folder_name')->nullable();
                $table->string('file_name')->nullable();
                $table->string('checklist')->nullable();
                $table->unsignedTinyInteger('not_used_doc')->nullable();
            });
        }

        if (! $schema->hasTable('staff')) {
            $schema->create('staff', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('portal_document_checklists')) {
            $schema->create('portal_document_checklists', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->unsignedTinyInteger('doc_type')->nullable();
                $table->unsignedTinyInteger('status')->nullable();
                $table->timestamps();
            });
        }
    }

    private function insertMoneyEntriesForClient(int $clientId): void
    {
        DB::table('account_client_receipts')->insert([
            [
                'client_id' => $clientId,
                'client_matter_id' => null,
                'receipt_id' => 10,
                'receipt_type' => 1,
                'deposit_amount' => 50,
                'withdraw_amount' => 0,
                'balance_amount' => 0,
                'void_fee_transfer' => 0,
                'invoice_status' => null,
                'invoice_no' => null,
                'uploaded_doc_id' => null,
            ],
            [
                'client_id' => $clientId,
                'client_matter_id' => null,
                'receipt_id' => 11,
                'receipt_type' => 1,
                'deposit_amount' => 0,
                'withdraw_amount' => 10,
                'balance_amount' => 0,
                'void_fee_transfer' => 0,
                'invoice_status' => null,
                'invoice_no' => null,
                'uploaded_doc_id' => null,
            ],
            [
                'client_id' => $clientId,
                'client_matter_id' => null,
                'receipt_id' => 12,
                'receipt_type' => 1,
                'deposit_amount' => 100,
                'withdraw_amount' => 0,
                'balance_amount' => 0,
                'void_fee_transfer' => 1,
                'invoice_status' => null,
                'invoice_no' => null,
                'uploaded_doc_id' => null,
            ],
            [
                'client_id' => $clientId,
                'client_matter_id' => null,
                'receipt_id' => 20,
                'receipt_type' => 3,
                'deposit_amount' => 0,
                'withdraw_amount' => 0,
                'balance_amount' => 10,
                'void_fee_transfer' => 0,
                'invoice_status' => 0,
                'invoice_no' => 'INV-1',
                'uploaded_doc_id' => null,
            ],
            [
                'client_id' => $clientId,
                'client_matter_id' => null,
                'receipt_id' => 20,
                'receipt_type' => 3,
                'deposit_amount' => 0,
                'withdraw_amount' => 0,
                'balance_amount' => 25,
                'void_fee_transfer' => 0,
                'invoice_status' => 0,
                'invoice_no' => 'INV-1',
                'uploaded_doc_id' => null,
            ],
            [
                'client_id' => $clientId,
                'client_matter_id' => null,
                'receipt_id' => 30,
                'receipt_type' => 2,
                'deposit_amount' => 40,
                'withdraw_amount' => 0,
                'balance_amount' => 0,
                'void_fee_transfer' => 0,
                'invoice_status' => null,
                'invoice_no' => null,
                'uploaded_doc_id' => null,
            ],
        ]);
    }
}
