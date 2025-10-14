<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('client_passport_informations', function (Blueprint $table) {
            // Add passport_country column to store the country that issued the passport
            $table->unsignedBigInteger('passport_country')->nullable()->after('passport_number')
                ->comment('Country that issued the passport (reference to countries table)');
            
            // Add index for better query performance
            $table->index('passport_country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_passport_informations', function (Blueprint $table) {
            $table->dropIndex(['passport_country']);
            $table->dropColumn('passport_country');
        });
    }
};
