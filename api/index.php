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
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('APP_MAINTENANCE_STORE=array');
putenv('CACHE_DRIVER=file');
putenv('SESSION_DRIVER=file');
putenv('LOG_CHANNEL=stderr');
putenv('APP_DEBUG=true');

// SQLite database copy for Vercel Serverless
$sqliteDbPath = '/tmp/database.sqlite';
$sourceDb = __DIR__ . '/../database/database.sqlite';
if (file_exists($sourceDb)) {
    if (!file_exists($sqliteDbPath) || @md5_file($sourceDb) !== @md5_file($sqliteDbPath)) {
        @copy($sourceDb, $sqliteDbPath);
        @chmod($sqliteDbPath, 0666);
    }
} else if (!file_exists($sqliteDbPath)) {
    @touch($sqliteDbPath);
    @chmod($sqliteDbPath, 0666);
}

putenv('DB_CONNECTION=sqlite');
putenv("DB_DATABASE={$sqliteDbPath}");

$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/events.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_ENV['APP_MAINTENANCE_STORE'] = 'array';
$_ENV['CACHE_DRIVER'] = 'file';
$_ENV['SESSION_DRIVER'] = 'file';
$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['APP_DEBUG'] = 'true';
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $sqliteDbPath;

$_SERVER['APP_STORAGE'] = '/tmp/storage';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp';
$_SERVER['APP_CONFIG_CACHE'] = '/tmp/config.php';
$_SERVER['APP_EVENTS_CACHE'] = '/tmp/events.php';
$_SERVER['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_SERVER['APP_ROUTES_CACHE'] = '/tmp/routes.php';
$_SERVER['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_SERVER['APP_MAINTENANCE_STORE'] = 'array';
$_SERVER['CACHE_DRIVER'] = 'file';
$_SERVER['SESSION_DRIVER'] = 'file';
$_SERVER['LOG_CHANNEL'] = 'stderr';
$_SERVER['APP_DEBUG'] = 'true';
$_SERVER['DB_CONNECTION'] = 'sqlite';
$_SERVER['DB_DATABASE'] = $sqliteDbPath;

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

// 3. Forward request directly to Laravel entry point
require __DIR__ . '/../public/index.php';
