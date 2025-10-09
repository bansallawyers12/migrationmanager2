<?php
/**
 * ANZSCO Data Import Script
 * Imports all 4 ANZSCO CSV files with proper overlapping handling
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AnzscoImportService;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                                                                ║\n";
echo "║           ANZSCO Occupation Data Import Script                ║\n";
echo "║                                                                ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Check database connection
try {
    DB::connection()->getPdo();
    echo "✅ Database connection successful\n\n";
} catch (\Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Check if table exists
$tableExists = DB::select("SHOW TABLES LIKE 'anzsco_occupations'");
if (empty($tableExists)) {
    echo "❌ Table 'anzsco_occupations' does not exist!\n";
    echo "   Please run migrations first: php artisan migrate\n\n";
    exit(1);
}
echo "✅ Table 'anzsco_occupations' exists\n\n";

// Files to import
$files = [
    ['name' => 'MLTSSL', 'path' => 'public/anzsco_mltssl_completed.csv', 'expected' => 213],
    ['name' => 'STSOL', 'path' => 'public/anzsco_stsol_completed.csv', 'expected' => 206],
    ['name' => 'ROL', 'path' => 'public/anzsco_rol_completed.csv', 'expected' => 78],
    ['name' => 'CSOL', 'path' => 'public/anzsco_csol_completed.csv', 'expected' => 457],
];

// Column mapping
$columnMapping = [
    'anzsco_code' => 'anzsco_code',
    'occupation_title' => 'occupation_title',
    'skill_level' => 'skill_level',
    'is_on_mltssl' => 'mltssl',
    'is_on_stsol' => 'stsol',
    'is_on_rol' => 'rol',
    'is_on_csol' => 'csol',
    'assessing_authority' => 'assessing_authority',
    'assessment_validity_years' => 'validity_years',
    'additional_info' => 'additional_info',
    'alternate_titles' => 'alternate_titles'
];

$importService = new AnzscoImportService();
$totalStats = [
    'total' => 0,
    'inserted' => 0,
    'updated' => 0,
    'skipped' => 0,
    'errors' => 0
];

$allErrors = [];
$allWarnings = [];

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Starting Import Process...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

foreach ($files as $index => $file) {
    $fileNum = $index + 1;
    echo "[$fileNum/4] Importing {$file['name']} list...\n";
    echo "     File: {$file['path']}\n";
    
    // Check if file exists
    if (!file_exists($file['path'])) {
        echo "     ❌ File not found!\n\n";
        continue;
    }
    
    $fileSize = filesize($file['path']);
    echo "     Size: " . number_format($fileSize) . " bytes\n";
    
    // Import the file
    $startTime = microtime(true);
    $result = $importService->import($file['path'], $columnMapping, true); // true = update existing
    $duration = round(microtime(true) - $startTime, 2);
    
    echo "     ⏱️  Duration: {$duration} seconds\n";
    echo "\n";
    
    // Show results
    if ($result['success']) {
        echo "     ✅ Import completed successfully!\n";
    } else {
        echo "     ⚠️  Import completed with errors\n";
    }
    
    echo "     📊 Results:\n";
    echo "        • Total rows:  {$result['stats']['total']}\n";
    echo "        • Inserted:    {$result['stats']['inserted']}\n";
    echo "        • Updated:     {$result['stats']['updated']}\n";
    echo "        • Skipped:     {$result['stats']['skipped']}\n";
    echo "        • Errors:      {$result['stats']['errors']}\n";
    
    // Accumulate totals
    $totalStats['total'] += $result['stats']['total'];
    $totalStats['inserted'] += $result['stats']['inserted'];
    $totalStats['updated'] += $result['stats']['updated'];
    $totalStats['skipped'] += $result['stats']['skipped'];
    $totalStats['errors'] += $result['stats']['errors'];
    
    // Collect errors and warnings
    if (!empty($result['errors'])) {
        $allErrors = array_merge($allErrors, $result['errors']);
        echo "        ⚠️  Errors found (see summary below)\n";
    }
    if (!empty($result['warnings'])) {
        $allWarnings = array_merge($allWarnings, $result['warnings']);
    }
    
    echo "\n";
    echo "────────────────────────────────────────────────────────────────\n\n";
}

// Summary
echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                      IMPORT SUMMARY                            ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "📊 Overall Statistics:\n";
echo "   • Total rows processed:  {$totalStats['total']}\n";
echo "   • New records inserted:  {$totalStats['inserted']}\n";
echo "   • Records updated:       {$totalStats['updated']}\n";
echo "   • Records skipped:       {$totalStats['skipped']}\n";
echo "   • Errors encountered:    {$totalStats['errors']}\n";
echo "\n";

// Count unique occupations
$uniqueCount = DB::table('anzsco_occupations')->count();
echo "✅ Total unique occupations in database: {$uniqueCount}\n\n";

// Count occupations on multiple lists
$multipleListsQuery = "
    SELECT COUNT(*) as count 
    FROM anzsco_occupations 
    WHERE (is_on_mltssl + is_on_stsol + is_on_rol + is_on_csol) > 1
";
$multipleLists = DB::select($multipleListsQuery)[0]->count;
echo "🔄 Occupations on multiple lists: {$multipleLists}\n\n";

// Show list distribution
echo "📋 Distribution by List:\n";
$mltssl = DB::table('anzsco_occupations')->where('is_on_mltssl', true)->count();
$stsol = DB::table('anzsco_occupations')->where('is_on_stsol', true)->count();
$rol = DB::table('anzsco_occupations')->where('is_on_rol', true)->count();
$csol = DB::table('anzsco_occupations')->where('is_on_csol', true)->count();

echo "   • MLTSSL: {$mltssl}\n";
echo "   • STSOL:  {$stsol}\n";
echo "   • ROL:    {$rol}\n";
echo "   • CSOL:   {$csol}\n";
echo "\n";

// Show errors if any
if (!empty($allErrors)) {
    echo "⚠️  ERRORS:\n";
    foreach (array_slice($allErrors, 0, 10) as $error) {
        echo "   • {$error}\n";
    }
    if (count($allErrors) > 10) {
        echo "   ... and " . (count($allErrors) - 10) . " more errors\n";
    }
    echo "\n";
}

// Show some sample overlapping occupations
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 Sample Occupations on Multiple Lists:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$samples = DB::table('anzsco_occupations')
    ->select('anzsco_code', 'occupation_title', 'is_on_mltssl', 'is_on_stsol', 'is_on_rol', 'is_on_csol')
    ->whereRaw('(is_on_mltssl + is_on_stsol + is_on_rol + is_on_csol) > 1')
    ->limit(5)
    ->get();

foreach ($samples as $sample) {
    $lists = [];
    if ($sample->is_on_mltssl) $lists[] = 'MLTSSL';
    if ($sample->is_on_stsol) $lists[] = 'STSOL';
    if ($sample->is_on_rol) $lists[] = 'ROL';
    if ($sample->is_on_csol) $lists[] = 'CSOL';
    
    echo "• {$sample->anzsco_code} - {$sample->occupation_title}\n";
    echo "  Lists: " . implode(', ', $lists) . "\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if ($totalStats['errors'] == 0) {
    echo "✅ Import completed successfully with no errors!\n";
} else {
    echo "⚠️  Import completed with {$totalStats['errors']} errors (see details above)\n";
}

echo "\n🎉 ANZSCO data import finished!\n\n";
echo "Next steps:\n";
echo "  1. Visit /admin/anzsco to view the imported data\n";
echo "  2. Test autocomplete in client forms\n";
echo "  3. Verify occupations show correct list badges\n\n";

