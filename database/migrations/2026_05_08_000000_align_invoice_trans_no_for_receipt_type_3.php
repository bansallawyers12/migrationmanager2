<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * For invoice rows (receipt_type = 3), align invoice_no with trans_no when missing or inconsistent.
     */
    public function up(): void
    {
        foreach (['account_client_receipts', 'account_all_invoice_receipts'] as $table) {
            DB::table($table)
                ->where('receipt_type', 3)
                ->whereNotNull('trans_no')
                ->where('trans_no', 'like', 'INV-%')
                ->where(function ($q) {
                    $q->whereNull('invoice_no')
                        ->orWhere('invoice_no', '')
                        ->orWhereColumn('invoice_no', '!=', 'trans_no');
                })
                ->update(['invoice_no' => DB::raw('trans_no')]);
        }
    }

    public function down(): void
    {
        // Intentionally empty: cannot safely restore prior divergent values.
    }
};
