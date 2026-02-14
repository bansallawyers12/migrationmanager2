<?php
/**
 * One-off script to verify staff data was copied from admins.
 * Run: php verify_staff_data.php
 */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== Staff Data Verification ===\n\n";

if (!Schema::hasTable('staff')) {
    echo "⚠ Staff table does not exist. Run: php artisan migrate\n";
    exit(1);
}

$staffCount = (int) DB::table('staff')->count();
echo "Staff table count: $staffCount\n";

$adminsStaffCount = (int) DB::table('admins')->where('role', '!=', '7')->count();
echo "Admins (role != 7) count: $adminsStaffCount\n";

$staffMaxId = (int) DB::table('staff')->max('id');
echo "Staff max ID: $staffMaxId\n\n";

if ($staffCount > 0 && $staffCount == $adminsStaffCount) {
    echo "✓ Count match: staff and admins staff counts are equal.\n";
} elseif ($staffCount > 0) {
    echo "⚠ Count mismatch: staff=$staffCount, admins_staff=$adminsStaffCount\n";
} elseif ($staffCount === 0 && $adminsStaffCount > 0) {
    echo "⚠ Staff table is empty. Run copy migration: php artisan migrate\n";
} else {
    echo "✓ Verification complete.\n";
}
