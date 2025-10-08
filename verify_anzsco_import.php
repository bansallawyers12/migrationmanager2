<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║            ANZSCO Import Verification                         ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Test Chef (351311) - Should be on MLTSSL and CSOL
echo "🔍 Verifying MERGE Logic - Chef (351311):\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$chef = DB::table('anzsco_occupations')->where('anzsco_code', '351311')->first();
if ($chef) {
    echo "Occupation: {$chef->occupation_title}\n";
    echo "MLTSSL: " . ($chef->is_on_mltssl ? '✅ YES' : '❌ NO') . "\n";
    echo "STSOL:  " . ($chef->is_on_stsol ? '✅ YES' : '❌ NO') . "\n";
    echo "ROL:    " . ($chef->is_on_rol ? '✅ YES' : '❌ NO') . "\n";
    echo "CSOL:   " . ($chef->is_on_csol ? '✅ YES' : '❌ NO') . "\n";
    
    $chefLists = [];
    if ($chef->is_on_mltssl) $chefLists[] = 'MLTSSL';
    if ($chef->is_on_stsol) $chefLists[] = 'STSOL';
    if ($chef->is_on_rol) $chefLists[] = 'ROL';
    if ($chef->is_on_csol) $chefLists[] = 'CSOL';
    
    echo "\n✅ RESULT: Chef is on " . count($chefLists) . " list(s): " . implode(', ', $chefLists) . "\n";
} else {
    echo "❌ Chef not found in database\n";
}

echo "\n";

// Test Software Engineer (261313) - Should be on MLTSSL and CSOL
echo "🔍 Verifying MERGE Logic - Software Engineer (261313):\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$softwareEngineer = DB::table('anzsco_occupations')->where('anzsco_code', '261313')->first();
if ($softwareEngineer) {
    echo "Occupation: {$softwareEngineer->occupation_title}\n";
    echo "MLTSSL: " . ($softwareEngineer->is_on_mltssl ? '✅ YES' : '❌ NO') . "\n";
    echo "STSOL:  " . ($softwareEngineer->is_on_stsol ? '✅ YES' : '❌ NO') . "\n";
    echo "ROL:    " . ($softwareEngineer->is_on_rol ? '✅ YES' : '❌ NO') . "\n";
    echo "CSOL:   " . ($softwareEngineer->is_on_csol ? '✅ YES' : '❌ NO') . "\n";
    
    $seLists = [];
    if ($softwareEngineer->is_on_mltssl) $seLists[] = 'MLTSSL';
    if ($softwareEngineer->is_on_stsol) $seLists[] = 'STSOL';
    if ($softwareEngineer->is_on_rol) $seLists[] = 'ROL';
    if ($softwareEngineer->is_on_csol) $seLists[] = 'CSOL';
    
    echo "\n✅ RESULT: Software Engineer is on " . count($seLists) . " list(s): " . implode(', ', $seLists) . "\n";
} else {
    echo "❌ Software Engineer not found in database\n";
}

echo "\n";

// Show top 10 occupations with most lists
echo "🏆 Top 10 Occupations on Most Lists:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$topOccupations = DB::select("
    SELECT 
        anzsco_code,
        occupation_title,
        is_on_mltssl,
        is_on_stsol,
        is_on_rol,
        is_on_csol,
        (is_on_mltssl + is_on_stsol + is_on_rol + is_on_csol) as total_lists
    FROM anzsco_occupations
    WHERE (is_on_mltssl + is_on_stsol + is_on_rol + is_on_csol) > 1
    ORDER BY total_lists DESC, occupation_title ASC
    LIMIT 10
");

foreach ($topOccupations as $index => $occ) {
    $lists = [];
    if ($occ->is_on_mltssl) $lists[] = 'MLTSSL';
    if ($occ->is_on_stsol) $lists[] = 'STSOL';
    if ($occ->is_on_rol) $lists[] = 'ROL';
    if ($occ->is_on_csol) $lists[] = 'CSOL';
    
    echo sprintf("%2d. %s - %s\n", $index + 1, $occ->anzsco_code, $occ->occupation_title);
    echo "    On {$occ->total_lists} lists: " . implode(', ', $lists) . "\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Final stats
$total = DB::table('anzsco_occupations')->count();
$onMultipleLists = DB::select("
    SELECT COUNT(*) as count 
    FROM anzsco_occupations 
    WHERE (is_on_mltssl + is_on_stsol + is_on_rol + is_on_csol) > 1
")[0]->count;

$onOnlyOne = $total - $onMultipleLists;
$percentage = round(($onMultipleLists / $total) * 100, 1);

echo "📊 FINAL VERIFICATION:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Total Occupations:              {$total}\n";
echo "On Multiple Lists:              {$onMultipleLists} ({$percentage}%)\n";
echo "On Single List Only:            {$onOnlyOne}\n";
echo "\n";

if ($onMultipleLists > 0) {
    echo "✅ MERGE LOGIC WORKING PERFECTLY!\n";
    echo "   {$onMultipleLists} occupations successfully retained flags from multiple lists.\n";
} else {
    echo "⚠️  WARNING: No occupations found on multiple lists!\n";
    echo "   This suggests the merge logic may not be working.\n";
}

echo "\n";

