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
     * Adds missing migration agent fields to the admins table
     * (only adds columns that don't already exist)
     */
    public function up(): void
    {
        // Check column existence OUTSIDE the Schema::table closure
        $hasIsMigrationAgent = Schema::hasColumn('admins', 'is_migration_agent');
        $hasMarnNumber = Schema::hasColumn('admins', 'marn_number');
        
        Schema::table('admins', function (Blueprint $table) {
            // Check and add only missing columns
            
            if (!Schema::hasColumn('admins', 'business_address')) {
                $table->text('business_address')->nullable()->after('business_fax');
            }
            
            if (!Schema::hasColumn('admins', 'business_phone')) {
                $table->string('business_phone')->nullable()->after('business_fax');
            }
            
            if (!Schema::hasColumn('admins', 'business_email')) {
                $table->string('business_email')->nullable()->after('business_fax');
            }
            
            if (!Schema::hasColumn('admins', 'tax_number')) {
                $table->string('tax_number')->nullable()->after('business_fax');
            }
        });
        
        // Add indexes separately, outside the Schema::table closure
        // Only create indexes if the columns actually exist
        if ($hasIsMigrationAgent && !$this->indexExists('admins', 'admins_is_migration_agent_index')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->index('is_migration_agent');
            });
        }
        
        if ($hasMarnNumber && !$this->indexExists('admins', 'admins_marn_number_index')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->index('marn_number');
            });
        }
        
        echo "\n✅ Migration completed successfully!\n";
        echo "   - Added missing migration agent fields to admins table\n";
        echo "   - business_address, business_phone, business_email, tax_number\n";
        if ($hasIsMigrationAgent) {
            echo "   - Index created on is_migration_agent\n";
        } else {
            echo "   - Note: is_migration_agent column does not exist, skipping index\n";
        }
        if ($hasMarnNumber) {
            echo "   - Index created on marn_number\n";
        } else {
            echo "   - Note: marn_number column does not exist, skipping index\n";
        }
        echo "\n";
    }

    /**
     * Check if an index exists
     */
    private function indexExists($table, $indexName)
    {
        $indexes = DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $index) {
            if ($index->Key_name === $indexName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Only drop columns that we added
            if (Schema::hasColumn('admins', 'business_address')) {
                $table->dropColumn('business_address');
            }
            if (Schema::hasColumn('admins', 'business_phone')) {
                $table->dropColumn('business_phone');
            }
            if (Schema::hasColumn('admins', 'business_email')) {
                $table->dropColumn('business_email');
            }
            if (Schema::hasColumn('admins', 'tax_number')) {
                $table->dropColumn('tax_number');
            }
        });
        
        echo "\n✅ Rollback completed - new agent fields removed\n\n";
    }
};
