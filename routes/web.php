<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobOrderController;
use App\Http\Controllers\TarifController;
use Illuminate\Support\Facades\Route;

// Migration Helper Route for Vercel / Remote Deployment
Route::get('/migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = \Illuminate\Support\Facades::Artisan::output();
        return response('<div style="background:#0f172a;color:#38bdf8;padding:24px;font-family:sans-serif;border-radius:12px;max-width:800px;margin:40px auto;box-shadow:0 10px 25px rgba(0,0,0,0.5);">'
            . '<h2 style="color:#10b981;margin-top:0;">✅ Database Migration Executed Successfully</h2>'
            . '<pre style="background:#1e293b;color:#f8fafc;padding:16px;border-radius:8px;overflow-x:auto;">' . htmlspecialchars($output ?: 'Nothing to migrate or migration completed cleanly.') . '</pre>'
            . '<a href="/" style="display:inline-block;margin-top:16px;background:#3b82f6;color:white;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:bold;">Return to Dashboard &rarr;</a>'
            . '</div>');
    } catch (\Throwable $e) {
        return response('<div style="background:#0f172a;color:#f87171;padding:24px;font-family:sans-serif;border-radius:12px;max-width:800px;margin:40px auto;box-shadow:0 10px 25px rgba(0,0,0,0.5);">'
            . '<h2 style="color:#ef4444;margin-top:0;">❌ Migration Failed</h2>'
            . '<pre style="background:#1e293b;color:#fca5a5;padding:16px;border-radius:8px;overflow-x:auto;">' . htmlspecialchars($e->getMessage()) . '</pre>'
            . '<a href="/" style="display:inline-block;margin-top:16px;background:#64748b;color:white;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:bold;">Return to Dashboard &rarr;</a>'
            . '</div>', 500);
    }
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

use App\Http\Controllers\UserController;

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard & Rekap
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/user/update-target', [DashboardController::class, 'updateTarget'])->name('user.target.update');

    // Job Orders
    Route::post('/job-orders', [JobOrderController::class, 'store'])->name('job-orders.store');
    Route::put('/job-orders/{jobOrder}', [JobOrderController::class, 'update'])->name('job-orders.update');
    Route::delete('/job-orders/{jobOrder}', [JobOrderController::class, 'destroy'])->name('job-orders.destroy');

    // Exports
    Route::get('/export/csv', [JobOrderController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/pdf', [JobOrderController::class, 'exportPdf'])->name('export.pdf');

    // Admin Routes (Kelola User & Kelola Tarif)
    Route::middleware('admin')->group(function () {
        // User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Tarif Management
        Route::get('/tarifs', [TarifController::class, 'index'])->name('tarifs.index');
        Route::post('/tarifs', [TarifController::class, 'store'])->name('tarifs.store');
        Route::put('/tarifs/{tarif}', [TarifController::class, 'update'])->name('tarifs.update');
        Route::delete('/tarifs/{tarif}', [TarifController::class, 'destroy'])->name('tarifs.destroy');
    });

    // Dashboard Stats API (Real-time Ajax Refresh)
    Route::get('/api/dashboard-stats', [DashboardController::class, 'apiStats'])->name('dashboard.stats');

    // AI Assistant Chatbot API
    Route::post('/ai/chat', [\App\Http\Controllers\AiChatController::class, 'chat'])->name('ai.chat');
    Route::post('/ai/undo', [\App\Http\Controllers\AiChatController::class, 'undo'])->name('ai.undo.post');
    Route::delete('/ai/undo/{id?}', [\App\Http\Controllers\AiChatController::class, 'undo'])->name('ai.undo');
});
