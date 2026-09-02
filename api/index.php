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

// 2. Set serverless environment variables
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('SESSION_DRIVER=array');
putenv('CACHE_STORE=array');
putenv('LOG_CHANNEL=stderr');
putenv('APP_DEBUG=true');

$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['SESSION_DRIVER'] = 'array';
$_ENV['CACHE_STORE'] = 'array';
$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['APP_DEBUG'] = 'true';

$_SERVER['APP_STORAGE'] = '/tmp/storage';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['SESSION_DRIVER'] = 'array';
$_SERVER['CACHE_STORE'] = 'array';
$_SERVER['LOG_CHANNEL'] = 'stderr';
$_SERVER['APP_DEBUG'] = 'true';

if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY'])) {
    $appKey = 'base64:nIETmmyRblG5BQ2BRzwFSvkt3STBSxh6/D1bH2ovjTs=';
    putenv("APP_KEY={$appKey}");
    $_ENV['APP_KEY'] = $appKey;
    $_SERVER['APP_KEY'] = $appKey;
}

// 3. Register global exception handler to output true error message
set_exception_handler(function (\Throwable $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><title>Vercel Error Diagnostic</title><style>body{font-family:sans-serif;background:#0f172a;color:#f8fafc;padding:2rem;}pre{background:#1e293b;padding:1rem;border-radius:0.5rem;overflow-x:auto;color:#38bdf8;}</style></head><body>';
    echo '<h1>⚠️ Serverless Diagnostic Output</h1>';
    echo '<p style="color:#f43f5e;font-weight:bold;font-size:1.2rem;">Message: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
    echo '<h2>Stack Trace:</h2><pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</body></html>';
    exit(1);
});

// 4. Forward to Laravel public/index.php
require __DIR__ . '/../public/index.php';
