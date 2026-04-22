<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Speeds up "latest activity per client" lookups (e.g. dashboard cases).
     */
    public function up(): void
    {
        Schema::table('activities_logs', function (Blueprint $table) {
            $table->index(['client_id', 'created_at'], 'activities_logs_client_id_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('activities_logs', function (Blueprint $table) {
            $table->dropIndex('activities_logs_client_id_created_at_index');
        });
    }
};
