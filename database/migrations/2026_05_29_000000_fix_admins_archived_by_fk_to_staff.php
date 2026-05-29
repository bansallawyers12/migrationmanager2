<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * CRM auth uses the 'admin' guard with the 'staff' provider, so Auth::id()
     * returns staff.id. admins.archived_by originally referenced admins.id, which
     * caused client archive to fail with a foreign key violation.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('admins', 'archived_by')) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE admins DROP CONSTRAINT IF EXISTS admins_archived_by_foreign');
        } else {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropForeign(['archived_by']);
            });
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('
                UPDATE admins
                SET archived_by = NULL
                WHERE archived_by IS NOT NULL
                AND NOT EXISTS (SELECT 1 FROM staff s WHERE s.id = admins.archived_by)
            ');
        } else {
            DB::statement('
                UPDATE admins
                SET archived_by = NULL
                WHERE archived_by IS NOT NULL
                AND archived_by NOT IN (SELECT id FROM staff)
            ');
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->foreign('archived_by')
                ->references('id')
                ->on('staff')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('admins', 'archived_by')) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE admins DROP CONSTRAINT IF EXISTS admins_archived_by_foreign');
        } else {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropForeign(['archived_by']);
            });
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->foreign('archived_by')
                ->references('id')
                ->on('admins')
                ->onDelete('set null');
        });
    }
};
