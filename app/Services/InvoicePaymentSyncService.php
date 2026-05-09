<?php

namespace App\Services;

use App\Models\AccountAllInvoiceReceipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Keeps invoice payment totals, status, and balance_amount consistent:
 * - Invoice total = sum of line withdraw amounts (Discount lines subtract, matching PDF totals).
 * - Paid = final office receipts + non-void fee transfers for the invoice key.
 * - Outstanding balance is stored only on the line with the greatest id (per invoice group)
 *   so SUM(balance_amount) across invoice lines matches true outstanding.
 */
class InvoicePaymentSyncService
{
    public function invoiceLinesExist(int $clientId, string $invoiceKey): bool
    {
        return $this->invoiceLinesBaseQuery($clientId, $invoiceKey)->exists();
    }

    /**
     * Total invoice amount (inc. GST lines) matching AccountAllInvoiceReceipt / PDF line totals.
     */
    public function sumInvoiceLineWithdrawTotal(int $clientId, string $invoiceKey): float
    {
        $sum = $this->invoiceLinesBaseQuery($clientId, $invoiceKey)
            ->sum(DB::raw("CASE WHEN payment_type = 'Discount' THEN -withdraw_amount ELSE withdraw_amount END"));

        return round((float) $sum, 2);
    }

    public function sumTotalPaidForInvoice(int $clientId, string $invoiceKey): float
    {
        $totalPaidOffice = (float) DB::table('account_client_receipts')
            ->where('receipt_type', 2)
            ->where('invoice_no', $invoiceKey)
            ->where('client_id', $clientId)
            ->where(function ($q) {
                $q->where('save_type', 'final')
                    ->orWhereNull('save_type');
            })
            ->sum('deposit_amount');

        $totalPaidFeeTransfer = (float) DB::table('account_client_receipts')
            ->where('receipt_type', 1)
            ->where('client_fund_ledger_type', 'Fee Transfer')
            ->where('invoice_no', $invoiceKey)
            ->where('client_id', $clientId)
            ->where(function ($q) {
                $q->whereNull('void_fee_transfer')
                    ->orWhere('void_fee_transfer', 0);
            })
            ->sum('withdraw_amount');

        return round($totalPaidOffice + $totalPaidFeeTransfer, 2);
    }

    /**
     * Amount still owed if we ignore one office receipt row (e.g. before applying its deposit in overpayment logic).
     */
    public function outstandingExcludingOfficeReceiptId(int $clientId, string $invoiceKey, int $excludeOfficeReceiptId): float
    {
        if (! $this->invoiceLinesExist($clientId, $invoiceKey)) {
            return 0.0;
        }

        $invoiceTotal = $this->sumInvoiceLineWithdrawTotal($clientId, $invoiceKey);

        $officeWithout = (float) DB::table('account_client_receipts')
            ->where('receipt_type', 2)
            ->where('invoice_no', $invoiceKey)
            ->where('client_id', $clientId)
            ->where(function ($q) {
                $q->where('save_type', 'final')
                    ->orWhereNull('save_type');
            })
            ->where('id', '!=', $excludeOfficeReceiptId)
            ->sum('deposit_amount');

        $totalPaidFeeTransfer = (float) DB::table('account_client_receipts')
            ->where('receipt_type', 1)
            ->where('client_fund_ledger_type', 'Fee Transfer')
            ->where('invoice_no', $invoiceKey)
            ->where('client_id', $clientId)
            ->where(function ($q) {
                $q->whereNull('void_fee_transfer')
                    ->orWhere('void_fee_transfer', 0);
            })
            ->sum('withdraw_amount');

        $paid = round($officeWithout + $totalPaidFeeTransfer, 2);
        $raw = round($invoiceTotal - $paid, 2);

        return max(0.0, $raw);
    }

    /**
     * @return array{invoice_total: float, total_paid: float, new_balance: float, invoice_status: int}|null
     */
    public function computePaymentState(int $clientId, string $invoiceKey): ?array
    {
        if (! $this->invoiceLinesExist($clientId, $invoiceKey)) {
            return null;
        }

        $invoiceTotal = $this->sumInvoiceLineWithdrawTotal($clientId, $invoiceKey);
        $totalPaid = $this->sumTotalPaidForInvoice($clientId, $invoiceKey);
        $rawBalance = round($invoiceTotal - $totalPaid, 2);

        if ($rawBalance < 0 && $rawBalance > -0.02) {
            $rawBalance = 0.0;
        }

        $newBalanceDisplay = max(0.0, $rawBalance);

        if ($newBalanceDisplay <= 0) {
            $status = 1;
        } elseif ($totalPaid > 0) {
            $status = 2;
        } else {
            $status = 0;
        }

        return [
            'invoice_total' => $invoiceTotal,
            'total_paid' => $totalPaid,
            'new_balance' => $newBalanceDisplay,
            'invoice_status' => $status,
        ];
    }

    /**
     * Outstanding for a PDF / receipt group identified by receipt_id (all lines share invoice key).
     *
     * @return array{invoice_total: float, total_paid: float, new_balance: float, invoice_status: int}|null
     */
    public function computePaymentStateForReceiptId(int $clientId, int $receiptId): ?array
    {
        $line = DB::table('account_client_receipts')
            ->where('client_id', $clientId)
            ->where('receipt_type', 3)
            ->where('receipt_id', $receiptId)
            ->where(function ($q) {
                $q->whereNull('void_invoice')
                    ->orWhere('void_invoice', 0);
            })
            ->orderByRaw("CASE WHEN LOWER(COALESCE(save_type, '')) = 'draft' THEN 1 ELSE 0 END")
            ->orderBy('id')
            ->first();

        if (! $line) {
            return null;
        }

        $key = ! empty($line->invoice_no) ? (string) $line->invoice_no : (string) $line->trans_no;

        return $this->computePaymentState($clientId, $key);
    }

    /**
     * Pending total for PDFs when some invoices have no matching payment-sync rows (e.g. draft-only lines).
     *
     * @param  int|null  $voidInvoice 1 = voided invoice row (no amount owing)
     */
    public function pendingAmountForReceiptPdf(int $clientId, int $receiptId, float $invoiceTotalFallback, ?int $voidInvoice = null): float
    {
        $state = $this->computePaymentStateForReceiptId($clientId, $receiptId);
        if ($state !== null) {
            return (float) $state['new_balance'];
        }

        if ($voidInvoice === 1) {
            return 0.0;
        }

        return max(0.0, $invoiceTotalFallback);
    }

    /**
     * Persists invoice_status / partial_paid on all lines; balance_amount only on max(id) line.
     *
     * @return array{invoice_total: float, total_paid: float, new_balance: float, invoice_status: int}|null
     */
    public function persistPaymentState(int $clientId, string $invoiceKey): ?array
    {
        $state = $this->computePaymentState($clientId, $invoiceKey);
        if ($state === null) {
            return null;
        }

        $ids = $this->invoiceLinesBaseQuery($clientId, $invoiceKey)->pluck('id');
        if ($ids->isEmpty()) {
            return null;
        }

        $maxId = (int) $ids->max();

        $mirrorInvoiceNo = $this->invoiceLinesBaseQuery($clientId, $invoiceKey)
            ->whereNotNull('invoice_no')
            ->where('invoice_no', '!=', '')
            ->value('invoice_no');

        $mirrorKey = $mirrorInvoiceNo ?? $invoiceKey;

        $now = now();

        DB::transaction(function () use ($ids, $maxId, $state, $now, $clientId, $mirrorKey) {
            DB::table('account_client_receipts')
                ->whereIn('id', $ids)
                ->update([
                    'invoice_status' => $state['invoice_status'],
                    'partial_paid_amount' => $state['total_paid'],
                    'balance_amount' => 0,
                    'updated_at' => $now,
                ]);

            DB::table('account_client_receipts')
                ->where('id', $maxId)
                ->update([
                    'balance_amount' => $state['new_balance'],
                    'updated_at' => $now,
                ]);

            AccountAllInvoiceReceipt::where('receipt_type', 3)
                ->where('client_id', $clientId)
                ->where(function ($q) use ($mirrorKey) {
                    $q->where('invoice_no', $mirrorKey)
                        ->orWhere('trans_no', $mirrorKey);
                })
                ->update([
                    'invoice_status' => $state['invoice_status'],
                    'updated_at' => $now,
                ]);
        });

        // Invalidate cached PDF after transaction commits — pending amount has changed.
        $this->invalidateCachedPdf($clientId, $invoiceKey);

        return $state;
    }

    /**
     * Stripe / portal settlement: mark invoice fully paid in CRM tables (no office receipt row).
     * Scopes by receipt_id + matter so we don't touch unrelated mirror rows.
     */
    public function markFullyPaidFromClientPortal(int $clientId, int $receiptId, int $clientMatterId): bool
    {
        $lines = DB::table('account_client_receipts')
            ->where('client_id', $clientId)
            ->where('receipt_type', 3)
            ->where('receipt_id', $receiptId)
            ->where('client_matter_id', $clientMatterId)
            ->where(function ($q) {
                $q->whereNull('void_invoice')
                    ->orWhere('void_invoice', 0);
            })
            ->orderByRaw("CASE WHEN LOWER(COALESCE(save_type, '')) = 'draft' THEN 1 ELSE 0 END")
            ->orderBy('id')
            ->get(['id', 'invoice_no', 'trans_no']);

        if ($lines->isEmpty()) {
            return false;
        }

        $ids = $lines->pluck('id');
        $maxId = (int) $ids->max();
        $first = $lines->first();
        $invoiceKey = ! empty($first->invoice_no) ? (string) $first->invoice_no : (string) $first->trans_no;
        $invoiceTotal = $this->sumInvoiceLineWithdrawTotal($clientId, $invoiceKey);
        $now = now();

        DB::transaction(function () use ($ids, $maxId, $invoiceTotal, $now, $clientId, $receiptId) {
            DB::table('account_client_receipts')
                ->whereIn('id', $ids)
                ->update([
                    'invoice_status' => 1,
                    'partial_paid_amount' => $invoiceTotal,
                    'balance_amount' => 0,
                    'updated_at' => $now,
                ]);

            DB::table('account_client_receipts')
                ->where('id', $maxId)
                ->update([
                    'balance_amount' => 0,
                    'updated_at' => $now,
                ]);

            AccountAllInvoiceReceipt::where('receipt_type', 3)
                ->where('client_id', $clientId)
                ->where('receipt_id', $receiptId)
                ->update([
                    'invoice_status' => 1,
                    'updated_at' => $now,
                ]);
        });

        // Invalidate cached PDF — invoice is now fully paid.
        $this->invalidateCachedPdf($clientId, $invoiceKey);

        return true;
    }

    /**
     * Delete the cached invoice PDF from S3 + documents table and clear pdf_document_id on
     * all matching invoice lines. Called whenever a payment changes what "Total Pending" should be.
     * Failures are logged but never throw — PDF invalidation is best-effort and must not
     * roll back the payment state that was already committed.
     */
    private function invalidateCachedPdf(int $clientId, string $invoiceKey): void
    {
        try {
            $rows = $this->invoiceLinesBaseQuery($clientId, $invoiceKey)
                ->whereNotNull('pdf_document_id')
                ->select(['id', 'pdf_document_id', 'receipt_id'])
                ->get();

            if ($rows->isEmpty()) {
                return;
            }

            $clientUniqueId = DB::table('admins')
                ->where('id', $clientId)
                ->value('client_id');

            $seenDocIds = [];

            foreach ($rows as $row) {
                $docId = $row->pdf_document_id;
                if ($docId === null || in_array($docId, $seenDocIds, true)) {
                    continue;
                }
                $seenDocIds[] = $docId;

                $doc = DB::table('documents')->where('id', $docId)->first();
                if ($doc && ! empty($doc->myfile_key) && $clientUniqueId) {
                    $docType = $doc->doc_type ?? 'invoices';
                    $s3Path  = $clientUniqueId . '/' . $docType . '/' . $doc->myfile_key;
                    try {
                        Storage::disk('s3')->delete($s3Path);
                    } catch (\Exception $e) {
                        Log::warning('InvoicePaymentSyncService: failed to delete PDF from S3', [
                            'path'  => $s3Path,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                DB::table('documents')->where('id', $docId)->delete();
            }

            // Clear the reference on every invoice line for this key
            $this->invoiceLinesBaseQuery($clientId, $invoiceKey)
                ->update(['pdf_document_id' => null, 'updated_at' => now()]);

            Log::info('InvoicePaymentSyncService: invoice PDF cache invalidated', [
                'client_id'   => $clientId,
                'invoice_key' => $invoiceKey,
                'docs_removed' => count($seenDocIds),
            ]);
        } catch (\Exception $e) {
            Log::error('InvoicePaymentSyncService: unexpected error in invalidateCachedPdf', [
                'client_id'   => $clientId,
                'invoice_key' => $invoiceKey,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    private function invoiceLinesBaseQuery(int $clientId, string $invoiceKey)
    {
        return DB::table('account_client_receipts')
            ->where('client_id', $clientId)
            ->where('receipt_type', 3)
            ->where(function ($q) use ($invoiceKey) {
                $q->where('invoice_no', $invoiceKey)
                    ->orWhere('trans_no', $invoiceKey);
            })
            ->where(function ($q) {
                $q->whereNull('void_invoice')
                    ->orWhere('void_invoice', 0);
            });
    }
}
