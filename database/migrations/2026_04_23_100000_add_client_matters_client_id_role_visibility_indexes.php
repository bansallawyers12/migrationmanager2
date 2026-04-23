<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Speed up EXISTS / WHERE patterns that filter by client_id and OR on staff assignment
     * columns (StaffClientVisibility, lead scoping, API association checks). PostgreSQL can
     * bitmap-OR index scans on (client_id, sel_*). Adds only indexes; no behavior change.
     */
    public function up(): void
    {
        if (! Schema::hasTable('client_matters')) {
            return;
        }

        $columns = ['client_id', 'sel_migration_agent', 'sel_person_responsible', 'sel_person_assisting'];
        foreach ($columns as $column) {
            if (! Schema::hasColumn('client_matters', $column)) {
                return;
            }
        }

        Schema::table('client_matters', function (Blueprint $table) {
            $table->index(
                ['client_id', 'sel_migration_agent'],
                'client_matters_cid_sel_ma_idx'
            );
            $table->index(
                ['client_id', 'sel_person_responsible'],
                'client_matters_cid_sel_pr_idx'
            );
            $table->index(
                ['client_id', 'sel_person_assisting'],
                'client_matters_cid_sel_pa_idx'
            );
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('client_matters')) {
            return;
        }

        Schema::table('client_matters', function (Blueprint $table) {
            $table->dropIndex('client_matters_cid_sel_ma_idx');
            $table->dropIndex('client_matters_cid_sel_pr_idx');
            $table->dropIndex('client_matters_cid_sel_pa_idx');
        });
    }
};
