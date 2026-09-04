<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobOrderController;
use App\Http\Controllers\TarifController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard & Rekap
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Job Orders
    Route::post('/job-orders', [JobOrderController::class, 'store'])->name('job-orders.store');
    Route::put('/job-orders/{jobOrder}', [JobOrderController::class, 'update'])->name('job-orders.update');
    Route::delete('/job-orders/{jobOrder}', [JobOrderController::class, 'destroy'])->name('job-orders.destroy');

    // Exports
    Route::get('/export/csv', [JobOrderController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export/pdf', [JobOrderController::class, 'exportPdf'])->name('export.pdf');

    // Admin Tarif Management
    Route::get('/tarifs', [TarifController::class, 'index'])->name('tarifs.index');
    Route::post('/tarifs', [TarifController::class, 'store'])->name('tarifs.store');
    Route::put('/tarifs/{tarif}', [TarifController::class, 'update'])->name('tarifs.update');
    Route::delete('/tarifs/{tarif}', [TarifController::class, 'destroy'])->name('tarifs.destroy');

    // Dashboard Stats API (Real-time Ajax Refresh)
    Route::get('/api/dashboard-stats', [DashboardController::class, 'apiStats'])->name('dashboard.stats');

    // AI Assistant Chatbot API
    Route::post('/ai/chat', [\App\Http\Controllers\AiChatController::class, 'chat'])->name('ai.chat');
    Route::post('/ai/undo', [\App\Http\Controllers\AiChatController::class, 'undo'])->name('ai.undo.post');
    Route::delete('/ai/undo/{id?}', [\App\Http\Controllers\AiChatController::class, 'undo'])->name('ai.undo');
});
