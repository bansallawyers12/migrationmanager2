<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates a mapping table to track which admin IDs were migrated to which client IDs.
     * This is crucial for updating foreign key references in other tables.
     */
    public function up(): void
    {
        Schema::create('admin_to_client_mapping', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('old_admin_id')->comment('Original ID in admins table');
            $table->unsignedBigInteger('new_client_id')->comment('New ID in clients table');
            $table->string('type', 20)->comment('Type: client or lead');
            $table->timestamp('migrated_at')->nullable();
            
            // Indexes for quick lookups
            $table->index('old_admin_id');
            $table->index('new_client_id');
            $table->unique('old_admin_id'); // Each admin ID should only map once
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_to_client_mapping');
    }
};

