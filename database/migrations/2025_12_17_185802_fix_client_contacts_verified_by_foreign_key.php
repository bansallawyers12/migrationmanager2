<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the old foreign key constraint
        Schema::table('client_contacts', function (Blueprint $table) {
            $table->dropForeign('client_contacts_verified_by_foreign');
        });
        
        // Add the correct foreign key constraint pointing to admins table
        Schema::table('client_contacts', function (Blueprint $table) {
            $table->foreign('verified_by')
                  ->references('id')
                  ->on('admins')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new foreign key
        Schema::table('client_contacts', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
        });
        
        // Restore the old foreign key (to admins_bkk_24oct2025)
        Schema::table('client_contacts', function (Blueprint $table) {
            $table->foreign('verified_by')
                  ->references('id')
                  ->on('admins_bkk_24oct2025')
                  ->onDelete('set null');
        });
    }
};
