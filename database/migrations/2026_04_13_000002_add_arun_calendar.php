<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add 'arun' calendar_type and "Arun Calendar" consultant.
     *
     * Assign-only: no CRM calendar tab or route (see BookingAppointmentsController::$validTypes).
     * show_in_filter stays true so staff can assign appointments via modal dropdowns.
     */
    public function up(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            $constraints = DB::select("
                SELECT conname as constraint_name
                FROM pg_constraint
                WHERE conrelid = 'appointment_consultants'::regclass
                AND contype = 'c'
                AND pg_get_constraintdef(oid) LIKE '%calendar_type%'
            ");

            foreach ($constraints as $constraint) {
                DB::statement("ALTER TABLE appointment_consultants DROP CONSTRAINT IF EXISTS {$constraint->constraint_name}");
            }

            DB::statement("ALTER TABLE appointment_consultants ADD CONSTRAINT appointment_consultants_calendar_type_check CHECK (calendar_type IN ('paid', 'jrp', 'education', 'tourist', 'adelaide', 'ajay', 'kunal', 'arun'))");
        } else {
            if (Schema::hasColumn('appointment_consultants', 'calendar_type')) {
                DB::statement("ALTER TABLE appointment_consultants MODIFY COLUMN calendar_type ENUM('paid', 'jrp', 'education', 'tourist', 'adelaide', 'ajay', 'kunal', 'arun')");
            }
        }

        if (DB::table('appointment_consultants')->where('calendar_type', 'arun')->exists()) {
            return;
        }

        $nextId = (int) DB::table('appointment_consultants')->max('id') + 1;

        $row = [
            'id' => $nextId,
            'name' => 'Arun Calendar',
            'email' => null,
            'calendar_type' => 'arun',
            'location' => 'melbourne',
            'specializations' => json_encode([]),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('appointment_consultants', 'show_in_filter')) {
            $row['show_in_filter'] = true;
        }

        DB::table('appointment_consultants')->insert($row);

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('appointment_consultants', 'id'), (SELECT MAX(id) FROM appointment_consultants))");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        DB::table('appointment_consultants')->where('calendar_type', 'arun')->delete();

        if (DB::getDriverName() === 'pgsql') {
            $constraints = DB::select("
                SELECT conname as constraint_name
                FROM pg_constraint
                WHERE conrelid = 'appointment_consultants'::regclass
                AND contype = 'c'
                AND pg_get_constraintdef(oid) LIKE '%calendar_type%'
            ");

            foreach ($constraints as $constraint) {
                DB::statement("ALTER TABLE appointment_consultants DROP CONSTRAINT IF EXISTS {$constraint->constraint_name}");
            }

            DB::statement("ALTER TABLE appointment_consultants ADD CONSTRAINT appointment_consultants_calendar_type_check CHECK (calendar_type IN ('paid', 'jrp', 'education', 'tourist', 'adelaide', 'ajay', 'kunal'))");
        } else {
            if (Schema::hasColumn('appointment_consultants', 'calendar_type')) {
                DB::statement("ALTER TABLE appointment_consultants MODIFY COLUMN calendar_type ENUM('paid', 'jrp', 'education', 'tourist', 'adelaide', 'ajay', 'kunal')");
            }
        }
    }
};
