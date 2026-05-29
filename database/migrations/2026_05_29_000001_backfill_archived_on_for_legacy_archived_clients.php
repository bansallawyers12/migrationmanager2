<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legacy archives only set is_archived; backfill archived_on from last update for display.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('admins', 'archived_on')) {
            return;
        }

        DB::table('admins')
            ->where('is_archived', 1)
            ->whereNull('archived_on')
            ->update(['archived_on' => DB::raw('updated_at')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive backfill; no rollback.
    }
};
