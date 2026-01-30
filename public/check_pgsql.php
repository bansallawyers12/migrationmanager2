<?php
// Load .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Quick check for PostgreSQL extensions
echo "<h2>PostgreSQL Extension Check</h2>";
echo "<p><strong>pdo_pgsql loaded:</strong> " . (extension_loaded('pdo_pgsql') ? 'YES ✓' : 'NO ✗') . "</p>";
echo "<p><strong>pgsql loaded:</strong> " . (extension_loaded('pgsql') ? 'YES ✓' : 'NO ✗') . "</p>";

echo "<h3>Loaded PDO Drivers:</h3>";
echo "<pre>";
print_r(PDO::getAvailableDrivers());
echo "</pre>";

echo "<h3>PHP Configuration File:</h3>";
echo "<p>" . php_ini_loaded_file() . "</p>";

echo "<h3>Test Database Connection:</h3>";
try {
    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port = $_ENV['DB_PORT'] ?? '5432';
    $database = $_ENV['DB_DATABASE'] ?? 'migration_manager_crm';
    $username = $_ENV['DB_USERNAME'] ?? 'postgres';
    $password = $_ENV['DB_PASSWORD'] ?? '123456';
    
    echo "<p>Connecting to: <strong>$database</strong> on <strong>$host:$port</strong> as <strong>$username</strong></p>";
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$database";
    $pdo = new PDO($dsn, $username, $password);
    echo "<p style='color: green;'><strong>✓ Database connection successful!</strong></p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>✗ Database connection failed:</strong> " . $e->getMessage() . "</p>";
}

