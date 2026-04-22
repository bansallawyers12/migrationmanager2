<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Supports dashboard client matter list + batched unread email counts.
     */
    public function up(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->index(
                ['client_matter_id', 'client_id', 'conversion_type'],
                'email_logs_cm_client_conversion_idx'
            );
        });

        Schema::table('client_matters', function (Blueprint $table) {
            $table->index(
                ['matter_status', 'workflow_stage_id', 'updated_at'],
                'client_matters_dashboard_list_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropIndex('email_logs_cm_client_conversion_idx');
        });

        Schema::table('client_matters', function (Blueprint $table) {
            $table->dropIndex('client_matters_dashboard_list_idx');
        });
    }
};
