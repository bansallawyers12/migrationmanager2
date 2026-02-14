<?php
/**
 * Verify staff data migration from admins to staff table.
 * Run: php verify_staff_migration.php
 */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Staff Migration Verification ===\n\n";

// 1. Count comparison
$hasIsDeleted = Schema::hasColumn('admins', 'is_deleted');
$adminsStaffCount = DB::table('admins')
    ->where('role', '!=', 7)
    ->when($hasIsDeleted, fn ($q) => $q->whereNull('is_deleted'))
    ->count();

$staffCount = DB::table('staff')->count();

echo "1. ROW COUNT\n";
echo "   Admins (staff: role != 7): {$adminsStaffCount}\n";
echo "   Staff table: {$staffCount}\n";
echo "   " . ($adminsStaffCount === $staffCount ? "✓ MATCH" : "✗ MISMATCH") . "\n\n";

// 2. Email-based join - all staff should have matching record by email
$adminsEmails = DB::table('admins')
    ->where('role', '!=', 7)
    ->when($hasIsDeleted, fn ($q) => $q->whereNull('is_deleted'))
    ->pluck('email')
    ->toArray();

$staffEmails = DB::table('staff')->pluck('email')->toArray();

$missingInStaff = array_diff($adminsEmails, $staffEmails);
$extraInStaff = array_diff($staffEmails, $adminsEmails);

echo "2. EMAIL COVERAGE\n";
echo "   Admins staff emails: " . count($adminsEmails) . "\n";
echo "   Staff emails: " . count($staffEmails) . "\n";
echo "   Emails in admins but NOT in staff: " . count($missingInStaff);
if (!empty($missingInStaff)) {
    echo " - " . implode(', ', array_slice($missingInStaff, 0, 5)) . (count($missingInStaff) > 5 ? '...' : '');
}
echo "\n";
echo "   Emails in staff but NOT in admins: " . count($extraInStaff);
if (!empty($extraInStaff)) {
    echo " - " . implode(', ', array_slice($extraInStaff, 0, 5)) . (count($extraInStaff) > 5 ? '...' : '');
}
echo "\n";
echo "   " . (empty($missingInStaff) && empty($extraInStaff) ? "✓ All emails match" : "✗ Email mismatch") . "\n\n";

// 3. Spot-check: compare key fields for sample records (by email)
echo "3. SPOT-CHECK (sample of 5 by email match)\n";
$sample = DB::table('admins')
    ->where('role', '!=', 7)
    ->when($hasIsDeleted, fn ($q) => $q->whereNull('is_deleted'))
    ->select(['id', 'first_name', 'last_name', 'email', 'role', 'status'])
    ->limit(5)
    ->get();

$allMatch = true;
foreach ($sample as $a) {
    $s = DB::table('staff')->where('email', $a->email)->first();
    if (!$s) {
        echo "   ✗ No staff record for email: {$a->email}\n";
        $allMatch = false;
        continue;
    }
    $nameMatch = ($a->first_name === $s->first_name && $a->last_name === $s->last_name);
    $roleMatch = ($a->role == $s->role);
    $statusMatch = ($a->status == $s->status);
    $idMatch = ($a->id == $s->id);
    $rowOk = $nameMatch && $roleMatch && $statusMatch;
    if (!$rowOk) $allMatch = false;
    $status = $rowOk ? '✓' : '✗';
    echo "   {$status} {$a->email}: name=" . ($nameMatch ? 'OK' : 'MISMATCH') . ", role=" . ($roleMatch ? 'OK' : 'MISMATCH') . ", status=" . ($statusMatch ? 'OK' : 'MISMATCH') . ", id_preserved=" . ($idMatch ? 'yes' : 'no') . "\n";
}
echo "   " . ($allMatch ? "✓ All samples OK" : "✗ Some samples failed") . "\n\n";

// 4. ID preservation check (since migration preserves IDs)
echo "4. ID PRESERVATION\n";
$idMismatch = DB::table('admins')
    ->where('admins.role', '!=', 7)
    ->when($hasIsDeleted, fn ($q) => $q->whereNull('admins.is_deleted'))
    ->join('staff', 'admins.email', '=', 'staff.email')
    ->whereColumn('admins.id', '!=', 'staff.id')
    ->count();
echo "   Records with different IDs (same email): {$idMismatch}\n";
echo "   " . ($idMismatch === 0 ? "✓ All IDs preserved" : "✗ Some IDs differ") . "\n\n";

// 5. Nullable/optional columns - check a few critical ones exist
echo "5. COLUMN PRESENCE (staff table)\n";
$cols = ['first_name', 'last_name', 'email', 'password', 'role', 'position', 'team', 'office_id', 'is_migration_agent', 'is_archived'];
foreach ($cols as $c) {
    $has = Schema::hasColumn('staff', $c);
    echo "   " . ($has ? '✓' : '✗') . " {$c}\n";
}

// 6. Full record comparison - all 96 records
echo "\n6. FULL RECORD COMPARISON (all {$staffCount} records)\n";
$fullCheck = DB::select("
    SELECT a.id as admin_id, s.id as staff_id, a.email,
        (a.first_name = s.first_name AND a.last_name = s.last_name) as name_match,
        (a.role = s.role) as role_match,
        (a.password = s.password) as password_match
    FROM admins a
    INNER JOIN staff s ON a.email = s.email
    WHERE a.role != 7
");
$nameMismatch = count(array_filter($fullCheck, fn ($r) => !$r->name_match));
$roleMismatch = count(array_filter($fullCheck, fn ($r) => !$r->role_match));
$passwordMismatch = count(array_filter($fullCheck, fn ($r) => !$r->password_match));
$idMismatchFull = count(array_filter($fullCheck, fn ($r) => $r->admin_id != $r->staff_id));

echo "   Total compared: " . count($fullCheck) . "\n";
echo "   Name mismatches: {$nameMismatch}\n";
echo "   Role mismatches: {$roleMismatch}\n";
echo "   Password mismatches: {$passwordMismatch}\n";
echo "   ID mismatches: {$idMismatchFull}\n";
$fullOk = ($nameMismatch === 0 && $roleMismatch === 0 && $passwordMismatch === 0 && $idMismatchFull === 0);
echo "   " . ($fullOk ? "✓ All 96 records verified" : "✗ Some records have mismatches") . "\n";

echo "\n=== Verification complete ===\n";
