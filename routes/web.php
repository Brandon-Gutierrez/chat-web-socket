<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

// Rutas de autenticacion con Google
Route::get('/auth/google', [AuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'callback'])->name('auth.google.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas del sistema
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ChatController::class, 'index'])->name('dashboard');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/join', [ChatController::class, 'join'])->name('chat.join');
    Route::post('/chat/{id}/message', [ChatController::class, 'sendMessage'])->name('chat.message');
});
