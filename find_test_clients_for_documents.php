<?php
/**
 * Find Test Clients for Document Download/Preview Testing
 * 
 * Run this script to get client references with documents to test the fix
 * Usage: php find_test_clients_for_documents.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Document;
use App\Models\Admin;

echo "\n============================================\n";
echo "FINDING TEST CLIENTS FOR DOCUMENT TESTING\n";
echo "============================================\n\n";

// Find clients with personal documents
echo "📁 PERSONAL DOCUMENTS:\n";
echo str_repeat("-", 80) . "\n";

$personalDocs = Document::select('client_id', 'doc_type', 'myfile', 'myfile_key', 'file_name', 'id')
    ->where('doc_type', 'personal')
    ->whereNotNull('file_name')
    ->whereNull('not_used_doc')
    ->where('type', 'client')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

if ($personalDocs->count() > 0) {
    $clientsShown = [];
    foreach ($personalDocs as $doc) {
        if (in_array($doc->client_id, $clientsShown)) {
            continue; // Skip duplicate clients
        }
        
        $client = Admin::find($doc->client_id);
        if (!$client) continue;
        
        $clientsShown[] = $doc->client_id;
        
        // Check if legacy or modern
        $isLegacy = empty($doc->myfile_key) ? '🔴 LEGACY' : '🟢 MODERN';
        $hasFullUrl = (strpos($doc->myfile, 'http') === 0) ? 'Full URL' : 'Relative Path';
        
        echo sprintf("%-15s | %-30s | %-12s | %s\n", 
            $isLegacy,
            substr($client->first_name . ' ' . $client->last_name, 0, 28),
            "ID: " . $doc->client_id,
            $hasFullUrl
        );
        echo "   File: " . substr($doc->file_name, 0, 50) . "\n";
        echo "   URL: /admin/clients/detail/{$doc->client_id}//personaldocuments\n";
        echo "\n";
        
        if (count($clientsShown) >= 5) break;
    }
} else {
    echo "   ⚠️  No personal documents found\n\n";
}

// Find clients with visa documents
echo "\n📁 VISA DOCUMENTS:\n";
echo str_repeat("-", 80) . "\n";

$visaDocs = Document::select('client_id', 'doc_type', 'myfile', 'myfile_key', 'file_name', 'id', 'client_matter_id')
    ->where('doc_type', 'visa')
    ->whereNotNull('file_name')
    ->whereNull('not_used_doc')
    ->where('type', 'client')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

if ($visaDocs->count() > 0) {
    $clientsShown = [];
    foreach ($visaDocs as $doc) {
        if (in_array($doc->client_id, $clientsShown)) {
            continue; // Skip duplicate clients
        }
        
        $client = Admin::find($doc->client_id);
        if (!$client) continue;
        
        $clientsShown[] = $doc->client_id;
        
        // Check if legacy or modern
        $isLegacy = empty($doc->myfile_key) ? '🔴 LEGACY' : '🟢 MODERN';
        $hasFullUrl = (strpos($doc->myfile, 'http') === 0) ? 'Full URL' : 'Relative Path';
        
        // Get matter reference if available
        $matterRef = '';
        if ($doc->client_matter_id) {
            $matter = \App\Models\ClientMatter::find($doc->client_matter_id);
            if ($matter) {
                $matterRef = $matter->client_unique_matter_no;
            }
        }
        
        echo sprintf("%-15s | %-30s | %-12s | %s\n", 
            $isLegacy,
            substr($client->first_name . ' ' . $client->last_name, 0, 28),
            "ID: " . $doc->client_id,
            $hasFullUrl
        );
        echo "   File: " . substr($doc->file_name, 0, 50) . "\n";
        
        if ($matterRef) {
            echo "   URL: /admin/clients/detail/{$doc->client_id}/{$matterRef}/visadocuments\n";
        } else {
            echo "   URL: /admin/clients/detail/{$doc->client_id}//visadocuments\n";
        }
        echo "\n";
        
        if (count($clientsShown) >= 5) break;
    }
} else {
    echo "   ⚠️  No visa documents found\n\n";
}

// Summary statistics
echo "\n📊 SUMMARY:\n";
echo str_repeat("-", 80) . "\n";

$totalPersonal = Document::where('doc_type', 'personal')
    ->whereNotNull('file_name')
    ->whereNull('not_used_doc')
    ->where('type', 'client')
    ->count();

$legacyPersonal = Document::where('doc_type', 'personal')
    ->whereNotNull('file_name')
    ->whereNull('not_used_doc')
    ->where('type', 'client')
    ->whereNull('myfile_key')
    ->count();

$totalVisa = Document::where('doc_type', 'visa')
    ->whereNotNull('file_name')
    ->whereNull('not_used_doc')
    ->where('type', 'client')
    ->count();

$legacyVisa = Document::where('doc_type', 'visa')
    ->whereNotNull('file_name')
    ->whereNull('not_used_doc')
    ->where('type', 'client')
    ->whereNull('myfile_key')
    ->count();

echo "Personal Documents:\n";
echo "  - Total: {$totalPersonal}\n";
echo "  - Legacy (myfile_key is NULL): {$legacyPersonal}\n";
echo "  - Modern: " . ($totalPersonal - $legacyPersonal) . "\n\n";

echo "Visa Documents:\n";
echo "  - Total: {$totalVisa}\n";
echo "  - Legacy (myfile_key is NULL): {$legacyVisa}\n";
echo "  - Modern: " . ($totalVisa - $legacyVisa) . "\n\n";

echo "\n✅ TESTING INSTRUCTIONS:\n";
echo str_repeat("-", 80) . "\n";
echo "1. Copy one of the URLs above and paste it in your browser\n";
echo "2. Navigate to the appropriate tab (Personal Documents or Visa Documents)\n";
echo "3. Right-click on a document file\n";
echo "4. Test these options:\n";
echo "   - Preview (should open in new tab)\n";
echo "   - Download (should download file)\n";
echo "5. Check browser console (F12) for any errors\n";
echo "6. Test BOTH legacy (🔴) and modern (🟢) documents if available\n\n";

echo "🟢 MODERN = Has myfile_key (should work with or without fix)\n";
echo "🔴 LEGACY = No myfile_key (will ONLY work after the fix)\n\n";

echo "============================================\n\n";

