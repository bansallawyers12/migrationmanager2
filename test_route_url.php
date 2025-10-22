<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test the route URL generation
$url = route('public.documents.page', ['id' => 25, 'page' => 1]);
echo "Generated URL: " . $url . PHP_EOL;

// Test if the route exists
try {
    $routes = app('router')->getRoutes();
    $route = $routes->getByName('public.documents.page');
    if ($route) {
        echo "Route exists: YES" . PHP_EOL;
        echo "Route URI: " . $route->uri() . PHP_EOL;
        echo "Route Methods: " . implode(', ', $route->methods()) . PHP_EOL;
    } else {
        echo "Route exists: NO" . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "Error checking route: " . $e->getMessage() . PHP_EOL;
}
