<?php
/**
 * Migration Verification Script
 * 
 * Run this after data migration (before cleanup) to verify everything worked correctly
 */

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║         ADMIN TO CLIENT MIGRATION VERIFICATION                 ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Check if tables exist
echo "1. Checking Tables...\n";
echo "   ----------------\n";

$tables = ['admins', 'clients', 'admin_to_client_mapping'];
foreach ($tables as $table) {
    $exists = Schema::hasTable($table);
    $status = $exists ? '✓' : '✗';
    echo "   {$status} {$table}: " . ($exists ? 'EXISTS' : 'MISSING') . "\n";
}
echo "\n";

// Count records
echo "2. Record Counts...\n";
echo "   ---------------\n";

$adminCount = DB::table('admins')->count();
$clientCount = DB::table('clients')->count();
$mappingCount = DB::table('admin_to_client_mapping')->count();

echo "   Admins table:  {$adminCount} records\n";
echo "   Clients table: {$clientCount} records\n";
echo "   Mapping table: {$mappingCount} records\n";
echo "\n";

// Check type distribution
echo "3. Type Distribution...\n";
echo "   -------------------\n";

$types = DB::table('clients')
    ->selectRaw('type, COUNT(*) as count')
    ->groupBy('type')
    ->get();

foreach ($types as $type) {
    echo "   {$type->type}: {$type->count} records\n";
}
echo "\n";

// Check for unmapped records
echo "4. Checking for Unmapped Records...\n";
echo "   --------------------------------\n";

$unmapped = DB::table('admins')
    ->whereNotIn('id', function($query) {
        $query->select('old_admin_id')->from('admin_to_client_mapping');
    })
    ->where(function($query) {
        $query->where('type', 'client')
              ->orWhere('type', 'lead')
              ->orWhereNotNull('client_id')
              ->orWhereNotNull('lead_id')
              ->orWhereNotNull('lead_status');
    })
    ->count();

if ($unmapped > 0) {
    echo "   ⚠️  WARNING: {$unmapped} client/lead records were NOT migrated!\n";
    echo "   Please review these records before proceeding.\n";
    
    $unmappedRecords = DB::table('admins')
        ->whereNotIn('id', function($query) {
            $query->select('old_admin_id')->from('admin_to_client_mapping');
        })
        ->where(function($query) {
            $query->where('type', 'client')
                  ->orWhere('type', 'lead')
                  ->orWhereNotNull('client_id')
                  ->orWhereNotNull('lead_id')
                  ->orWhereNotNull('lead_status');
        })
        ->limit(10)
        ->get(['id', 'first_name', 'last_name', 'email', 'type']);
    
    echo "\n   Sample unmapped records:\n";
    foreach ($unmappedRecords as $record) {
        echo "   - ID: {$record->id}, {$record->first_name} {$record->last_name} ({$record->email}) - Type: {$record->type}\n";
    }
} else {
    echo "   ✓ All client/lead records migrated successfully!\n";
}
echo "\n";

// Verify foreign key updates
echo "5. Foreign Key Verification...\n";
echo "   ---------------------------\n";

$tables = [
    'forms_956' => 'client_id',
    'client_eoi_references' => 'client_id',
    'client_test_scores' => 'client_id',
    'client_experiences' => 'client_id',
    'client_qualifications' => 'client_id',
    'client_spouse_details' => 'client_id',
    'client_occupations' => 'client_id',
    'client_relationships' => 'client_id',
    'lead_followups' => 'lead_id',
];

$allGood = true;
foreach ($tables as $table => $column) {
    if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
        $count = DB::table($table)->whereNotNull($column)->count();
        $orphaned = DB::table($table)
            ->whereNotNull($column)
            ->whereNotIn($column, function($query) {
                $query->select('id')->from('clients');
            })
            ->count();
        
        if ($orphaned > 0) {
            echo "   ⚠️  {$table}.{$column}: {$count} records ({$orphaned} orphaned)\n";
            $allGood = false;
        } else {
            echo "   ✓ {$table}.{$column}: {$count} records\n";
        }
    }
}

if ($allGood) {
    echo "   ✓ All foreign keys updated correctly!\n";
}
echo "\n";

// Data integrity checks
echo "6. Data Integrity Checks...\n";
echo "   ------------------------\n";

// Check for duplicate client_ids
$duplicates = DB::table('clients')
    ->selectRaw('client_id, COUNT(*) as count')
    ->whereNotNull('client_id')
    ->groupBy('client_id')
    ->having('count', '>', 1)
    ->count();

if ($duplicates > 0) {
    echo "   ⚠️  WARNING: {$duplicates} duplicate client_id values found!\n";
} else {
    echo "   ✓ No duplicate client_ids\n";
}

// Check for clients without email
$noEmail = DB::table('clients')->whereNull('email')->count();
if ($noEmail > 0) {
    echo "   ⚠️  INFO: {$noEmail} clients without email\n";
} else {
    echo "   ✓ All clients have email addresses\n";
}

// Check for clients without names
$noName = DB::table('clients')
    ->whereNull('first_name')
    ->whereNull('last_name')
    ->count();
if ($noName > 0) {
    echo "   ⚠️  WARNING: {$noName} clients without names!\n";
} else {
    echo "   ✓ All clients have names\n";
}

echo "\n";

// Summary
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                      VERIFICATION SUMMARY                      ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$status = ($unmapped == 0 && $allGood && $duplicates == 0 && $noName == 0) ? 'PASSED' : 'NEEDS ATTENTION';
$color = ($status == 'PASSED') ? '✓' : '⚠️';

echo "   Overall Status: {$color} {$status}\n";
echo "\n";

if ($status == 'PASSED') {
    echo "   ✓ Migration completed successfully!\n";
    echo "   ✓ All data migrated correctly\n";
    echo "   ✓ Foreign keys updated\n";
    echo "   ✓ Data integrity verified\n";
    echo "\n";
    echo "   Next steps:\n";
    echo "   1. Test the application thoroughly\n";
    echo "   2. Run cleanup migration if everything works\n";
    echo "\n";
} else {
    echo "   ⚠️  Please review the warnings above before proceeding.\n";
    echo "   ⚠️  DO NOT run the cleanup migration yet!\n";
    echo "\n";
    echo "   Next steps:\n";
    echo "   1. Fix any issues identified above\n";
    echo "   2. Run this verification script again\n";
    echo "   3. Only proceed to cleanup after all checks pass\n";
    echo "\n";
}

