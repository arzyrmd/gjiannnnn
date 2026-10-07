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

// Intercept /migrate request directly on Vercel Serverless
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if ($requestUri === '/migrate') {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        echo '<div style="background:#0f172a;color:#38bdf8;padding:24px;font-family:sans-serif;border-radius:12px;max-width:800px;margin:40px auto;box-shadow:0 10px 25px rgba(0,0,0,0.5);">'
            . '<h2 style="color:#10b981;margin-top:0;">✅ Database Migration Executed Successfully</h2>'
            . '<pre style="background:#1e293b;color:#f8fafc;padding:16px;border-radius:8px;overflow-x:auto;">' . htmlspecialchars($output ?: 'Nothing to migrate or migration completed cleanly.') . '</pre>'
            . '<a href="/" style="display:inline-block;margin-top:16px;background:#3b82f6;color:white;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:bold;">Return to Dashboard &rarr;</a>'
            . '</div>';
    } catch (\Throwable $e) {
        echo '<div style="background:#0f172a;color:#f87171;padding:24px;font-family:sans-serif;border-radius:12px;max-width:800px;margin:40px auto;box-shadow:0 10px 25px rgba(0,0,0,0.5);">'
            . '<h2 style="color:#ef4444;margin-top:0;">❌ Migration Failed</h2>'
            . '<pre style="background:#1e293b;color:#fca5a5;padding:16px;border-radius:8px;overflow-x:auto;">' . htmlspecialchars($e->getMessage()) . '</pre>'
            . '<a href="/" style="display:inline-block;margin-top:16px;background:#64748b;color:white;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:bold;">Return to Dashboard &rarr;</a>'
            . '</div>';
    }
    exit;
}

// 3. Forward request directly to Laravel entry point
require __DIR__ . '/../public/index.php';
