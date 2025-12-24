<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drops unused and legacy tables that are no longer needed:
     * - lead_followups: Unused followup tracking
     * - nature_of_enquiry: Old appointment system (replaced)
     * - service_fee_options: Legacy fee options
     * - service_fee_option_types: Legacy fee option types
     * - sub_categories: Unused categorization
     * - test_scores: Unused test score tracking
     * - tasks: Unused task management
     * - users: Legacy user table (replaced by admins)
     */
    public function up(): void
    {
        // Drop tables in reverse dependency order to avoid foreign key constraint issues
        // Start with tables that might have foreign keys pointing to other tables
        
        $tablesToDrop = [
            'lead_followups',              // Might reference leads
            'service_fee_options',         // Might reference service_fee_option_types
            'service_fee_option_types',    // Parent table
            'sub_categories',              // Might reference categories
            'test_scores',                 // Might reference other tables
            'tasks',                       // Might reference other tables
            'nature_of_enquiry',           // Old appointment system
            'users',                       // Legacy user table (might be referenced, but should be safe if unused)
        ];
        
        foreach ($tablesToDrop as $table) {
            if (Schema::hasTable($table)) {
                // Drop foreign key constraints first if they exist
                try {
                    $constraints = DB::select("
                        SELECT constraint_name 
                        FROM information_schema.table_constraints 
                        WHERE table_name = ? 
                        AND constraint_type = 'FOREIGN KEY'
                        AND table_schema = 'public'
                    ", [$table]);
                    
                    foreach ($constraints as $constraint) {
                        DB::statement("ALTER TABLE {$table} DROP CONSTRAINT IF EXISTS {$constraint->constraint_name} CASCADE");
                    }
                } catch (\Exception $e) {
                    // Ignore errors - constraints might not exist
                }
                
                Schema::dropIfExists($table);
                echo "Dropped table: {$table}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     * 
     * WARNING: This will NOT recreate the tables as they are unused/legacy.
     * The table structures are not preserved.
     */
    public function down(): void
    {
        // Cannot reverse - these are unused/legacy tables
        throw new \Exception('Cannot reverse this migration. Unused/legacy tables have been permanently removed.');
    }
};


