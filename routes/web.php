<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// ===== PUBLIC =====
Route::get('/', fn() => view('landing'))->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

// AI Chat
Route::get('/chat',       [AiChatController::class, 'index'])->name('chat.index');
Route::post('/chat/send', [AiChatController::class, 'send'])->name('chat.send');

// ===== ADMIN (wajib login) =====
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.orders.index'));
    Route::get('/orders',         [OrderController::class, 'index'])->name('orders.index');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::resource('menu', MenuController::class)->except(['index']);

    // Menu — list admin
    Route::get('/menu', [MenuController::class, 'adminIndex'])->name('menu.adminIndex');
    Route::resource('menu', MenuController::class)->except(['index']);
});

// Dashboard redirect setelah login
Route::get('/dashboard', fn() => redirect()->route('admin.orders.index'))
    ->middleware('auth')
    ->name('dashboard');

require __DIR__.'/auth.php';
