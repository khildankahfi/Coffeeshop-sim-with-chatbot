<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;

// Landing
Route::get('/', fn() => view('landing'))->name('home');

// AI Chat
Route::get('/chat',       [AiChatController::class, 'index'])->name('chat.index');
Route::post('/chat/send', [AiChatController::class, 'send'])->name('chat.send');

// Menu publik
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

// Orders publik (untuk navbar)
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('menu',   MenuController::class)->except(['index']);
    Route::resource('orders', OrderController::class)->except(['index']);
});
