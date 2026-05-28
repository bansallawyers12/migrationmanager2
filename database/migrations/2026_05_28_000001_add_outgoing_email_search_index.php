<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            // Speeds up the outgoing base scope + date-sorted list/dashboard queries.
            // Composite: (mail_type, conversion_type, created_at DESC) covers the WHERE + ORDER BY.
            if (!$this->indexExists('email_logs', 'email_logs_outgoing_date_idx')) {
                $table->index(['mail_type', 'conversion_type', 'created_at'], 'email_logs_outgoing_date_idx');
            }

            // user_id + created_at: speeds up "sent by staff" filter and top-senders aggregation.
            if (!$this->indexExists('email_logs', 'email_logs_user_created_idx')) {
                $table->index(['user_id', 'created_at'], 'email_logs_user_created_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropIndex('email_logs_outgoing_date_idx');
            $table->dropIndex('email_logs_user_created_idx');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            $result = \Illuminate\Support\Facades\DB::select(
                "SELECT 1 FROM pg_indexes WHERE tablename = ? AND indexname = ? LIMIT 1",
                [$table, $indexName]
            );
            return !empty($result);
        }

        // MySQL/MariaDB
        $result = \Illuminate\Support\Facades\DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );
        return !empty($result);
    }
};
