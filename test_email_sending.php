<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Test email template rendering
    $templateData = [
        'signerName' => 'Test User',
        'documentTitle' => 'Test Document',
        'signingUrl' => 'https://example.com/sign/test',
        'message' => 'Please review and sign the attached document.',
        'documentType' => 'document',
        'dueDate' => null,
    ];
    
    $html = view('emails.signature.send', $templateData)->render();
    echo "SUCCESS: Email template rendered successfully\n";
    echo "Template length: " . strlen($html) . " characters\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
