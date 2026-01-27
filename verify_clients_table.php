<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Check if clients table exists
$result = DB::select("
    SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name = 'clients'
    ) as exists
");

if ($result[0]->exists) {
    // Get columns
    $columns = DB::select("
        SELECT column_name, data_type 
        FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'clients'
        ORDER BY ordinal_position
    ");
    
    // Get row count
    $count = DB::table('clients')->count();
    
    echo "✓ 'clients' table EXISTS\n";
    echo "Rows: {$count}\n\n";
    echo "Columns:\n";
    foreach ($columns as $col) {
        echo "  - {$col->column_name} ({$col->data_type})\n";
    }
} else {
    echo "✗ 'clients' table DOES NOT EXIST\n";
}
