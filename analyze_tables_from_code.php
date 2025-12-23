<?php

/**
 * Analyze tables from codebase without database connection
 * Identifies tables that are no longer needed
 */

$migrationPath = __DIR__ . '/database/migrations';
$appPath = __DIR__ . '/app';
$modelPath = __DIR__ . '/app/Models';

// Get all table names from migrations
function getTablesFromMigrations($migrationPath) {
    $tables = [];
    $droppedTables = [];
    $files = glob($migrationPath . '/*.php');
    
    foreach ($files as $file) {
        $content = file_get_contents($file);
        
        // Find Schema::create('table_name')
        if (preg_match_all("/Schema::create\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
            foreach ($matches[1] as $table) {
                $tables[$table] = ['created' => true, 'file' => basename($file)];
            }
        }
        
        // Find Schema::dropIfExists('table_name') in up() method
        if (preg_match_all("/Schema::dropIfExists\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
            // Check if it's in up() method (not down())
            $upMethod = preg_match("/public\s+function\s+up\([^\{]*\{/s", $content);
            if ($upMethod) {
                foreach ($matches[1] as $table) {
                    $droppedTables[$table] = ['file' => basename($file)];
                }
            }
        }
    }
    
    return ['created' => $tables, 'dropped' => $droppedTables];
}

// Get all table references from codebase
function getTableReferences($appPath) {
    $references = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($appPath)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            
            // DB::table('table_name')
            if (preg_match_all("/DB::table\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
                foreach ($matches[1] as $table) {
                    $references[$table][] = $file->getPathname();
                }
            }
            
            // ->from('table_name')
            if (preg_match_all("/->from\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
                foreach ($matches[1] as $table) {
                    $references[$table][] = $file->getPathname();
                }
            }
        }
    }
    
    return $references;
}

// Get all models and their table names
function getModelTables($modelPath) {
    $modelTables = [];
    $files = glob($modelPath . '/*.php');
    
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $className = basename($file, '.php');
        
        // Check for explicit table name
        if (preg_match("/protected\s+\\\$table\s*=\s*['\"]([^'\"]+)['\"]/", $content, $matches)) {
            $modelTables[$matches[1]] = $className;
        } else {
            // Infer table name from class name (simple pluralization)
            $inferred = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
            // Simple pluralization - add 's' if not ending in s
            if (!preg_match('/[sxzh]$/i', $inferred)) {
                $inferred .= 's';
            }
            $modelTables[$inferred] = $className;
        }
    }
    
    return $modelTables;
}

// Run analysis
echo "=== TABLE USAGE ANALYSIS ===\n\n";

$migrationData = getTablesFromMigrations($migrationPath);
$codeReferences = getTableReferences($appPath);
$modelTables = getModelTables($modelPath);

echo "Tables created in migrations: " . count($migrationData['created']) . "\n";
echo "Tables dropped in migrations: " . count($migrationData['dropped']) . "\n";
echo "Tables referenced in code: " . count($codeReferences) . "\n";
echo "Tables with models: " . count($modelTables) . "\n\n";

// Find explicitly dropped tables
echo "=== TABLES EXPLICITLY MARKED FOR DELETION ===\n\n";
foreach ($migrationData['dropped'] as $table => $info) {
    echo "✓ $table (dropped in: {$info['file']})\n";
    // Check if still referenced in code
    if (isset($codeReferences[$table])) {
        echo "  ⚠ WARNING: Still referenced in code!\n";
        foreach (array_unique($codeReferences[$table]) as $file) {
            echo "    - " . str_replace(__DIR__ . '/', '', $file) . "\n";
        }
    }
}

// Find tables referenced in code but no model/migration
echo "\n=== TABLES REFERENCED IN CODE BUT NO CLEAR MODEL/MIGRATION ===\n\n";
$unclearTables = [];
foreach ($codeReferences as $table => $files) {
    if (!isset($modelTables[$table]) && !isset($migrationData['created'][$table])) {
        $unclearTables[$table] = $files;
    }
}

if (empty($unclearTables)) {
    echo "None found.\n";
} else {
    foreach ($unclearTables as $table => $files) {
        echo "? $table\n";
        echo "  Referenced in:\n";
        foreach (array_unique($files) as $file) {
            echo "    - " . str_replace(__DIR__ . '/', '', $file) . "\n";
        }
        echo "\n";
    }
}

// Find tables with models but no code references (potentially unused)
echo "\n=== POTENTIALLY UNUSED TABLES (Have model but no code references) ===\n\n";
$potentiallyUnused = [];
foreach ($modelTables as $table => $model) {
    if (!isset($codeReferences[$table])) {
        $potentiallyUnused[$table] = $model;
    }
}

// Filter out Laravel system tables
$systemTables = [
    'migrations', 'cache', 'cache_locks', 'jobs', 'job_batches', 
    'failed_jobs', 'sessions', 'password_reset_tokens'
];

$potentiallyUnused = array_filter($potentiallyUnused, function($table) use ($systemTables) {
    return !in_array($table, $systemTables);
});

if (empty($potentiallyUnused)) {
    echo "None found.\n";
} else {
    foreach ($potentiallyUnused as $table => $model) {
        echo "⚠ $table (Model: $model)\n";
    }
}

echo "\n=== SUMMARY OF TABLES TO INVESTIGATE ===\n\n";
echo "1. Explicitly dropped (safe to delete if migration ran):\n";
foreach ($migrationData['dropped'] as $table => $info) {
    if (!isset($codeReferences[$table])) {
        echo "   - $table\n";
    }
}

echo "\n2. Referenced in code but no model/migration found:\n";
foreach ($unclearTables as $table => $files) {
    echo "   - $table (check if this is a view or legacy table)\n";
}

echo "\n3. Has model but no code references:\n";
foreach ($potentiallyUnused as $table => $model) {
    echo "   - $table\n";
}




