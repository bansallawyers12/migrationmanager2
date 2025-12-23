<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "Analyzing database tables...\n\n";

// Get all tables from database
try {
    $tables = DB::select("SHOW TABLES");
    $databaseName = DB::connection()->getDatabaseName();
    $tablesKey = "Tables_in_{$databaseName}";
    
    $allTables = [];
    foreach ($tables as $table) {
        $allTables[] = $table->$tablesKey;
    }
    
    echo "Found " . count($allTables) . " tables in database.\n\n";
    
    // Get all model files
    $modelPath = app_path('Models');
    $modelFiles = File::glob($modelPath . '/*.php');
    
    $modelTables = [];
    foreach ($modelFiles as $modelFile) {
        $content = File::get($modelFile);
        
        // Check for explicit table name
        if (preg_match("/protected\s+\\\$table\s*=\s*['\"]([^'\"]+)['\"]/", $content, $matches)) {
            $modelTables[] = $matches[1];
        } else {
            // Use class name to infer table name
            $className = basename($modelFile, '.php');
            $inferredTable = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
            $inferredTable = str_plural($inferredTable);
            $modelTables[] = $inferredTable;
        }
    }
    
    // Get all table references from codebase
    $codebasePath = app_path();
    $tableReferences = [];
    
    // Search for DB::table() calls
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($codebasePath));
    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = File::get($file->getPathname());
            
            // Find DB::table('table_name')
            if (preg_match_all("/DB::table\(['\"]([^'\"]+)['\"]\)/", $content, $matches)) {
                foreach ($matches[1] as $table) {
                    $tableReferences[] = $table;
                }
            }
            
            // Find ->from('table_name')
            if (preg_match_all("/->from\(['\"]([^'\"]+)['\"]\)/", $content, $matches)) {
                foreach ($matches[1] as $table) {
                    $tableReferences[] = $table;
                }
            }
        }
    }
    
    // Also check routes and migrations for table references
    $migrationPath = database_path('migrations');
    $migrationFiles = File::glob($migrationPath . '/*.php');
    
    $migrationTables = [];
    foreach ($migrationFiles as $migrationFile) {
        $content = File::get($migrationFile);
        
        // Find Schema::create('table_name')
        if (preg_match_all("/Schema::create\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
            foreach ($matches[1] as $table) {
                $migrationTables[] = $table;
            }
        }
        
        // Find Schema::table('table_name')
        if (preg_match_all("/Schema::table\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
            foreach ($matches[1] as $table) {
                $migrationTables[] = $table;
            }
        }
        
        // Find Schema::dropIfExists('table_name')
        if (preg_match_all("/Schema::dropIfExists\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
            foreach ($matches[1] as $table) {
                $migrationTables[] = $table;
            }
        }
    }
    
    // Combine all referenced tables
    $allReferencedTables = array_unique(array_merge($modelTables, $tableReferences, $migrationTables));
    
    // Laravel system tables that should be kept
    $systemTables = [
        'migrations',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'sessions',
        'password_reset_tokens',
    ];
    
    // Find unused tables
    $unusedTables = [];
    foreach ($allTables as $table) {
        // Skip system tables
        if (in_array($table, $systemTables)) {
            continue;
        }
        
        // Check if table is referenced anywhere
        $isReferenced = false;
        
        // Check exact match
        if (in_array($table, $allReferencedTables)) {
            $isReferenced = true;
        }
        
        // Check if it's a pivot table (contains underscore with two table names)
        // Pivot tables might not have explicit references
        
        // Check foreign key relationships
        try {
            $foreignKeys = DB::select("
                SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE REFERENCED_TABLE_SCHEMA = ? 
                AND REFERENCED_TABLE_NAME = ?
            ", [$databaseName, $table]);
            
            if (!empty($foreignKeys)) {
                $isReferenced = true;
            }
        } catch (\Exception $e) {
            // Ignore errors
        }
        
        // Check if other tables reference this table via foreign keys
        try {
            $referencedBy = DB::select("
                SELECT TABLE_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = ? 
                AND REFERENCED_TABLE_NAME = ?
            ", [$databaseName, $table]);
            
            if (!empty($referencedBy)) {
                $isReferenced = true;
            }
        } catch (\Exception $e) {
            // Ignore errors
        }
        
        if (!$isReferenced) {
            $unusedTables[] = $table;
        }
    }
    
    echo "=== ANALYSIS RESULTS ===\n\n";
    echo "Total tables in database: " . count($allTables) . "\n";
    echo "Tables referenced in code/models/migrations: " . count($allReferencedTables) . "\n";
    echo "Potentially unused tables: " . count($unusedTables) . "\n\n";
    
    if (!empty($unusedTables)) {
        echo "=== POTENTIALLY UNUSED TABLES ===\n\n";
        foreach ($unusedTables as $table) {
            echo "- $table\n";
        }
    } else {
        echo "No unused tables found.\n";
    }
    
    // Also check for tables that are explicitly dropped in migrations
    echo "\n=== TABLES EXPLICITLY MARKED FOR DELETION IN MIGRATIONS ===\n\n";
    $droppedTables = [];
    foreach ($migrationFiles as $migrationFile) {
        $content = File::get($migrationFile);
        if (preg_match_all("/Schema::dropIfExists\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
            foreach ($matches[1] as $table) {
                if (!in_array($table, $droppedTables)) {
                    $droppedTables[] = $table;
                    echo "- $table (dropped in: " . basename($migrationFile) . ")\n";
                }
            }
        }
    }
    
    if (empty($droppedTables)) {
        echo "None found.\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}




