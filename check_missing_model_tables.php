<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

echo "=== CHECKING MODELS FOR MISSING TABLES ===\n\n";

try {
    // Get all model files
    $modelPath = app_path('Models');
    $modelFiles = File::glob($modelPath . '/*.php');
    
    $modelsWithMissingTables = [];
    $modelsChecked = 0;
    
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
        
        // Check if table exists in database
        $tableExists = false;
        try {
            // Use PostgreSQL information_schema to check if table exists
            $result = DB::select("
                SELECT EXISTS (
                    SELECT FROM information_schema.tables 
                    WHERE table_schema = 'public' 
                    AND table_name = ?
                ) as exists
            ", [$tableName]);
            
            if (!empty($result) && $result[0]->exists) {
                $tableExists = true;
            }
        } catch (\Exception $e) {
            // If query fails, assume table doesn't exist
            $tableExists = false;
        }
        
        // If table doesn't exist, add to list
        if (!$tableExists) {
            $modelsWithMissingTables[] = [
                'model' => $className,
                'table' => $tableName
            ];
        }
    }
    
    echo "Total models checked: {$modelsChecked}\n";
    echo "Models with missing tables: " . count($modelsWithMissingTables) . "\n\n";
    
    if (!empty($modelsWithMissingTables)) {
        echo "=== MODELS WITH MISSING TABLES ===\n\n";
        foreach ($modelsWithMissingTables as $item) {
            echo "- {$item['model']} (table: {$item['table']})\n";
        }
    } else {
        echo "✓ All models have corresponding tables in the database.\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
