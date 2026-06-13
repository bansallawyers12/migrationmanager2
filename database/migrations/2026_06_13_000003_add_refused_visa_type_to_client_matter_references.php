<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Refused visa / matter category for ART Matters sheet rows (nullable for all other sheet types).
     */
    public function up(): void
    {
        if (! Schema::hasTable('client_matter_references')) {
            return;
        }

        if (! Schema::hasColumn('client_matter_references', 'refused_visa_type')) {
            Schema::table('client_matter_references', function (Blueprint $table) {
                $table->string('refused_visa_type', 50)->nullable()->index()
                    ->comment('ART Matters: refused visa or company matter type (student, visitor, sbs, etc.)');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('client_matter_references')
            && Schema::hasColumn('client_matter_references', 'refused_visa_type')) {
            Schema::table('client_matter_references', function (Blueprint $table) {
                $table->dropColumn('refused_visa_type');
            });
        }
    }
};
