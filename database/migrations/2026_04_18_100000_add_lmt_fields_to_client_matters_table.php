<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Matter-scoped Labour Market Testing (company clients only in UI).
     * Migrates existing company-level LMT onto all matters for that client.
     */
    public function up(): void
    {
        if (! Schema::hasTable('client_matters')) {
            return;
        }

        Schema::table('client_matters', function (Blueprint $table) {
            if (! Schema::hasColumn('client_matters', 'lmt_required')) {
                $table->boolean('lmt_required')->nullable();
            }
            if (! Schema::hasColumn('client_matters', 'lmt_start_date')) {
                $table->date('lmt_start_date')->nullable();
            }
            if (! Schema::hasColumn('client_matters', 'lmt_end_date')) {
                $table->date('lmt_end_date')->nullable();
            }
            if (! Schema::hasColumn('client_matters', 'lmt_notes')) {
                $table->text('lmt_notes')->nullable();
            }
        });

        if (Schema::hasTable('companies')
            && Schema::hasColumn('companies', 'lmt_required')
            && Schema::hasColumn('client_matters', 'lmt_required')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement('
                    UPDATE client_matters cm
                    INNER JOIN companies c ON c.admin_id = cm.client_id
                    SET
                        cm.lmt_required = c.lmt_required,
                        cm.lmt_start_date = c.lmt_start_date,
                        cm.lmt_end_date = c.lmt_end_date,
                        cm.lmt_notes = c.lmt_notes
                    WHERE
                        c.lmt_required IS NOT NULL
                        OR c.lmt_start_date IS NOT NULL
                        OR c.lmt_end_date IS NOT NULL
                        OR (c.lmt_notes IS NOT NULL AND TRIM(c.lmt_notes) <> \'\')
                ');
            } else {
                // SQLite / others: row-by-row backfill
                $companies = DB::table('companies')
                    ->where(function ($q) {
                        $q->whereNotNull('lmt_required')
                            ->orWhereNotNull('lmt_start_date')
                            ->orWhereNotNull('lmt_end_date')
                            ->orWhereNotNull('lmt_notes');
                    })
                    ->get(['admin_id', 'lmt_required', 'lmt_start_date', 'lmt_end_date', 'lmt_notes']);

                foreach ($companies as $co) {
                    DB::table('client_matters')
                        ->where('client_id', $co->admin_id)
                        ->update([
                            'lmt_required' => $co->lmt_required,
                            'lmt_start_date' => $co->lmt_start_date,
                            'lmt_end_date' => $co->lmt_end_date,
                            'lmt_notes' => $co->lmt_notes,
                        ]);
                }
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('client_matters')) {
            return;
        }

        Schema::table('client_matters', function (Blueprint $table) {
            $cols = [];
            foreach (['lmt_required', 'lmt_start_date', 'lmt_end_date', 'lmt_notes'] as $col) {
                if (Schema::hasColumn('client_matters', $col)) {
                    $cols[] = $col;
                }
            }
            if ($cols !== []) {
                $table->dropColumn($cols);
            }
        });
    }
};
