<?php
/**
 * Diagnostic Script for Visa Agreement Generation Issues
 * 
 * This script checks all prerequisites for visa agreement generation
 * for a specific client and matter.
 * 
 * Usage: php artisan tinker
 * Then: include 'diagnose_visa_agreement_issue.php';
 * Or access via browser: /diagnose_visa_agreement_issue.php?client_ref=JSxTQFQsQyRgCmAK&matter_ref=General_1
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get parameters
$clientRef = $_GET['client_ref'] ?? 'JSxTQFQsQyRgCmAK';
$matterRef = $_GET['matter_ref'] ?? 'General_1';

echo "<h1>Visa Agreement Generation Diagnostic</h1>";
echo "<h2>Client Reference: {$clientRef}</h2>";
echo "<h2>Matter Reference: {$matterRef}</h2>";
echo "<hr>";

// Step 1: Check if client exists
echo "<h3>Step 1: Checking Client...</h3>";
$client = \App\Models\Admin::where('client_id', $clientRef)->first();

if (!$client) {
    echo "❌ <strong>ERROR:</strong> Client with reference '{$clientRef}' not found in database.<br>";
    echo "This is the root cause. The client either doesn't exist or the reference is incorrect.<br>";
    exit;
}

echo "✅ Client found<br>";
echo "- ID: {$client->id}<br>";
echo "- Name: {$client->first_name} {$client->last_name}<br>";
echo "- Email: {$client->email}<br>";
echo "- Phone: {$client->phone}<br>";
echo "<br>";

// Step 2: Check if matter exists
echo "<h3>Step 2: Checking Matter...</h3>";
$matter = \App\Models\ClientMatter::where('client_id', $client->id)
    ->where('client_unique_matter_no', $matterRef)
    ->first();

if (!$matter) {
    echo "❌ <strong>ERROR:</strong> Matter '{$matterRef}' not found for this client.<br>";
    echo "Available matters for this client:<br>";
    
    $allMatters = \App\Models\ClientMatter::where('client_id', $client->id)->get();
    
    if ($allMatters->isEmpty()) {
        echo "⚠️ <strong>No matters found!</strong> This is likely the issue.<br>";
        echo "<strong>Solution:</strong> Create a matter for this client first before generating agreement.<br>";
    } else {
        echo "<ul>";
        foreach ($allMatters as $m) {
            echo "<li>Matter ID: {$m->id} | Reference: {$m->client_unique_matter_no} | Title: {$m->title} | Status: " . ($m->matter_status == 1 ? 'Active' : 'Inactive') . "</li>";
        }
        echo "</ul>";
        echo "<strong>Note:</strong> The matter reference in the URL ('{$matterRef}') doesn't match any existing matter.<br>";
    }
    exit;
}

echo "✅ Matter found<br>";
echo "- Matter ID: {$matter->id}<br>";
echo "- Reference: {$matter->client_unique_matter_no}<br>";
echo "- Title: {$matter->title}<br>";
echo "- Status: " . ($matter->matter_status == 1 ? 'Active' : 'Inactive') . "<br>";
echo "- Matter Type ID: {$matter->sel_matter_id}<br>";
echo "<br>";

// Step 3: Check if agent is assigned
echo "<h3>Step 3: Checking Assigned Agent...</h3>";
if (!$matter->agent_id) {
    echo "❌ <strong>ERROR:</strong> No agent assigned to this matter.<br>";
    echo "<strong>Solution:</strong> Assign a migration agent to this matter before generating agreement.<br>";
} else {
    $agent = \App\Models\Admin::find($matter->agent_id);
    if (!$agent) {
        echo "❌ <strong>ERROR:</strong> Agent ID {$matter->agent_id} assigned but agent not found in database.<br>";
    } else {
        echo "✅ Agent found<br>";
        echo "- Agent ID: {$agent->id}<br>";
        echo "- Name: {$agent->first_name} {$agent->last_name}<br>";
        echo "- Company: {$agent->company_name}<br>";
        echo "- MARN: {$agent->marn_number}<br>";
    }
}
echo "<br>";

// Step 4: Check cost assignment
echo "<h3>Step 4: Checking Cost Assignment...</h3>";
$costAssignment = \App\Models\CostAssignmentForm::where('client_id', $client->id)
    ->where('client_matter_id', $matter->id)
    ->first();

if (!$costAssignment) {
    echo "❌ <strong>ERROR:</strong> No cost assignment found for this client and matter.<br>";
    echo "<strong>Solution:</strong> Create a cost assignment form before generating the visa agreement.<br>";
    echo "Go to: Form Generation > Create Cost Assignment<br>";
} else {
    echo "✅ Cost assignment found<br>";
    echo "- ID: {$costAssignment->id}<br>";
    echo "- Block 1 Fee: \${$costAssignment->Block_1_Ex_Tax}<br>";
    echo "- Block 2 Fee: \${$costAssignment->Block_2_Ex_Tax}<br>";
    echo "- Block 3 Fee: \${$costAssignment->Block_3_Ex_Tax}<br>";
    echo "- Dept Base Charge: \${$costAssignment->Dept_Base_Application_Charge}<br>";
}
echo "<br>";

// Step 5: Check matter type and template
echo "<h3>Step 5: Checking Template File...</h3>";
$matterType = \App\Models\Matter::find($matter->sel_matter_id);
$matterCode = $matterType ? $matterType->matter_code : 'Unknown';

$templateFile = 'agreement_template.docx'; // Default
if (in_array($matterCode, ['ART', 'KART', 'NART'])) {
    $templateFile = 'agreement_template-ART.docx';
} elseif ($matterCode === 'SA') {
    $templateFile = 'agreement_template-skillassment.docx';
} elseif ($matterCode === 'JRP') {
    $templateFile = 'agreement_template-JRP.docx';
}

$templatePath = storage_path('app/templates/' . $templateFile);
echo "- Matter Type: {$matterCode}<br>";
echo "- Template File: {$templateFile}<br>";
echo "- Template Path: {$templatePath}<br>";

if (!file_exists($templatePath)) {
    echo "❌ <strong>ERROR:</strong> Template file not found!<br>";
    echo "<strong>Solution:</strong> Upload the template file to storage/app/templates/<br>";
} else {
    echo "✅ Template file exists<br>";
    echo "- File size: " . number_format(filesize($templatePath) / 1024, 2) . " KB<br>";
    echo "- Readable: " . (is_readable($templatePath) ? 'Yes' : 'No') . "<br>";
}
echo "<br>";

// Step 6: Check output directory
echo "<h3>Step 6: Checking Output Directory...</h3>";
$outputDir = storage_path('app/public/agreements');
echo "- Directory: {$outputDir}<br>";

if (!file_exists($outputDir)) {
    echo "⚠️ Output directory doesn't exist (will be created automatically)<br>";
} else {
    echo "✅ Directory exists<br>";
    echo "- Writable: " . (is_writable($outputDir) ? 'Yes' : 'No') . "<br>";
}
echo "<br>";

// Step 7: Check PHPWord library
echo "<h3>Step 7: Checking PHPWord Library...</h3>";
if (!class_exists('\PhpOffice\PhpWord\TemplateProcessor')) {
    echo "❌ <strong>ERROR:</strong> PHPWord library not installed or not autoloaded.<br>";
    echo "<strong>Solution:</strong> Run 'composer require phpoffice/phpword'<br>";
} else {
    echo "✅ PHPWord library available<br>";
}
echo "<br>";

// Summary
echo "<h2>SUMMARY</h2>";
echo "<hr>";

$issues = [];

if (!$client) {
    $issues[] = "Client not found";
}
if (!$matter) {
    $issues[] = "Matter not found or doesn't match reference";
}
if ($matter && !$matter->agent_id) {
    $issues[] = "No agent assigned to matter";
}
if (!$costAssignment) {
    $issues[] = "Cost assignment not created";
}
if (!file_exists($templatePath)) {
    $issues[] = "Template file missing";
}
if (file_exists($outputDir) && !is_writable($outputDir)) {
    $issues[] = "Output directory not writable";
}

if (empty($issues)) {
    echo "<div style='color: green; font-size: 18px;'>";
    echo "✅ <strong>All checks passed!</strong><br>";
    echo "The visa agreement should generate successfully. If you're still seeing an error, check:<br>";
    echo "<ul>";
    echo "<li>Browser console for JavaScript errors</li>";
    echo "<li>Laravel logs at storage/logs/laravel-" . date('Y-m-d') . ".log</li>";
    echo "<li>Network tab to see the actual error response from the server</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='color: red; font-size: 18px;'>";
    echo "❌ <strong>Issues Found:</strong><br>";
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li>{$issue}</li>";
    }
    echo "</ul>";
    echo "<br>";
    echo "<strong>Please fix the above issues before attempting to generate the visa agreement.</strong>";
    echo "</div>";
}

echo "<br><br>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Fix any issues listed above</li>";
echo "<li>Refresh the client detail page</li>";
echo "<li>Ensure a matter is selected in the dropdown</li>";
echo "<li>Click 'Create Visa Agreement' button</li>";
echo "</ol>";
