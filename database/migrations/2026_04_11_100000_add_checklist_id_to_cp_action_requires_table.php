<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Links an action row to cp_doc_checklists.id (allowed_checklist_id in the mobile API).
     */
    public function up(): void
    {
        Schema::table('cp_action_requires', function (Blueprint $table) {
            if (!Schema::hasColumn('cp_action_requires', 'checklist_id')) {
                $table->unsignedBigInteger('checklist_id')->nullable();
                $table->index(['client_id', 'checklist_id'], 'idx_cp_action_requires_client_checklist_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cp_action_requires', function (Blueprint $table) {
            if (Schema::hasColumn('cp_action_requires', 'checklist_id')) {
                $table->dropIndex('idx_cp_action_requires_client_checklist_id');
                $table->dropColumn('checklist_id');
            }
        });
    }
};
