<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Show every active consultant in CRM list filters and calendar transfer dropdowns.
     * (Previously the employer-sponsored / paid row used show_in_filter = false.)
     */
    public function up(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        if (! Schema::hasColumn('appointment_consultants', 'show_in_filter')) {
            return;
        }

        DB::table('appointment_consultants')
            ->where('show_in_filter', false)
            ->update(['show_in_filter' => true]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        if (! Schema::hasColumn('appointment_consultants', 'show_in_filter')) {
            return;
        }

        DB::table('appointment_consultants')
            ->where('calendar_type', 'paid')
            ->where('name', 'like', '%Arun Kumar%')
            ->update(['show_in_filter' => false]);
    }
};
