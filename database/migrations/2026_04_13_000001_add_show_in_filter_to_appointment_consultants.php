<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow hiding a consultant from CRM filter/transfer dropdowns without disabling
     * sync (assignConsultant still needs an active row per calendar_type).
     */
    public function up(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        if (! Schema::hasColumn('appointment_consultants', 'show_in_filter')) {
            Schema::table('appointment_consultants', function (Blueprint $table) {
                $table->boolean('show_in_filter')->default(true);
            });
        }

        DB::table('appointment_consultants')
            ->where('calendar_type', 'paid')
            ->where('name', 'like', '%Arun Kumar%')
            ->update(['show_in_filter' => false]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        if (Schema::hasColumn('appointment_consultants', 'show_in_filter')) {
            Schema::table('appointment_consultants', function (Blueprint $table) {
                $table->dropColumn('show_in_filter');
            });
        }
    }
};
