<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;

// ===== PUBLIC =====
Route::get('/', fn() => view('landing'))->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

// AI Chat
Route::get('/chat',       [AiChatController::class, 'index'])->name('chat.index');
Route::post('/chat/send', [AiChatController::class, 'send'])->name('chat.send');

// ===== ADMIN (wajib login) =====
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard redirect
    Route::get('/', fn() => redirect()->route('admin.orders.index'))->name('dashboard');

    // Orders
    Route::get('/orders',         [OrderController::class, 'index'])->name('orders.index');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');

    // Menu CRUD
    Route::resource('menu', MenuController::class)->except(['index'])->names([
        'create'  => 'menu.create',
        'store'   => 'menu.store',
        'edit'    => 'menu.edit',
        'update'  => 'menu.update',
        'destroy' => 'menu.destroy',
    ]);
});

// Auth routes (login/logout dari Breeze)
require __DIR__.'/auth.php';
