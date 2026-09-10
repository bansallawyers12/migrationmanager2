<?php

namespace App\Services;

use App\Models\AccountClientReceipt;
use App\Models\ActivitiesLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class ApplyInvoiceOfficeDiscount
{
    public const PAYMENT_METHOD = 'Discount';

    public function __construct(private InvoicePaymentSyncService $invoicePaymentSync) {}

    /**
     * Apply a discount to an existing invoice as a Direct Office Receipt only.
     * Does not add an invoice Discount line.
     *
     * @return array{status: bool, message: string, invoice_no?: string, invoice_balance?: float, invoice_status?: int, requestData?: array<int, array<string, mixed>>}
     */
    public function handle(int $clientId, string $invoiceNo, float $amount, ?string $description, ?int $userId): array
    {
        $invoiceNo = trim($invoiceNo);
        $amount = round($amount, 2);

        if ($invoiceNo === '' || $amount <= 0) {
            return [
                'status' => false,
                'message' => 'A valid invoice and discount amount are required.',
            ];
        }

        try {
            return DB::transaction(function () use ($clientId, $invoiceNo, $amount, $description, $userId) {
                $invoice = $this->lockInvoiceRow($clientId, $invoiceNo);

                if ($invoice === null) {
                    return [
                        'status' => false,
                        'message' => 'Invoice not found.',
                    ];
                }

                $rejection = $this->rejectionForInvoice($invoice, $clientId, $invoiceNo, $amount);
                if ($rejection !== null) {
                    return $rejection;
                }

                $date = now()->format('d/m/Y');
                $resolvedDescription = trim((string) $description);
                if ($resolvedDescription === '') {
                    $resolvedDescription = 'Discount for '.$invoiceNo;
                }

                $transNo = $this->nextOfficeReceiptTransNo();
                $receiptGroupId = $this->nextOfficeReceiptGroupId();

                $insertedId = DB::table((new AccountClientReceipt)->getTable())->insertGetId([
                    'user_id' => $userId,
                    'client_id' => $clientId,
                    'client_matter_id' => $invoice->client_matter_id ?? null,
                    'receipt_id' => $receiptGroupId,
                    'receipt_type' => 2,
                    'trans_date' => $date,
                    'entry_date' => $date,
                    'trans_no' => $transNo,
                    'invoice_no' => $invoiceNo,
                    'payment_method' => self::PAYMENT_METHOD,
                    'description' => $resolvedDescription,
                    'deposit_amount' => $amount,
                    'eftpos_surcharge_amount' => null,
                    'uploaded_doc_id' => null,
                    'save_type' => 'final',
                    'validate_receipt' => 0,
                    'void_invoice' => 0,
                    'invoice_status' => 0,
                    'hubdoc_sent' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $synced = $this->invoicePaymentSync->persistPaymentState($clientId, $invoiceNo);
                if ($synced === null) {
                    throw new RuntimeException('Could not update invoice balance after discount.');
                }

                $this->logActivity($clientId, $userId, $transNo, $amount, $invoiceNo);

                return [
                    'status' => true,
                    'message' => 'Discount applied successfully',
                    'invoice_no' => $invoiceNo,
                    'invoice_balance' => $synced['new_balance'],
                    'invoice_status' => $synced['invoice_status'],
                    'requestData' => [[
                        'id' => $insertedId,
                        'trans_date' => $date,
                        'entry_date' => $date,
                        'trans_no' => $transNo,
                        'invoice_no' => $invoiceNo,
                        'payment_method' => self::PAYMENT_METHOD,
                        'description' => $resolvedDescription,
                        'deposit_amount' => $amount,
                    ]],
                ];
            });
        } catch (RuntimeException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * @return array{status: bool, message: string}|null
     */
    private function rejectionForInvoice(object $invoice, int $clientId, string $invoiceNo, float $amount): ?array
    {
        if ((int) ($invoice->void_invoice ?? 0) === 1) {
            return [
                'status' => false,
                'message' => 'Cannot apply a discount to a voided invoice.',
            ];
        }

        $saveType = strtolower(trim((string) ($invoice->save_type ?? '')));
        if ($saveType === 'draft') {
            return [
                'status' => false,
                'message' => 'Finalize the invoice before applying a discount.',
            ];
        }

        $status = (int) ($invoice->invoice_status ?? 0);
        if (! in_array($status, [0, 2], true)) {
            return [
                'status' => false,
                'message' => 'Discount can only be applied to unpaid or partially paid invoices.',
            ];
        }

        $state = $this->invoicePaymentSync->computePaymentState($clientId, $invoiceNo);
        $outstanding = $state['new_balance'] ?? null;
        if ($state === null || $outstanding === null) {
            return [
                'status' => false,
                'message' => 'Invoice not found.',
            ];
        }

        if ($amount > round((float) $outstanding, 2)) {
            return [
                'status' => false,
                'message' => 'Discount cannot exceed the invoice outstanding balance of $'.number_format((float) $outstanding, 2).'.',
            ];
        }

        return null;
    }

    private function lockInvoiceRow(int $clientId, string $invoiceNo): ?object
    {
        $table = (new AccountClientReceipt)->getTable();

        return DB::table($table)
            ->where('client_id', $clientId)
            ->where('receipt_type', 3)
            ->where(function ($query) use ($invoiceNo) {
                $query->where('invoice_no', $invoiceNo)
                    ->orWhere('trans_no', $invoiceNo);
            })
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();
    }

    private function nextOfficeReceiptTransNo(): string
    {
        $table = (new AccountClientReceipt)->getTable();
        $latestTrans = DB::table($table)
            ->where('receipt_type', 2)
            ->orderByDesc('id')
            ->value('trans_no');

        $lastNumber = 0;
        if (is_string($latestTrans) && $latestTrans !== '') {
            $parts = explode('-', $latestTrans);
            $lastNumber = isset($parts[1]) ? (int) $parts[1] : 0;
        }

        return 'REC-'.str_pad((string) ($lastNumber + 1), 3, '0', STR_PAD_LEFT);
    }

    private function nextOfficeReceiptGroupId(): int
    {
        $table = (new AccountClientReceipt)->getTable();
        $existingMax = (int) DB::table($table)
            ->where('receipt_type', 2)
            ->max('receipt_id');

        if (! Schema::hasTable('receipt_sequences')) {
            return $existingMax + 1;
        }

        $counter = DB::table('receipt_sequences')
            ->where('receipt_type', 2)
            ->lockForUpdate()
            ->first();

        $next = max((int) ($counter->last_receipt_id ?? 0), $existingMax) + 1;

        if ($counter) {
            DB::table('receipt_sequences')
                ->where('receipt_type', 2)
                ->update(['last_receipt_id' => $next, 'updated_at' => now()]);
        } else {
            DB::table('receipt_sequences')->insert([
                'receipt_type' => 2,
                'last_receipt_id' => $next,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $next;
    }

    private function logActivity(int $clientId, ?int $userId, string $transNo, float $amount, string $invoiceNo): void
    {
        if ($userId === null || $userId <= 0 || ! Schema::hasTable((new ActivitiesLog)->getTable())) {
            return;
        }

        $log = new ActivitiesLog;
        $log->client_id = $clientId;
        $log->created_by = $userId;
        $log->subject = 'added office receipt. Reference no- '.$transNo;
        $log->description = 'Discount for '.$invoiceNo.', Amount: $'.number_format($amount, 2);
        $log->activity_type = 'financial';
        $log->task_status = 0;
        $log->pin = 0;
        $log->save();
    }
}
