<?php
/**
 * Script to Remove Duplicate Note Methods from ClientsController
 * 
 * This script safely removes the 10 note-related methods that were
 * migrated to ClientNotesController but still exist in ClientsController.
 * 
 * Lines to remove: 4791-6451
 */

$controllerFile = __DIR__ . '/app/Http/Controllers/Admin/ClientsController.php';
$backupFile = __DIR__ . '/app/Http/Controllers/Admin/ClientsController.php.backup';

echo "🔍 ClientsController Duplicate Methods Removal Script\n";
echo str_repeat("=", 60) . "\n\n";

// Check if file exists
if (!file_exists($controllerFile)) {
    die("❌ Error: ClientsController.php not found at: $controllerFile\n");
}

echo "📂 Reading ClientsController.php...\n";
$lines = file($controllerFile);
$totalLines = count($lines);
echo "✅ Found $totalLines lines\n\n";

// Create backup
echo "💾 Creating backup...\n";
copy($controllerFile, $backupFile);
echo "✅ Backup created: ClientsController.php.backup\n\n";

// Lines to remove (0-indexed, so subtract 1)
$startLine = 4790; // Line 4791 in editor (0-indexed)
$endLine = 6450;   // Line 6451 in editor (0-indexed)
$linesToRemove = $endLine - $startLine + 1;

echo "🗑️  Removing lines " . ($startLine + 1) . " to " . ($endLine + 1) . " ($linesToRemove lines)...\n";
echo "   Methods being removed:\n";
echo "   - createnote()\n";
echo "   - updateNoteDatetime()\n";
echo "   - getnotedetail()\n";
echo "   - viewnotedetail()\n";
echo "   - viewapplicationnote()\n";
echo "   - getnotes()\n";
echo "   - deletenote()\n";
echo "   - pinnote()\n";
echo "   - saveprevvisa()\n";
echo "   - saveonlineform()\n\n";

// Verify we're removing the right section
$checkLine = trim($lines[$startLine]);
if (strpos($checkLine, '//Save create and update note') === false) {
    echo "⚠️  WARNING: Line " . ($startLine + 1) . " doesn't match expected pattern!\n";
    echo "   Expected: //Save create and update note\n";
    echo "   Found: $checkLine\n";
    echo "\n❌ Aborting for safety. Please check the file manually.\n";
    exit(1);
}

// Remove the lines
$newLines = array_merge(
    array_slice($lines, 0, $startLine),
    array_slice($lines, $endLine + 1)
);

// Write back to file
echo "💾 Writing updated file...\n";
file_put_contents($controllerFile, implode('', $newLines));

$newTotalLines = count($newLines);
$removed = $totalLines - $newTotalLines;

echo "✅ File updated successfully!\n\n";
echo "📊 Summary:\n";
echo "   Original lines: $totalLines\n";
echo "   New lines: $newTotalLines\n";
echo "   Lines removed: $removed\n\n";

// Verify the methods are gone
echo "🔍 Verifying removal...\n";
$content = file_get_contents($controllerFile);

$methodsToCheck = [
    'public function createnote(',
    'public function updateNoteDatetime(',
    'public function getnotedetail(',
    'public function viewnotedetail(',
    'public function viewapplicationnote(',
    'public function getnotes(',
    'public function deletenote(',
    'public function pinnote(',
    'public function saveprevvisa(',
    'public function saveonlineform('
];

$stillPresent = [];
foreach ($methodsToCheck as $method) {
    if (strpos($content, $method) !== false) {
        $stillPresent[] = $method;
    }
}

if (empty($stillPresent)) {
    echo "✅ All methods successfully removed!\n\n";
    echo "🎉 SUCCESS! ClientsController is now clean.\n\n";
    echo "📝 Next steps:\n";
    echo "   1. Review the changes in ClientsController.php\n";
    echo "   2. Run: php artisan route:clear\n";
    echo "   3. Run: php artisan route:cache\n";
    echo "   4. Run: php artisan config:clear\n";
    echo "   5. Test all note functionality\n\n";
    echo "💡 Backup saved at: ClientsController.php.backup\n";
    echo "   (You can delete this after confirming everything works)\n";
} else {
    echo "⚠️  WARNING: Some methods still found in file:\n";
    foreach ($stillPresent as $method) {
        echo "   - $method\n";
    }
    echo "\n❌ Please check the file manually.\n";
    echo "   Backup is at: ClientsController.php.backup\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ Script completed.\n";

