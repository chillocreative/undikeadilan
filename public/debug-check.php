<?php
// Standalone diagnostic - no Laravel needed
// Visit: https://undi.keadilankb.com/debug-check.php

echo "<h2>Server Diagnostic</h2><pre>";

echo "PHP Version: " . phpversion() . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n\n";

// Check .env
$envPath = __DIR__ . '/../.env';
echo ".env exists: " . (file_exists($envPath) ? 'YES' : 'NO - MUST CREATE THIS FILE') . "\n";

// Check .env.example
$envExamplePath = __DIR__ . '/../.env.example';
echo ".env.example exists: " . (file_exists($envExamplePath) ? 'YES' : 'NO') . "\n";

// Check vendor
$vendorPath = __DIR__ . '/../vendor/autoload.php';
echo "vendor/autoload.php exists: " . (file_exists($vendorPath) ? 'YES' : 'NO - VENDOR MISSING') . "\n\n";

// Check storage writable
$storagePath = __DIR__ . '/../storage';
echo "storage/ writable: " . (is_writable($storagePath) ? 'YES' : 'NO - RUN: chmod -R 775 storage') . "\n";

$bootstrapCachePath = __DIR__ . '/../bootstrap/cache';
echo "bootstrap/cache/ writable: " . (is_writable($bootstrapCachePath) ? 'YES' : 'NO - RUN: chmod -R 775 bootstrap/cache') . "\n\n";

// Check required PHP extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
echo "PHP Extensions:\n";
foreach ($requiredExtensions as $ext) {
    echo "  $ext: " . (extension_loaded($ext) ? 'OK' : 'MISSING') . "\n";
}

// If .env missing, offer to create it
if (!file_exists($envPath) && file_exists($envExamplePath)) {
    if (isset($_GET['create_env'])) {
        copy($envExamplePath, $envPath);
        echo "\n.env CREATED from .env.example! Refresh the page.\n";
    } else {
        echo "\nTo auto-create .env from .env.example, visit:\n";
        echo $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '&' : '?') . "create_env=1\n";
    }
}

echo "</pre>";
