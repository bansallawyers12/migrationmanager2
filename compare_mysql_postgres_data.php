<?php

/**
 * Script to compare data between MySQL and PostgreSQL databases
 * - Compares row counts for each table
 * - Identifies tables with data discrepancies
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Comparing data between MySQL and PostgreSQL databases...\n\n";

try {
    // Get MySQL connection
    $mysqlConnection = DB::connection('mysql_source');
    
    // Get PostgreSQL connection
    $pgsqlConnection = DB::connection('pgsql');
    
    // Get all tables from PostgreSQL (as reference)
    $pgsqlTables = [];
    $tables = $pgsqlConnection->select("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_type = 'BASE TABLE'
        ORDER BY table_name
    ");
    
    foreach ($tables as $table) {
        $pgsqlTables[] = $table->table_name;
    }
    
    echo "Found " . count($pgsqlTables) . " tables to compare\n\n";
    echo str_repeat("=", 100) . "\n";
    printf("%-45s | %15s | %15s | %15s\n", "Table Name", "MySQL Rows", "PostgreSQL Rows", "Difference");
    echo str_repeat("=", 100) . "\n";
    
    $comparisonResults = [];
    $tablesWithDifferences = [];
    $tablesWithErrors = [];
    $totalMySQLRows = 0;
    $totalPostgresRows = 0;
    
    foreach ($pgsqlTables as $table) {
        try {
            // Get MySQL row count
            $mysqlCount = 0;
            try {
                $result = $mysqlConnection->selectOne("SELECT COUNT(*) as count FROM `{$table}`");
                $mysqlCount = (int)$result->count;
            } catch (\Exception $e) {
                // Table might not exist in MySQL or error accessing it
                $mysqlCount = -1;
            }
            
            // Get PostgreSQL row count
            $pgsqlCount = 0;
            try {
                $result = $pgsqlConnection->selectOne("SELECT COUNT(*) as count FROM {$table}");
                $pgsqlCount = (int)$result->count;
            } catch (\Exception $e) {
                $pgsqlCount = -1;
            }
            
            // Calculate difference
            $difference = 0;
            if ($mysqlCount >= 0 && $pgsqlCount >= 0) {
                $difference = $pgsqlCount - $mysqlCount;
                $totalMySQLRows += $mysqlCount;
                $totalPostgresRows += $pgsqlCount;
            }
            
            // Format counts for display
            $mysqlDisplay = $mysqlCount >= 0 ? number_format($mysqlCount) : "ERROR";
            $pgsqlDisplay = $pgsqlCount >= 0 ? number_format($pgsqlCount) : "ERROR";
            $diffDisplay = $difference != 0 ? number_format($difference) : "0";
            
            // Check if there's a difference
            $status = "";
            if ($mysqlCount >= 0 && $pgsqlCount >= 0) {
                if ($difference != 0) {
                    $status = " ⚠ DIFF";
                    $tablesWithDifferences[] = [
                        'table' => $table,
                        'mysql' => $mysqlCount,
                        'postgres' => $pgsqlCount,
                        'difference' => $difference
                    ];
                } else {
                    $status = " ✓";
                }
            } else {
                $status = " ❌ ERROR";
                $tablesWithErrors[] = [
                    'table' => $table,
                    'mysql' => $mysqlCount,
                    'postgres' => $pgsqlCount
                ];
            }
            
            printf("%-45s | %15s | %15s | %15s%s\n", 
                $table, 
                $mysqlDisplay, 
                $pgsqlDisplay, 
                $diffDisplay,
                $status
            );
            
            $comparisonResults[$table] = [
                'mysql' => $mysqlCount,
                'postgres' => $pgsqlCount,
                'difference' => $difference
            ];
            
        } catch (\Exception $e) {
            printf("%-45s | %15s | %15s | %15s\n", 
                $table, 
                "ERROR", 
                "ERROR", 
                "N/A"
            );
            $tablesWithErrors[] = [
                'table' => $table,
                'error' => $e->getMessage()
            ];
        }
    }
    
    echo str_repeat("=", 100) . "\n";
    printf("%-45s | %15s | %15s | %15s\n", 
        "TOTAL", 
        number_format($totalMySQLRows), 
        number_format($totalPostgresRows),
        number_format($totalPostgresRows - $totalMySQLRows)
    );
    echo str_repeat("=", 100) . "\n\n";
    
    // Summary
    echo str_repeat("=", 100) . "\n";
    echo "SUMMARY\n";
    echo str_repeat("=", 100) . "\n\n";
    
    echo "Total tables compared: " . count($pgsqlTables) . "\n";
    echo "Tables with matching row counts: " . (count($pgsqlTables) - count($tablesWithDifferences) - count($tablesWithErrors)) . "\n";
    echo "Tables with differences: " . count($tablesWithDifferences) . "\n";
    echo "Tables with errors: " . count($tablesWithErrors) . "\n\n";
    
    if (!empty($tablesWithDifferences)) {
        echo str_repeat("-", 100) . "\n";
        echo "TABLES WITH ROW COUNT DIFFERENCES:\n";
        echo str_repeat("-", 100) . "\n";
        printf("%-45s | %15s | %15s | %15s\n", "Table Name", "MySQL", "PostgreSQL", "Difference");
        echo str_repeat("-", 100) . "\n";
        
        foreach ($tablesWithDifferences as $diff) {
            printf("%-45s | %15s | %15s | %15s\n",
                $diff['table'],
                number_format($diff['mysql']),
                number_format($diff['postgres']),
                number_format($diff['difference'])
            );
        }
        echo "\n";
    }
    
    if (!empty($tablesWithErrors)) {
        echo str_repeat("-", 100) . "\n";
        echo "TABLES WITH ERRORS:\n";
        echo str_repeat("-", 100) . "\n";
        foreach ($tablesWithErrors as $error) {
            echo "Table: {$error['table']}\n";
            if (isset($error['error'])) {
                echo "  Error: {$error['error']}\n";
            } else {
                echo "  MySQL: " . ($error['mysql'] >= 0 ? number_format($error['mysql']) : "ERROR") . "\n";
                echo "  PostgreSQL: " . ($error['postgres'] >= 0 ? number_format($error['postgres']) : "ERROR") . "\n";
            }
            echo "\n";
        }
    }
    
    // Save detailed report
    $reportFile = 'data_comparison_report.txt';
    $report = "Data Comparison Report\n";
    $report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $report .= "MySQL Database: " . config('database.connections.mysql_source.database') . "\n";
    $report .= "PostgreSQL Database: " . config('database.connections.pgsql.database') . "\n\n";
    $report .= str_repeat("=", 100) . "\n\n";
    
    $report .= "TOTAL ROWS:\n";
    $report .= "  MySQL: " . number_format($totalMySQLRows) . "\n";
    $report .= "  PostgreSQL: " . number_format($totalPostgresRows) . "\n";
    $report .= "  Difference: " . number_format($totalPostgresRows - $totalMySQLRows) . "\n\n";
    
    $report .= str_repeat("=", 100) . "\n\n";
    
    if (!empty($tablesWithDifferences)) {
        $report .= "TABLES WITH DIFFERENCES (" . count($tablesWithDifferences) . "):\n";
        $report .= str_repeat("-", 100) . "\n";
        foreach ($tablesWithDifferences as $diff) {
            $report .= sprintf("%-45s | MySQL: %10s | PostgreSQL: %10s | Difference: %10s\n",
                $diff['table'],
                number_format($diff['mysql']),
                number_format($diff['postgres']),
                number_format($diff['difference'])
            );
        }
        $report .= "\n";
    }
    
    $report .= "\nDETAILED COMPARISON:\n";
    $report .= str_repeat("-", 100) . "\n";
    foreach ($comparisonResults as $table => $result) {
        $report .= sprintf("%-45s | MySQL: %10s | PostgreSQL: %10s | Difference: %10s\n",
            $table,
            $result['mysql'] >= 0 ? number_format($result['mysql']) : "ERROR",
            $result['postgres'] >= 0 ? number_format($result['postgres']) : "ERROR",
            $result['mysql'] >= 0 && $result['postgres'] >= 0 ? number_format($result['difference']) : "N/A"
        );
    }
    
    file_put_contents($reportFile, $report);
    echo "Detailed report saved to: $reportFile\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}


