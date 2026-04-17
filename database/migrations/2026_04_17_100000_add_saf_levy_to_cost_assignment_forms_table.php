<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cost_assignment_forms', function (Blueprint $table) {
            if (! Schema::hasColumn('cost_assignment_forms', 'saf_levy')) {
                $table->string('saf_levy', 255)->nullable()->after('Dept_Sponsorship_Application_Charge');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cost_assignment_forms', function (Blueprint $table) {
            if (Schema::hasColumn('cost_assignment_forms', 'saf_levy')) {
                $table->dropColumn('saf_levy');
            }
        });
    }
};
