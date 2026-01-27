<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

echo "=== COMPREHENSIVE MODEL-TABLE VERIFICATION ===\n\n";

try {
    // Get all tables from database
    $tables = DB::select("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_type = 'BASE TABLE' 
        ORDER BY table_name
    ");
    
    $existingTables = [];
    foreach ($tables as $table) {
        $existingTables[] = $table->table_name;
    }
    
    // Get all model files
    $modelPath = app_path('Models');
    $modelFiles = File::glob($modelPath . '/*.php');
    
    $modelToTable = [];
    $tableToModel = [];
    $modelsChecked = 0;
    
    // System tables that don't need models
    $systemTables = [
        'migrations',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'sessions',
        'password_reset_tokens',
        'password_reset_links',
        'personal_access_tokens',
    ];
    
    // Step 1: Check all models and their tables
    foreach ($modelFiles as $modelFile) {
        $className = basename($modelFile, '.php');
        
        // Skip if it's the model_list.txt file
        if ($className === 'model_list') {
            continue;
        }
        
        $modelsChecked++;
        $content = File::get($modelFile);
        
        // Determine table name
        $tableName = null;
        
        // Check for explicit table name
        if (preg_match("/protected\s+\\\$table\s*=\s*['\"]([^'\"]+)['\"]/", $content, $matches)) {
            $tableName = $matches[1];
        } else {
            // Use class name to infer table name (Laravel convention)
            // Convert PascalCase to snake_case and pluralize
            $inferredTable = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
            $tableName = Str::plural($inferredTable);
        }
        
        $modelToTable[$className] = $tableName;
        $tableToModel[$tableName] = $className;
    }
    
    // Step 2: Find models without tables
    $modelsWithMissingTables = [];
    foreach ($modelToTable as $model => $table) {
        if (!in_array($table, $existingTables)) {
            $modelsWithMissingTables[] = [
                'model' => $model,
                'table' => $table
            ];
        }
    }
    
    // Step 3: Find tables without models
    $tablesWithoutModels = [];
    foreach ($existingTables as $table) {
        // Skip system tables
        if (in_array($table, $systemTables)) {
            continue;
        }
        
        // Check if table has a corresponding model
        if (!isset($tableToModel[$table])) {
            $tablesWithoutModels[] = $table;
        }
    }
    
    // Output results
    echo "Total models checked: {$modelsChecked}\n";
    echo "Total tables in database: " . count($existingTables) . "\n\n";
    
    echo "=== MODELS WITH MISSING TABLES ===\n";
    echo "Count: " . count($modelsWithMissingTables) . "\n\n";
    
    if (!empty($modelsWithMissingTables)) {
        foreach ($modelsWithMissingTables as $item) {
            echo "- {$item['model']} → `{$item['table']}`\n";
        }
    } else {
        echo "✓ All models have corresponding tables.\n";
    }
    
    echo "\n=== TABLES WITHOUT MODELS ===\n";
    echo "Count: " . count($tablesWithoutModels) . "\n\n";
    
    if (!empty($tablesWithoutModels)) {
        foreach ($tablesWithoutModels as $table) {
            echo "- `{$table}`\n";
        }
    } else {
        echo "✓ All tables have corresponding models.\n";
    }
    
    // Save to file
    $output = "# Model-Table Mismatch Report\n\n";
    $output .= "This document lists models without tables and tables without models.\n\n";
    $output .= "**Generated:** " . date('F j, Y') . "\n";
    $output .= "**Total Models Checked:** {$modelsChecked}\n";
    $output .= "**Total Tables in Database:** " . count($existingTables) . "\n\n";
    
    $output .= "## Models with Missing Tables\n\n";
    $output .= "**Count:** " . count($modelsWithMissingTables) . "\n\n";
    
    if (!empty($modelsWithMissingTables)) {
        $output .= "| # | Model | Expected Table |\n";
        $output .= "|---|-------|----------------|\n";
        foreach ($modelsWithMissingTables as $index => $item) {
            $output .= "| " . ($index + 1) . " | **{$item['model']}** | `{$item['table']}` |\n";
        }
    } else {
        $output .= "✓ All models have corresponding tables.\n";
    }
    
    $output .= "\n## Tables without Models\n\n";
    $output .= "**Count:** " . count($tablesWithoutModels) . "\n\n";
    
    if (!empty($tablesWithoutModels)) {
        $output .= "| # | Table Name |\n";
        $output .= "|---|------------|\n";
        foreach ($tablesWithoutModels as $index => $table) {
            $output .= "| " . ($index + 1) . " | `{$table}` |\n";
        }
    } else {
        $output .= "✓ All tables have corresponding models.\n";
    }
    
    file_put_contents('MODELS_WITH_MISSING_TABLES.md', $output);
    echo "\n✓ Report saved to MODELS_WITH_MISSING_TABLES.md\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
