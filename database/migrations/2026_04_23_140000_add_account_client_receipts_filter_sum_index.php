<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Speeds up filters/sums on client ledger rows by
     * (client_id, client_matter_id = matter, receipt_type, invoice_status).
     * On PostgreSQL, balance_amount is attached via INCLUDE for covering SUM(balance_amount).
     * On MySQL/SQLite, balance_amount is a trailing key column (similar covering help).
     */
    private const INDEX_NAME = 'account_client_receipts_filter_sum_idx';

    public function up(): void
    {
        if (! Schema::hasTable('account_client_receipts')) {
            return;
        }

        $required = ['client_id', 'client_matter_id', 'receipt_type', 'invoice_status'];
        foreach ($required as $col) {
            if (! Schema::hasColumn('account_client_receipts', $col)) {
                return;
            }
        }

        if (Schema::hasIndex('account_client_receipts', self::INDEX_NAME)) {
            return;
        }

        $hasBalance = Schema::hasColumn('account_client_receipts', 'balance_amount');
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            if ($hasBalance) {
                DB::statement(
                    'CREATE INDEX ' . self::INDEX_NAME
                    . ' ON account_client_receipts (client_id, client_matter_id, receipt_type, invoice_status) '
                    . 'INCLUDE (balance_amount)'
                );
            } else {
                DB::statement(
                    'CREATE INDEX ' . self::INDEX_NAME
                    . ' ON account_client_receipts (client_id, client_matter_id, receipt_type, invoice_status)'
                );
            }

            return;
        }

        Schema::table('account_client_receipts', function (Blueprint $table) use ($hasBalance) {
            if ($hasBalance) {
                $table->index(
                    [
                        'client_id',
                        'client_matter_id',
                        'receipt_type',
                        'invoice_status',
                        'balance_amount',
                    ],
                    self::INDEX_NAME
                );
            } else {
                $table->index(
                    [
                        'client_id',
                        'client_matter_id',
                        'receipt_type',
                        'invoice_status',
                    ],
                    self::INDEX_NAME
                );
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('account_client_receipts')) {
            return;
        }

        if (! Schema::hasIndex('account_client_receipts', self::INDEX_NAME)) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS ' . self::INDEX_NAME);

            return;
        }

        Schema::table('account_client_receipts', function (Blueprint $table) {
            $table->dropIndex(self::INDEX_NAME);
        });
    }
};
