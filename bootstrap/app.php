<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// 1. Prepare writable storage directories in /tmp for Vercel Serverless
$storageDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];
foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, Request $request) {
            return response()->make(
                '<!DOCTYPE html><html><head><title>Debug Error Catcher</title><style>body{font-family:sans-serif;background:#0f172a;color:#f8fafc;padding:2rem;}pre{background:#1e293b;padding:1rem;border-radius:0.5rem;overflow-x:auto;color:#f43f5e;}</style></head><body>' .
                '<h1>⚠️ DEBUG ERROR CATCHER</h1>' .
                '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>' .
                '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>' .
                '<h2>Stack Trace:</h2><pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>' .
                '</body></html>',
                500
            );
        });
    })->create();

$app->useStoragePath('/tmp/storage');
$app->register(Illuminate\View\ViewServiceProvider::class);

return $app;
