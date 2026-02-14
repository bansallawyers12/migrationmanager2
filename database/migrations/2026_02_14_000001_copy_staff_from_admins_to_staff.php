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
     * Copies staff (role != 7) from admins to staff table, preserving IDs.
     * No mapping table - FK columns keep same values, will reference staff.id instead of admins.id.
     */
    public function up(): void
    {
        if (!Schema::hasTable('staff')) {
            return;
        }

        if (DB::table('staff')->exists()) {
            return;
        }

        DB::transaction(function () {
            // Drop archived_by FK temporarily (self-referential; inserts may be in any order)
            Schema::table('staff', function (Blueprint $table) {
                $table->dropForeign(['archived_by']);
            });

            $staffColumns = [
                'id', 'first_name', 'last_name', 'email', 'password',
                'country_code', 'phone', 'telephone',
                'profile_img', 'status', 'verified',
                'role', 'position', 'team', 'permission', 'office_id',
                'show_dashboard_per', 'time_zone',
                'is_migration_agent', 'marn_number', 'legal_practitioner_number',
                'company_name', 'company_website',
                'business_address', 'business_phone', 'business_mobile',
                'business_email', 'tax_number', 'ABN_number',
                'is_archived', 'archived_by', 'archived_on',
                'remember_token', 'created_at', 'updated_at',
            ];

            $staff = DB::table('admins')
                ->where('role', '!=', 7)
                ->when(Schema::hasColumn('admins', 'is_deleted'), fn ($q) => $q->whereNull('is_deleted'))
                ->orderBy('id')
                ->get($staffColumns);

            if ($staff->isEmpty()) {
                $this->restoreArchivedByFk();
                return;
            }

            // Valid FK references - null out orphaned values to avoid constraint violations
            $validBranchIds = Schema::hasTable('branches')
                ? DB::table('branches')->pluck('id')->flip()->all()
                : [];
            $validRoleIds = Schema::hasTable('user_roles')
                ? DB::table('user_roles')->pluck('id')->flip()->all()
                : [];
            $validArchivedByIds = $staff->pluck('id')->flip()->all(); // only copied staff exist; archived_by references staff.id

            foreach ($staff->chunk(50) as $chunk) {
                foreach ($chunk as $row) {
                    $insert = (array) $row;
                    if (isset($insert['office_id']) && $insert['office_id'] !== null && !isset($validBranchIds[$insert['office_id']])) {
                        $insert['office_id'] = null;
                    }
                    if (isset($insert['role']) && $insert['role'] !== null && !isset($validRoleIds[$insert['role']])) {
                        $insert['role'] = null;
                    }
                    if (isset($insert['archived_by']) && $insert['archived_by'] !== null && !isset($validArchivedByIds[$insert['archived_by']])) {
                        $insert['archived_by'] = null;
                    }
                    DB::table('staff')->insert($insert);
                }
            }

            $this->restoreArchivedByFk();
            $this->updateSequence();
        });
    }

    protected function restoreArchivedByFk(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->foreign('archived_by')->references('id')->on('staff')->onDelete('set null');
        });
    }

    protected function updateSequence(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        $maxId = DB::table('staff')->max('id') ?? 0;

        if ($driver === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('staff', 'id'), ?)", [$maxId]);
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE staff AUTO_INCREMENT = ?", [$maxId + 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('staff')->truncate();
    }
};
