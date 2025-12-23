<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Checking MySQL database for old appointment system tables...\n\n";

// List of tables that should exist for old appointment system
$oldAppointmentTables = [
    'appointments',
    'appointment_logs',
    'book_services',
    'book_service_disable_slots',
    'book_service_slot_per_person',
    'nature_of_enquiries',
];

try {
    // Try to connect to MySQL
    $connection = DB::connection('mysql');
    $tables = $connection->select("SHOW TABLES");
    
    $tableColumn = 'Tables_in_' . config('database.connections.mysql.database');
    
    $existingTables = [];
    foreach ($tables as $table) {
        $tableName = $table->$tableColumn;
        $existingTables[] = $tableName;
    }
    
    echo "Found " . count($existingTables) . " tables in MySQL database.\n\n";
    echo "Old Appointment System Tables Status:\n";
    echo str_repeat("=", 60) . "\n";
    
    $foundTables = [];
    foreach ($oldAppointmentTables as $tableName) {
        $exists = in_array($tableName, $existingTables);
        $status = $exists ? "✓ EXISTS" : "✗ NOT FOUND";
        echo sprintf("%-40s %s\n", $tableName, $status);
        
        if ($exists) {
            $foundTables[] = $tableName;
            
            // Get row count
            try {
                $count = $connection->table($tableName)->count();
                echo sprintf("  └─ Row count: %d\n", $count);
            } catch (\Exception $e) {
                echo sprintf("  └─ Error counting rows: %s\n", $e->getMessage());
            }
        }
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Summary:\n";
    echo "  - Tables found: " . count($foundTables) . " / " . count($oldAppointmentTables) . "\n";
    
    if (count($foundTables) > 0) {
        echo "\n⚠️  WARNING: The following tables can be removed:\n";
        foreach ($foundTables as $table) {
            echo "    - $table\n";
        }
    } else {
        echo "\n✓ All old appointment tables have already been removed.\n";
    }
    
} catch (\Exception $e) {
    echo "Error connecting to MySQL: " . $e->getMessage() . "\n";
    echo "\nTrying PostgreSQL instead...\n\n";
    
    try {
        $connection = DB::connection('pgsql');
        $tables = $connection->select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
        
        $existingTables = [];
        foreach ($tables as $table) {
            $existingTables[] = $table->tablename;
        }
        
        echo "Found " . count($existingTables) . " tables in PostgreSQL database.\n\n";
        echo "Old Appointment System Tables Status:\n";
        echo str_repeat("=", 60) . "\n";
        
        $foundTables = [];
        foreach ($oldAppointmentTables as $tableName) {
            $exists = in_array($tableName, $existingTables);
            $status = $exists ? "✓ EXISTS" : "✗ NOT FOUND";
            echo sprintf("%-40s %s\n", $tableName, $status);
            
            if ($exists) {
                $foundTables[] = $tableName;
                
                // Get row count
                try {
                    $count = $connection->table($tableName)->count();
                    echo sprintf("  └─ Row count: %d\n", $count);
                } catch (\Exception $e) {
                    echo sprintf("  └─ Error counting rows: %s\n", $e->getMessage());
                }
            }
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "Summary:\n";
        echo "  - Tables found: " . count($foundTables) . " / " . count($oldAppointmentTables) . "\n";
        
        if (count($foundTables) > 0) {
            echo "\n⚠️  WARNING: The following tables can be removed:\n";
            foreach ($foundTables as $table) {
                echo "    - $table\n";
            }
        } else {
            echo "\n✓ All old appointment tables have already been removed.\n";
        }
        
    } catch (\Exception $e2) {
        echo "Error connecting to PostgreSQL: " . $e2->getMessage() . "\n";
    }
}

