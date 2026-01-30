<?php
// Clear OpCache Script
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OpCache cleared successfully!";
} else {
    echo "OpCache is not enabled.";
}

// Show opcache status
if (function_exists('opcache_get_status')) {
    echo "\n\nOpCache Status:\n";
    $status = opcache_get_status();
    echo "Enabled: " . ($status ? 'Yes' : 'No') . "\n";
    if ($status) {
        echo "Memory Usage: " . round($status['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB\n";
        echo "Cached Scripts: " . $status['opcache_statistics']['num_cached_scripts'] . "\n";
    }
}

