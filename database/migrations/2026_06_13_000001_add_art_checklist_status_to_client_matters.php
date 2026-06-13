<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds art_checklist_status for ART Sheet Checklist tab (active, hold, convert_to_client, discontinue).
     */
    public function up(): void
    {
        if (! Schema::hasColumn('client_matters', 'art_checklist_status')) {
            Schema::table('client_matters', function (Blueprint $table) {
                $table->string('art_checklist_status', 32)->nullable()
                    ->comment('ART Matters sheet checklist status: active, hold, convert_to_client, discontinue');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('client_matters', 'art_checklist_status')) {
            Schema::table('client_matters', function (Blueprint $table) {
                $table->dropColumn('art_checklist_status');
            });
        }
    }
};
