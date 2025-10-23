<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

DB::table('clients')->truncate();
DB::table('admin_to_client_mapping')->truncate();

echo "Cleared clients and mapping tables\n";

