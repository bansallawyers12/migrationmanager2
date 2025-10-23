<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

if (Schema::hasTable('clients')) {
    Schema::dropIfExists('clients');
    echo "Clients table dropped successfully\n";
} else {
    echo "Clients table doesn't exist\n";
}

