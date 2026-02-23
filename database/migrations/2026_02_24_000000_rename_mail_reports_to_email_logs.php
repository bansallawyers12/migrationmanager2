<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Renames mail_reports to email_logs and related tables/columns.
     */
    public function up(): void
    {
        // 1. Rename mail_reports -> email_logs
        if (Schema::hasTable('mail_reports') && !Schema::hasTable('email_logs')) {
            Schema::rename('mail_reports', 'email_logs');
        }

        // 2. Rename mail_report_attachments -> email_log_attachments, update column
        if (Schema::hasTable('mail_report_attachments')) {
            Schema::table('mail_report_attachments', function (Blueprint $table) {
                $table->renameColumn('mail_report_id', 'email_log_id');
            });
            Schema::rename('mail_report_attachments', 'email_log_attachments');
        }

        // 3. Rename email_label_mail_report pivot -> email_label_email_log, update column
        if (Schema::hasTable('email_label_mail_report')) {
            Schema::table('email_label_mail_report', function (Blueprint $table) {
                $table->dropUnique('mail_report_label_unique');
                $table->renameColumn('mail_report_id', 'email_log_id');
            });
            Schema::rename('email_label_mail_report', 'email_label_email_log');
            Schema::table('email_label_email_log', function (Blueprint $table) {
                $table->unique(['email_log_id', 'email_label_id'], 'email_log_label_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 3. Revert email_label_email_log -> email_label_mail_report
        if (Schema::hasTable('email_label_email_log')) {
            Schema::table('email_label_email_log', function (Blueprint $table) {
                $table->dropUnique('email_log_label_unique');
                $table->renameColumn('email_log_id', 'mail_report_id');
            });
            Schema::rename('email_label_email_log', 'email_label_mail_report');
            Schema::table('email_label_mail_report', function (Blueprint $table) {
                $table->unique(['mail_report_id', 'email_label_id'], 'mail_report_label_unique');
            });
        }

        // 2. Revert email_log_attachments -> mail_report_attachments
        if (Schema::hasTable('email_log_attachments')) {
            Schema::rename('email_log_attachments', 'mail_report_attachments');
            Schema::table('mail_report_attachments', function (Blueprint $table) {
                $table->renameColumn('email_log_id', 'mail_report_id');
            });
        }

        // 1. Revert email_logs -> mail_reports
        if (Schema::hasTable('email_logs') && !Schema::hasTable('mail_reports')) {
            Schema::rename('email_logs', 'mail_reports');
        }
    }
};
