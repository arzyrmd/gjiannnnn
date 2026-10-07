<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

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

// 2. Set environment variables for Vercel
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp');
putenv('APP_MAINTENANCE_STORE=array');
putenv('CACHE_DRIVER=file');
putenv('SESSION_DRIVER=file');
putenv('LOG_CHANNEL=stderr');
putenv('APP_DEBUG=true');

$dbConn = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'sqlite');

if ($dbConn === 'sqlite') {
    // SQLite database copy for Vercel Serverless
    $sqliteDbPath = '/tmp/database.sqlite';
    $sourceDb = __DIR__ . '/../database/database.sqlite';
    if (file_exists($sourceDb)) {
        if (!file_exists($sqliteDbPath) || @filesize($sqliteDbPath) !== @filesize($sourceDb) || @md5_file($sourceDb) !== @md5_file($sqliteDbPath)) {
            @unlink($sqliteDbPath);
            @copy($sourceDb, $sqliteDbPath);
            @chmod($sqliteDbPath, 0666);
        }
    } else if (!file_exists($sqliteDbPath)) {
        @touch($sqliteDbPath);
        @chmod($sqliteDbPath, 0666);
    }

    putenv('DB_CONNECTION=sqlite');
    putenv("DB_DATABASE={$sqliteDbPath}");
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $sqliteDbPath;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $sqliteDbPath;
}

$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp';
$_ENV['APP_MAINTENANCE_STORE'] = 'array';
$_ENV['CACHE_DRIVER'] = 'file';
$_ENV['SESSION_DRIVER'] = 'file';
$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['APP_DEBUG'] = 'true';

$_SERVER['APP_STORAGE'] = '/tmp/storage';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp';
$_SERVER['APP_MAINTENANCE_STORE'] = 'array';
$_SERVER['CACHE_DRIVER'] = 'file';
$_SERVER['SESSION_DRIVER'] = 'file';
$_SERVER['LOG_CHANNEL'] = 'stderr';
$_SERVER['APP_DEBUG'] = 'true';

// Bind dynamic HTTPS APP_URL for Vercel redirects
if (isset($_SERVER['HTTP_HOST'])) {
    $appUrl = 'https://' . $_SERVER['HTTP_HOST'];
    putenv("APP_URL={$appUrl}");
    $_ENV['APP_URL'] = $appUrl;
    $_SERVER['APP_URL'] = $appUrl;
}

if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY'])) {
    $appKey = 'base64:nIETmmyRblG5BQ2BRzwFSvkt3STBSxh6/D1bH2ovjTs=';
    putenv("APP_KEY={$appKey}");
    $_ENV['APP_KEY'] = $appKey;
    $_SERVER['APP_KEY'] = $appKey;
}

// 3. Forward request directly to Laravel entry point with exception handling
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(200);
    echo '<div style="background:#0f172a;color:#f87171;padding:24px;font-family:sans-serif;border-radius:12px;max-width:900px;margin:40px auto;box-shadow:0 10px 30px rgba(0,0,0,0.5);">';
    echo '<h2 style="color:#ef4444;margin-top:0;">⚠️ Vercel Application Error</h2>';
    echo '<p style="color:#f8fafc;font-size:16px;"><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p style="color:#cbd5e1;font-size:14px;"><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ' : Line ' . $e->getLine() . '</p>';
    echo '<h3 style="color:#38bdf8;margin-bottom:8px;">Stack Trace:</h3>';
    echo '<pre style="background:#1e293b;color:#cbd5e1;padding:16px;border-radius:8px;overflow-x:auto;font-size:12px;max-height:400px;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}
