<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$columns = DB::select('SHOW COLUMNS FROM admins WHERE Field = "id"');
print_r($columns[0]);

