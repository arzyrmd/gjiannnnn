<?php

// 1. Prepare writable storage directories in /tmp for Vercel Serverless
$storageDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/testing',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// 2. Set environment fallbacks for Vercel
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

if (!getenv('APP_KEY') && empty($_ENV['APP_KEY'])) {
    $appKey = 'base64:nIETmmyRblG5BQ2BRzwFSvkt3STBSxh6/D1bH2ovjTs=';
    putenv("APP_KEY={$appKey}");
    $_ENV['APP_KEY'] = $appKey;
    $_SERVER['APP_KEY'] = $appKey;
}

if (!getenv('SESSION_DRIVER') && empty($_ENV['SESSION_DRIVER'])) {
    putenv('SESSION_DRIVER=cookie');
    $_ENV['SESSION_DRIVER'] = 'cookie';
}

if (!getenv('CACHE_STORE') && empty($_ENV['CACHE_STORE'])) {
    putenv('CACHE_STORE=array');
    $_ENV['CACHE_STORE'] = 'array';
}

// Enable debug on Vercel to inspect errors if any occur
putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';
$_SERVER['APP_DEBUG'] = 'true';

// 3. Forward request to Laravel entry point with Exception Handler
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><title>Vercel Laravel Diagnostic</title><style>body{font-family:sans-serif;background:#0f172a;color:#f8fafc;padding:2rem;}pre{background:#1e293b;padding:1rem;border-radius:0.5rem;overflow-x:auto;}</style></head><body>';
    echo '<h1>⚠️ Laravel Serverless Diagnostic</h1>';
    echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
    echo '<h2>Stack Trace:</h2><pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</body></html>';
}
