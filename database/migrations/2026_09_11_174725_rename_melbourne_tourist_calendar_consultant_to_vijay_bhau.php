<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename the Melbourne tourist calendar owner for CRM display.
     * calendar_type stays `tourist` so assignment and existing bookings are unchanged.
     */
    public function up(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        DB::table('appointment_consultants')
            ->where('calendar_type', 'tourist')
            ->where('location', 'melbourne')
            ->update(['name' => 'Vijay bhau']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        DB::table('appointment_consultants')
            ->where('calendar_type', 'tourist')
            ->where('location', 'melbourne')
            ->where('name', 'Vijay bhau')
            ->update(['name' => 'Tourist Visa Team']);
    }
};
