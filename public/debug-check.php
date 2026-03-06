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

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    // Show key .env values (mask sensitive data)
    preg_match('/APP_KEY=(.*)/', $envContent, $m);
    echo "APP_KEY: " . (empty(trim($m[1] ?? '')) ? 'EMPTY - NEEDS GENERATING' : 'SET (' . strlen(trim($m[1])) . ' chars)') . "\n";
    preg_match('/APP_ENV=(.*)/', $envContent, $m);
    echo "APP_ENV: " . trim($m[1] ?? 'not set') . "\n";
    preg_match('/APP_DEBUG=(.*)/', $envContent, $m);
    echo "APP_DEBUG: " . trim($m[1] ?? 'not set') . "\n";
    preg_match('/DB_DATABASE=(.*)/', $envContent, $m);
    echo "DB_DATABASE: " . trim($m[1] ?? 'not set') . "\n";
    preg_match('/DB_USERNAME=(.*)/', $envContent, $m);
    echo "DB_USERNAME: " . trim($m[1] ?? 'not set') . "\n";
}

// Check .env.example
$envExamplePath = __DIR__ . '/../.env.example';
echo ".env.example exists: " . (file_exists($envExamplePath) ? 'YES' : 'NO') . "\n";

// Check vendor
$vendorPath = __DIR__ . '/../vendor/autoload.php';
echo "vendor/autoload.php exists: " . (file_exists($vendorPath) ? 'YES' : 'NO - VENDOR MISSING') . "\n\n";

// Check storage subdirectories
$storageDirs = [
    'storage' => __DIR__ . '/../storage',
    'storage/framework' => __DIR__ . '/../storage/framework',
    'storage/framework/cache' => __DIR__ . '/../storage/framework/cache',
    'storage/framework/sessions' => __DIR__ . '/../storage/framework/sessions',
    'storage/framework/views' => __DIR__ . '/../storage/framework/views',
    'storage/logs' => __DIR__ . '/../storage/logs',
    'bootstrap/cache' => __DIR__ . '/../bootstrap/cache',
];
echo "Directory Permissions:\n";
foreach ($storageDirs as $name => $path) {
    if (!file_exists($path)) {
        echo "  $name: MISSING\n";
    } else {
        echo "  $name: " . (is_writable($path) ? 'writable' : 'NOT WRITABLE') . " (" . substr(sprintf('%o', fileperms($path)), -4) . ")\n";
    }
}

// Check required PHP extensions
echo "\nPHP Extensions:\n";
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
foreach ($requiredExtensions as $ext) {
    echo "  $ext: " . (extension_loaded($ext) ? 'OK' : 'MISSING') . "\n";
}

// Try to boot Laravel and handle a request to see the real error
echo "\n--- Laravel Boot Test ---\n";
try {
    // Force debug mode to see real errors
    putenv('APP_DEBUG=true');
    $_ENV['APP_DEBUG'] = 'true';
    $_SERVER['APP_DEBUG'] = 'true';

    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    // Handle a request to the homepage to trigger the real error
    $request = Illuminate\Http\Request::create('/', 'GET');
    $response = $kernel->handle($request);

    echo "Laravel response status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() >= 400) {
        // Show the error content
        $content = $response->getContent();
        // Strip HTML tags for readability, keep the error message
        $text = strip_tags($content);
        // Find the relevant error portion
        $text = preg_replace('/\s+/', ' ', $text);
        echo "Error: " . substr($text, 0, 2000) . "\n";
    } else {
        echo "Homepage loaded OK!\n";
    }

    // Test database connection
    try {
        $pdo = $app->make('db')->connection()->getPdo();
        echo "Database connection: OK\n";
    } catch (Exception $e) {
        echo "Database connection FAILED: " . $e->getMessage() . "\n";
    }

    $kernel->terminate($request, $response);
} catch (Throwable $e) {
    echo "Laravel FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nTrace (last 5):\n";
    $trace = $e->getTrace();
    foreach (array_slice($trace, 0, 5) as $i => $t) {
        echo "  #$i " . ($t['file'] ?? '?') . ':' . ($t['line'] ?? '?') . ' ' . ($t['class'] ?? '') . ($t['type'] ?? '') . ($t['function'] ?? '') . "()\n";
    }
}

echo "</pre>";
