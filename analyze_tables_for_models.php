<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== TABLE ANALYSIS FOR MODEL CREATION ===\n\n";

$tables = [
    'account_all_invoice_receipts',
    'message_recipients',
    'client_occupation_lists',
    'email_label_mail_report'
];

foreach ($tables as $table) {
    echo "TABLE: {$table}\n";
    echo str_repeat("-", 50) . "\n";
    
    $columns = Schema::getColumnListing($table);
    echo "Columns: " . implode(', ', $columns) . "\n";
    
    $count = DB::table($table)->count();
    echo "Row count: {$count}\n";
    
    // Check for relationships
    $hasForeignKeys = in_array('id', $columns);
    $hasTimestamps = in_array('created_at', $columns) && in_array('updated_at', $columns);
    
    echo "Has ID: " . ($hasForeignKeys ? 'Yes' : 'No') . "\n";
    echo "Has timestamps: " . ($hasTimestamps ? 'Yes' : 'No') . "\n\n";
}

echo "\n=== RECOMMENDATIONS ===\n\n";

echo "1. account_all_invoice_receipts:\n";
echo "   - Usage: Heavy (60 uses in accounting)\n";
echo "   - Recommendation: CREATE MODEL ✓\n";
echo "   - Reason: Complex queries, relationships, better code maintenance\n\n";

echo "2. message_recipients:\n";
echo "   - Usage: Moderate (14 uses in messaging)\n";
echo "   - Recommendation: CREATE MODEL ✓\n";
echo "   - Reason: Could use relationships with Message model\n\n";

echo "3. client_occupation_lists:\n";
echo "   - Usage: Light (few uses)\n";
echo "   - Recommendation: OPTIONAL - Can stay as DB::table()\n";
echo "   - Reason: Simple reference data, infrequent access\n\n";

echo "4. email_label_mail_report:\n";
echo "   - Usage: Pivot table\n";
echo "   - Recommendation: NO MODEL NEEDED ✓\n";
echo "   - Reason: Standard Laravel pivot, accessed via relationships\n\n";
