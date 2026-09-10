<?php

namespace Tests\Unit;

use App\Services\ApplyInvoiceOfficeDiscount;
use App\Services\InvoicePaymentSyncService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApplyInvoiceOfficeDiscountTest extends TestCase
{
    #[Test]
    public function apply_discount_creates_office_receipt_and_reduces_existing_invoice_balance(): void
    {
        $this->createDiscountSchema();
        $this->insertFinalInvoice(1, 'INV-100', 100.00);

        $result = app(ApplyInvoiceOfficeDiscount::class)->handle(1, 'INV-100', 25.00, 'Courtesy discount', 9);

        Assert::assertTrue($result['status']);
        Assert::assertSame('INV-100', $result['invoice_no']);
        Assert::assertSame(75.0, $result['invoice_balance']);
        Assert::assertSame(2, $result['invoice_status']);

        $office = DB::table('account_client_receipts')
            ->where('receipt_type', 2)
            ->where('invoice_no', 'INV-100')
            ->get();

        Assert::assertCount(1, $office);
        Assert::assertSame(ApplyInvoiceOfficeDiscount::PAYMENT_METHOD, $office[0]->payment_method);
        Assert::assertSame(25.0, (float) $office[0]->deposit_amount);
        Assert::assertSame('Courtesy discount', $office[0]->description);
        Assert::assertSame('final', $office[0]->save_type);

        $invoiceDiscountLines = DB::table('account_client_receipts')
            ->where('receipt_type', 3)
            ->where('payment_type', 'Discount')
            ->count();
        Assert::assertSame(0, $invoiceDiscountLines);

        $invoice = DB::table('account_client_receipts')
            ->where('receipt_type', 3)
            ->where('invoice_no', 'INV-100')
            ->first();
        Assert::assertSame('Professional Fee', $invoice->payment_type);
        Assert::assertSame(100.0, (float) $invoice->withdraw_amount);
        Assert::assertSame(75.0, (float) $invoice->balance_amount);
        Assert::assertSame(2, (int) $invoice->invoice_status);
        Assert::assertSame(25.0, app(InvoicePaymentSyncService::class)->sumOfficeDiscountForInvoice(1, 'INV-100'));
    }

    #[Test]
    public function cash_office_receipt_still_reduces_invoice_balance_without_discount_line(): void
    {
        $this->createDiscountSchema();
        $this->insertFinalInvoice(1, 'INV-200', 80.00);

        DB::table('account_client_receipts')->insert([
            'client_id' => 1,
            'receipt_id' => 50,
            'receipt_type' => 2,
            'trans_no' => 'REC-050',
            'invoice_no' => 'INV-200',
            'payment_method' => 'Cash',
            'deposit_amount' => 30,
            'save_type' => 'final',
            'void_invoice' => 0,
        ]);

        $synced = app(InvoicePaymentSyncService::class)->persistPaymentState(1, 'INV-200');

        Assert::assertNotNull($synced);
        Assert::assertSame(80.0, $synced['invoice_total']);
        Assert::assertSame(30.0, $synced['total_paid']);
        Assert::assertSame(50.0, $synced['new_balance']);
        Assert::assertSame(2, $synced['invoice_status']);
        Assert::assertSame(0, DB::table('account_client_receipts')->where('payment_type', 'Discount')->count());
        Assert::assertSame(0.0, app(InvoicePaymentSyncService::class)->sumOfficeDiscountForInvoice(1, 'INV-200'));
    }

    #[Test]
    public function rejects_discount_greater_than_outstanding_and_leaves_invoice_unchanged(): void
    {
        $this->createDiscountSchema();
        $this->insertFinalInvoice(1, 'INV-300', 40.00);

        $result = app(ApplyInvoiceOfficeDiscount::class)->handle(1, 'INV-300', 40.01, null, null);

        Assert::assertFalse($result['status']);
        Assert::assertStringContainsString('cannot exceed', strtolower($result['message']));
        Assert::assertSame(0, DB::table('account_client_receipts')->where('receipt_type', 2)->count());
        Assert::assertSame(40.0, (float) DB::table('account_client_receipts')->where('receipt_type', 3)->value('balance_amount'));
    }

    #[Test]
    public function rejects_voided_draft_and_paid_invoices(): void
    {
        $this->createDiscountSchema();
        $this->insertFinalInvoice(1, 'INV-VOID', 50.00, ['void_invoice' => 1]);
        $this->insertFinalInvoice(1, 'INV-DRAFT', 50.00, ['save_type' => 'draft', 'receipt_id' => 21]);
        $this->insertFinalInvoice(1, 'INV-PAID', 50.00, ['invoice_status' => 1, 'balance_amount' => 0, 'receipt_id' => 22]);

        $voided = app(ApplyInvoiceOfficeDiscount::class)->handle(1, 'INV-VOID', 10.00, null, null);
        $draft = app(ApplyInvoiceOfficeDiscount::class)->handle(1, 'INV-DRAFT', 10.00, null, null);
        $paid = app(ApplyInvoiceOfficeDiscount::class)->handle(1, 'INV-PAID', 10.00, null, null);

        Assert::assertFalse($voided['status']);
        Assert::assertFalse($draft['status']);
        Assert::assertFalse($paid['status']);
        Assert::assertSame(0, DB::table('account_client_receipts')->where('receipt_type', 2)->count());
    }

    #[Test]
    public function account_tab_exposes_discount_option_without_new_invoice_line_form(): void
    {
        $blade = (string) file_get_contents(resource_path('views/crm/clients/tabs/account.blade.php'));
        $invoicesJs = (string) file_get_contents(public_path('js/crm/clients/modules/invoices.js'));

        Assert::assertStringContainsString('apply-invoice-discount', $blade);
        Assert::assertStringContainsString("@icon('fa-percentage') Apply Discount", $blade);
        Assert::assertStringContainsString('id="applyInvoiceDiscountModal"', $blade);
        Assert::assertStringContainsString('applyInvoiceDiscount', $invoicesJs);
        Assert::assertStringNotContainsString('payment_type[]', $blade);
    }

    #[Test]
    public function invoice_pdf_shows_office_discount_between_invoice_and_pending_totals(): void
    {
        $blade = (string) file_get_contents(resource_path('views/emails/geninvoice.blade.php'));
        $invoicePos = strpos($blade, 'Total Invoice Amount:');
        $discountPos = strpos($blade, 'Discount:');
        $pendingPos = strpos($blade, 'Total Pending Amount:');

        Assert::assertNotFalse($invoicePos);
        Assert::assertNotFalse($discountPos);
        Assert::assertNotFalse($pendingPos);
        Assert::assertGreaterThan($invoicePos, $discountPos);
        Assert::assertGreaterThan($discountPos, $pendingPos);
        Assert::assertStringContainsString('$displayOfficeDiscount > 0', $blade);
        Assert::assertStringContainsString('office_discount_amount', $blade);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function insertFinalInvoice(int $clientId, string $invoiceNo, float $amount, array $overrides = []): void
    {
        DB::table('account_client_receipts')->insert(array_merge([
            'client_id' => $clientId,
            'client_matter_id' => null,
            'receipt_id' => 20,
            'receipt_type' => 3,
            'trans_no' => $invoiceNo,
            'invoice_no' => $invoiceNo,
            'payment_type' => 'Professional Fee',
            'withdraw_amount' => $amount,
            'balance_amount' => $amount,
            'invoice_status' => 0,
            'save_type' => 'final',
            'void_invoice' => 0,
        ], $overrides));
    }

    private function createDiscountSchema(): void
    {
        $schema = $this->app->make('db')->connection()->getSchemaBuilder();

        if (! $schema->hasTable('account_client_receipts')) {
            $schema->create('account_client_receipts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('client_matter_id')->nullable();
                $table->unsignedBigInteger('receipt_id')->nullable();
                $table->unsignedInteger('receipt_type')->nullable();
                $table->string('trans_date')->nullable();
                $table->string('entry_date')->nullable();
                $table->string('trans_no')->nullable();
                $table->string('invoice_no')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('payment_type')->nullable();
                $table->string('description')->nullable();
                $table->decimal('deposit_amount', 12, 2)->nullable();
                $table->decimal('withdraw_amount', 12, 2)->nullable();
                $table->decimal('balance_amount', 12, 2)->nullable();
                $table->decimal('partial_paid_amount', 12, 2)->nullable();
                $table->decimal('eftpos_surcharge_amount', 12, 2)->nullable();
                $table->unsignedBigInteger('uploaded_doc_id')->nullable();
                $table->unsignedBigInteger('pdf_document_id')->nullable();
                $table->string('save_type')->nullable();
                $table->unsignedInteger('validate_receipt')->nullable();
                $table->unsignedInteger('void_invoice')->nullable();
                $table->unsignedInteger('invoice_status')->nullable();
                $table->unsignedInteger('hubdoc_sent')->nullable();
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('account_all_invoice_receipts')) {
            $schema->create('account_all_invoice_receipts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('receipt_id')->nullable();
                $table->unsignedInteger('receipt_type')->nullable();
                $table->string('invoice_no')->nullable();
                $table->string('trans_no')->nullable();
                $table->unsignedInteger('invoice_status')->nullable();
                $table->timestamps();
            });
        }
    }
}
