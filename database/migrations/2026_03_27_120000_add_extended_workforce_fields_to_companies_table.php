<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Extended workforce breakdown (Skills in Demand nomination style), alongside existing counts.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->integer('workforce_foreign_employees')->nullable()->after('workforce_total');
            $table->integer('workforce_aus_professionals')->nullable()->after('workforce_foreign_employees');
            $table->integer('workforce_aus_tradespersons')->nullable()->after('workforce_aus_professionals');
            $table->integer('workforce_aus_recent_graduates_lt12m')->nullable()->after('workforce_aus_tradespersons');
            $table->integer('workforce_aus_apprentices_training')->nullable()->after('workforce_aus_recent_graduates_lt12m');
            $table->integer('workforce_aus_other_trainees_training')->nullable()->after('workforce_aus_apprentices_training');
            $table->integer('workforce_aus_employment_other')->nullable()->after('workforce_aus_other_trainees_training');
            $table->integer('workforce_foreign_482_457')->nullable()->after('workforce_aus_employment_other');
            $table->integer('workforce_foreign_494')->nullable()->after('workforce_foreign_482_457');
            $table->integer('workforce_foreign_other_temp_activity')->nullable()->after('workforce_foreign_494');
            $table->integer('workforce_foreign_overseas_students')->nullable()->after('workforce_foreign_other_temp_activity');
            $table->integer('workforce_foreign_working_holiday')->nullable()->after('workforce_foreign_overseas_students');
            $table->integer('workforce_foreign_other')->nullable()->after('workforce_foreign_working_holiday');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'workforce_foreign_employees',
                'workforce_aus_professionals',
                'workforce_aus_tradespersons',
                'workforce_aus_recent_graduates_lt12m',
                'workforce_aus_apprentices_training',
                'workforce_aus_other_trainees_training',
                'workforce_aus_employment_other',
                'workforce_foreign_482_457',
                'workforce_foreign_494',
                'workforce_foreign_other_temp_activity',
                'workforce_foreign_overseas_students',
                'workforce_foreign_working_holiday',
                'workforce_foreign_other',
            ]);
        });
    }
};
