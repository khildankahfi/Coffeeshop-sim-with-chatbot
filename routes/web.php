<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\LaporanController;

// ===== PUBLIC =====
Route::get('/', fn() => view('landing'))->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

// AI Chat
Route::get('/chat',       [AiChatController::class, 'index'])->name('chat.index');
Route::post('/chat/send', [AiChatController::class, 'send'])->name('chat.send');

// ===== ADMIN (wajib login) =====
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.orders.index'));

     // Check new orders untuk auto-refresh
    Route::get('/orders/check-new', function () {
        return response()->json([
            'total'   => \App\Models\Order::count(),
            'pending' => \App\Models\Order::where('status', 'pending')->count(),
        ]);
    })->name('orders.check-new');


    // Orders
    Route::get('/orders',         [OrderController::class, 'index'])->name('orders.index');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');

     // Kasir
    Route::get('/kasir',  [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir', [KasirController::class, 'store'])->name('kasir.store');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    
    // Menu — list admin
    Route::get('/menu', [MenuController::class, 'adminIndex'])->name('menu.adminIndex');
    Route::resource('menu', MenuController::class)->except(['index']);
});

// Dashboard redirect setelah login
Route::get('/dashboard', fn() => redirect()->route('admin.orders.index'))
    ->middleware('auth')
    ->name('dashboard');

Route::get('/personal/recommendation', function () {
    return response()->json([
        'message' => 'Recommendation API'
    ]);
})->name('api.personal.recommendation');

require __DIR__.'/auth.php';
