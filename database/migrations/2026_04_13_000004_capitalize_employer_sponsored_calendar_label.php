<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Align stored consultant names with "Employer Sponsored Calendar" capitalization.
     */
    public function up(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        DB::table('appointment_consultants')
            ->where('calendar_type', 'paid')
            ->where('name', 'like', '%Employer sponsored calendar%')
            ->update([
                'name' => DB::raw(
                    "REPLACE(name, 'Employer sponsored calendar', 'Employer Sponsored Calendar')"
                ),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        DB::table('appointment_consultants')
            ->where('calendar_type', 'paid')
            ->where('name', 'like', '%Employer Sponsored Calendar%')
            ->update([
                'name' => DB::raw(
                    "REPLACE(name, 'Employer Sponsored Calendar', 'Employer sponsored calendar')"
                ),
            ]);
    }
};
