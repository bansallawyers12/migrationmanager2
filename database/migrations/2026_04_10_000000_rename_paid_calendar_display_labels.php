<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename user-facing labels for calendar_type "paid" (internal key unchanged).
     */
    public function up(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        $from = 'Pr_complex matters';
        $to = 'Employer sponsored calendar';

        DB::table('appointment_consultants')
            ->where('calendar_type', 'paid')
            ->where('name', 'like', '%'.$from.'%')
            ->update([
                'name' => DB::raw(
                    "REPLACE(name, '".str_replace("'", "''", $from)."', '".str_replace("'", "''", $to)."')"
                ),
            ]);
    }

    /**
     * Restore previous display label text where it was updated by this migration.
     */
    public function down(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        $from = 'Employer sponsored calendar';
        $to = 'Pr_complex matters';

        DB::table('appointment_consultants')
            ->where('calendar_type', 'paid')
            ->where('name', 'like', '%'.$from.'%')
            ->update([
                'name' => DB::raw(
                    "REPLACE(name, '".str_replace("'", "''", $from)."', '".str_replace("'", "''", $to)."')"
                ),
            ]);
    }
};
