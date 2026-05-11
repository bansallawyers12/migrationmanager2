<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add 'adelaide_education' calendar_type and Adelaide Education consultant (Adelaide office; education + tourist NOE).
     */
    public function up(): void
    {
        if (! Schema::hasTable('appointment_consultants')) {
            return;
        }

        if (DB::getDriverName() === 'pgsql' && Schema::hasColumn('appointment_consultants', 'calendar_type')) {
            DB::statement('ALTER TABLE appointment_consultants ALTER COLUMN calendar_type TYPE VARCHAR(64)');
        }

        $allowed = "'paid', 'jrp', 'education', 'tourist', 'adelaide', 'adelaide_education', 'ajay', 'kunal', 'arun'";

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

            DB::statement("ALTER TABLE appointment_consultants ADD CONSTRAINT appointment_consultants_calendar_type_check CHECK (calendar_type IN ({$allowed}))");
        } else {
            if (Schema::hasColumn('appointment_consultants', 'calendar_type')) {
                DB::statement("ALTER TABLE appointment_consultants MODIFY COLUMN calendar_type ENUM('paid', 'jrp', 'education', 'tourist', 'adelaide', 'adelaide_education', 'ajay', 'kunal', 'arun')");
            }
        }

        if (DB::table('appointment_consultants')->where('calendar_type', 'adelaide_education')->exists()) {
            return;
        }

        $nextId = (int) DB::table('appointment_consultants')->max('id') + 1;

        $row = [
            'id' => $nextId,
            'name' => 'Adelaide Education',
            'email' => null,
            'calendar_type' => 'adelaide_education',
            'location' => 'adelaide',
            'specializations' => json_encode([4, 5]),
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

        DB::table('appointment_consultants')->where('calendar_type', 'adelaide_education')->delete();

        $allowedPrev = "'paid', 'jrp', 'education', 'tourist', 'adelaide', 'ajay', 'kunal', 'arun'";

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

            DB::statement("ALTER TABLE appointment_consultants ADD CONSTRAINT appointment_consultants_calendar_type_check CHECK (calendar_type IN ({$allowedPrev}))");
        } else {
            if (Schema::hasColumn('appointment_consultants', 'calendar_type')) {
                DB::statement("ALTER TABLE appointment_consultants MODIFY COLUMN calendar_type ENUM('paid', 'jrp', 'education', 'tourist', 'adelaide', 'ajay', 'kunal', 'arun')");
            }
        }
    }
};
