<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INDEX_NAME = 'email_log_attachments_email_log_id_idx';

    /**
     * Btree on email_log_id for WHERE email_log_id = ? and FK-style joins.
     * Skips if any index on email_log_id already exists (e.g. renamed mail_report_id index).
     */
    public function up(): void
    {
        if (! Schema::hasTable('email_log_attachments') || ! Schema::hasColumn('email_log_attachments', 'email_log_id')) {
            return;
        }

        if (Schema::hasIndex('email_log_attachments', ['email_log_id'])) {
            return;
        }

        Schema::table('email_log_attachments', function (Blueprint $table) {
            $table->index('email_log_id', self::INDEX_NAME);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('email_log_attachments')) {
            return;
        }

        if (! Schema::hasIndex('email_log_attachments', self::INDEX_NAME)) {
            return;
        }

        Schema::table('email_log_attachments', function (Blueprint $table) {
            $table->dropIndex(self::INDEX_NAME);
        });
    }
};
