<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dashboard client matter list (DashboardService::getClientMatters):
     * - Admin: matter_status, workflow_stage_id, ORDER BY updated_at — covered by client_matters_dashboard_list_idx.
     * - MA / PR / PA: OR on sel_* assignment columns plus same filters/sort — each branch benefits from a leading sel_* composite.
     * - Client name filter: whereHas('client') — speeds lookups by client_id on client_matters.
     *
     * Adding indexes only; no application logic changes. Slightly more work on INSERT/UPDATE of indexed columns.
     */
    public function up(): void
    {
        if (! Schema::hasTable('client_matters')) {
            return;
        }

        $required = [
            'sel_migration_agent',
            'sel_person_responsible',
            'sel_person_assisting',
            'matter_status',
            'workflow_stage_id',
            'updated_at',
            'client_id',
        ];

        foreach ($required as $column) {
            if (! Schema::hasColumn('client_matters', $column)) {
                return;
            }
        }

        Schema::table('client_matters', function (Blueprint $table) {
            $table->index(
                ['sel_migration_agent', 'matter_status', 'workflow_stage_id', 'updated_at'],
                'client_matters_dash_ma_scope_idx'
            );
            $table->index(
                ['sel_person_responsible', 'matter_status', 'workflow_stage_id', 'updated_at'],
                'client_matters_dash_pr_scope_idx'
            );
            $table->index(
                ['sel_person_assisting', 'matter_status', 'workflow_stage_id', 'updated_at'],
                'client_matters_dash_pa_scope_idx'
            );
            $table->index('client_id', 'client_matters_client_id_list_idx');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('client_matters')) {
            return;
        }

        Schema::table('client_matters', function (Blueprint $table) {
            $table->dropIndex('client_matters_dash_ma_scope_idx');
            $table->dropIndex('client_matters_dash_pr_scope_idx');
            $table->dropIndex('client_matters_dash_pa_scope_idx');
            $table->dropIndex('client_matters_client_id_list_idx');
        });
    }
};
