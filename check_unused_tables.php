<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "=== COMPREHENSIVE TABLE USAGE ANALYSIS ===\n\n";

try {
    // Get all tables from database
    $tables = DB::select("SHOW TABLES");
    $databaseName = DB::connection()->getDatabaseName();
    $tablesKey = "Tables_in_{$databaseName}";
    
    $allTables = [];
    foreach ($tables as $table) {
        $allTables[] = $table->$tablesKey;
    }
    
    echo "Total tables in database: " . count($allTables) . "\n\n";
    
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
    
    // Get all model files and their table names
    $modelPath = app_path('Models');
    $modelFiles = File::glob($modelPath . '/*.php');
    
    $modelTables = [];
    foreach ($modelFiles as $modelFile) {
        $content = File::get($modelFile);
        $className = basename($modelFile, '.php');
        
        // Skip if it's the model_list.txt file
        if ($className === 'model_list') {
            continue;
        }
        
        // Check for explicit table name
        if (preg_match("/protected\s+\\\$table\s*=\s*['\"]([^'\"]+)['\"]/", $content, $matches)) {
            $modelTables[$matches[1]] = $className;
        } else {
            // Use class name to infer table name (Laravel convention)
            $inferredTable = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
            $inferredTable = \Illuminate\Support\Str::plural($inferredTable);
            $modelTables[$inferredTable] = $className;
        }
    }
    
    // Get all table references from codebase (DB::table, ->from, etc.)
    $codeReferences = [];
    $searchPaths = [
        app_path(),
        base_path('routes'),
        base_path('database/migrations'),
    ];
    
    foreach ($searchPaths as $searchPath) {
        if (!is_dir($searchPath)) continue;
        
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($searchPath));
        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = File::get($file->getPathname());
                
                // Find DB::table('table_name')
                if (preg_match_all("/DB::table\(['\"]([^'\"]+)['\"]\)/", $content, $matches)) {
                    foreach ($matches[1] as $table) {
                        $codeReferences[$table] = true;
                    }
                }
                
                // Find ->from('table_name')
                if (preg_match_all("/->from\(['\"]([^'\"]+)['\"]\)/", $content, $matches)) {
                    foreach ($matches[1] as $table) {
                        $codeReferences[$table] = true;
                    }
                }
                
                // Find ->join('table_name')
                if (preg_match_all("/->join\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
                    foreach ($matches[1] as $table) {
                        $codeReferences[$table] = true;
                    }
                }
                
                // Find ->leftJoin('table_name')
                if (preg_match_all("/->leftJoin\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
                    foreach ($matches[1] as $table) {
                        $codeReferences[$table] = true;
                    }
                }
                
                // Find pivot tables in belongsToMany relationships
                // Pattern: belongsToMany(..., 'pivot_table_name', ...)
                if (preg_match_all("/belongsToMany\([^,]+,\s*['\"]([^'\"]+)['\"]/", $content, $matches)) {
                    foreach ($matches[1] as $table) {
                        $codeReferences[$table] = true;
                    }
                }
            }
        }
    }
    
    // Check foreign key relationships
    $referencedByFK = [];
    foreach ($allTables as $table) {
        try {
            $fkQuery = DB::select("
                SELECT DISTINCT REFERENCED_TABLE_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = ? 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$databaseName]);
            
            foreach ($fkQuery as $fk) {
                if ($fk->REFERENCED_TABLE_NAME) {
                    $referencedByFK[$fk->REFERENCED_TABLE_NAME] = true;
                }
            }
        } catch (\Exception $e) {
            // Ignore
        }
    }
    
    // Combine all referenced tables
    $allReferencedTables = array_unique(array_merge(
        array_keys($modelTables),
        array_keys($codeReferences),
        array_keys($referencedByFK)
    ));
    
    // Find unused tables
    $unusedTables = [];
    $usedTables = [];
    
    foreach ($allTables as $table) {
        // Skip system tables
        if (in_array($table, $systemTables)) {
            $usedTables[$table] = 'System table';
            continue;
        }
        
        $isReferenced = false;
        $referenceReason = [];
        
        // Check if table has a model
        if (isset($modelTables[$table])) {
            $isReferenced = true;
            $referenceReason[] = "Model: {$modelTables[$table]}";
        }
        
        // Check if referenced in code
        if (isset($codeReferences[$table])) {
            $isReferenced = true;
            $referenceReason[] = "Referenced in code";
        }
        
        // Check if referenced by foreign key
        if (isset($referencedByFK[$table])) {
            $isReferenced = true;
            $referenceReason[] = "Referenced by foreign key";
        }
        
        // Check if this table references other tables (has foreign keys)
        try {
            $hasFK = DB::select("
                SELECT COUNT(*) as count
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$databaseName, $table]);
            
            if (!empty($hasFK) && $hasFK[0]->count > 0) {
                $isReferenced = true;
                $referenceReason[] = "Has foreign keys";
            }
        } catch (\Exception $e) {
            // Ignore
        }
        
        if ($isReferenced) {
            $usedTables[$table] = implode(', ', $referenceReason);
        } else {
            $unusedTables[] = $table;
        }
    }
    
    echo "=== ANALYSIS RESULTS ===\n\n";
    echo "Total tables: " . count($allTables) . "\n";
    echo "Used tables: " . count($usedTables) . "\n";
    echo "Potentially unused tables: " . count($unusedTables) . "\n\n";
    
    if (!empty($unusedTables)) {
        echo "=== POTENTIALLY UNUSED TABLES ===\n\n";
        foreach ($unusedTables as $table) {
            echo "- $table\n";
        }
    } else {
        echo "No unused tables found.\n";
    }
    
    // Show some statistics
    echo "\n=== USED TABLES SUMMARY (first 20) ===\n\n";
    $count = 0;
    foreach ($usedTables as $table => $reason) {
        if ($count++ >= 20) break;
        echo "- $table: $reason\n";
    }
    if (count($usedTables) > 20) {
        echo "... and " . (count($usedTables) - 20) . " more\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

