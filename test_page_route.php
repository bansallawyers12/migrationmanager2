<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Create a request to test the route
$request = Illuminate\Http\Request::create(
    '/documents/25/page/1',
    'GET'
);

try {
    $response = $kernel->handle($request);
    echo "Status Code: " . $response->getStatusCode() . PHP_EOL;
    echo "Content Type: " . $response->headers->get('Content-Type') . PHP_EOL;
    echo "Content Length: " . strlen($response->getContent()) . " bytes" . PHP_EOL;
    
    if ($response->getStatusCode() !== 200) {
        echo "Response Content: " . substr($response->getContent(), 0, 500) . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}

$kernel->terminate($request, $response ?? null);
