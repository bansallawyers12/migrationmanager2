<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration finds all tables in the database that don't have a primary key column.
     * Results are logged to Laravel log file and can be viewed in storage/logs/laravel.log
     */
    public function up(): void
    {
        try {
            // Get the current database name
            $databaseName = DB::connection()->getDatabaseName();
            
            // Query to find all tables without primary key constraints
            // This query checks the information_schema to find tables that don't have any primary key
            $tablesWithoutPK = DB::select("
                SELECT 
                    t.table_name,
                    t.table_schema
                FROM 
                    information_schema.tables t
                LEFT JOIN (
                    SELECT 
                        tc.table_schema,
                        tc.table_name
                    FROM 
                        information_schema.table_constraints tc
                    WHERE 
                        tc.constraint_type = 'PRIMARY KEY'
                ) pk ON t.table_schema = pk.table_schema 
                    AND t.table_name = pk.table_name
                WHERE 
                    t.table_schema = 'public'
                    AND t.table_type = 'BASE TABLE'
                    AND pk.table_name IS NULL
                ORDER BY 
                    t.table_name
            ");
            
            // Convert to array for easier handling
            $tableList = [];
            foreach ($tablesWithoutPK as $table) {
                $tableList[] = $table->table_name;
            }
            
            // Log the results
            Log::info('=== TABLES WITHOUT PRIMARY KEY ===');
            Log::info('Database: ' . $databaseName);
            Log::info('Total tables without primary key: ' . count($tableList));
            Log::info('Tables: ' . implode(', ', $tableList));
            
            // Also output to console if running via artisan
            if (app()->runningInConsole()) {
                echo "\n=== TABLES WITHOUT PRIMARY KEY ===\n";
                echo "Database: {$databaseName}\n";
                echo "Total tables without primary key: " . count($tableList) . "\n\n";
                echo "Table List:\n";
                foreach ($tableList as $index => $tableName) {
                    echo ($index + 1) . ". {$tableName}\n";
                }
                echo "\n";
            }
            
            // Store results in a temporary table for reference (optional)
            // This allows querying the results later
            if (Schema::hasTable('_tables_without_pk_report')) {
                DB::statement('DROP TABLE IF EXISTS _tables_without_pk_report');
            }
            
            DB::statement('
                CREATE TABLE _tables_without_pk_report (
                    id SERIAL PRIMARY KEY,
                    table_name VARCHAR(255) NOT NULL,
                    checked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ');
            
            if (!empty($tableList)) {
                foreach ($tableList as $tableName) {
                    DB::table('_tables_without_pk_report')->insert([
                        'table_name' => $tableName,
                        'checked_at' => now()
                    ]);
                }
            }
            
            Log::info('Report table created: _tables_without_pk_report');
            Log::info('You can query this table with: SELECT * FROM _tables_without_pk_report');
            
        } catch (\Exception $e) {
            Log::error('Error finding tables without primary key: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if (app()->runningInConsole()) {
                echo "\nERROR: " . $e->getMessage() . "\n";
            }
            
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the temporary report table if it exists
        if (Schema::hasTable('_tables_without_pk_report')) {
            Schema::drop('_tables_without_pk_report');
        }
    }
};
