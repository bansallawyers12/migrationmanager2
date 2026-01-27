<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== INVESTIGATING CLIENTS TABLE ===\n\n";

// Check if used in codebase
echo "1. Code references: ";
$codeCheck = shell_exec('grep -r "DB::table.*clients" app/ 2>/dev/null | wc -l');
echo trim($codeCheck) . " references\n\n";

// Last activity
$recent = DB::table('clients')->orderBy('created_at', 'desc')->limit(1)->first();
echo "2. Last created: " . ($recent->created_at ?? 'NULL') . "\n";

$updated = DB::table('clients')->orderBy('updated_at', 'desc')->limit(1)->first();
echo "3. Last updated: " . ($updated->updated_at ?? 'NULL') . "\n\n";

// Sample data
echo "4. Sample records:\n";
$sample = DB::table('clients')->limit(2)->get(['id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at']);
foreach ($sample as $s) {
    echo "   - ID: {$s->id}, Name: {$s->first_name} {$s->last_name}, Created: {$s->created_at}\n";
}

// Check if data exists in admins table
echo "\n5. Comparing with admins table:\n";
$adminCount = DB::table('admins')->where('role', 7)->count();
echo "   - Admins with role=7 (clients): {$adminCount}\n";

$clientsCount = DB::table('clients')->count();
echo "   - Clients table rows: {$clientsCount}\n";

// Check for any recent activity (last 30 days)
$recentActivity = DB::table('clients')
    ->where('updated_at', '>=', now()->subDays(30))
    ->count();
echo "\n6. Recent activity (last 30 days): {$recentActivity} rows\n";

echo "\n=== RECOMMENDATION ===\n";
if ($recentActivity == 0) {
    echo "✓ SAFE TO DELETE - No recent activity\n";
} else {
    echo "⚠ CAUTION - Has recent activity\n";
}
