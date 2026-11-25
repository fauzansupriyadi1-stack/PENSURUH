<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Slug Navigation Routes (Landing Page Sections)
Route::get('/about', function () {
    return view('welcome', ['slug' => 'about']);
})->name('about');

Route::get('/review', function () {
    return view('welcome', ['slug' => 'review']);
})->name('review');

Route::get('/pricing', function () {
    return view('welcome', ['slug' => 'pricing']);
})->name('pricing');

Route::get('/contact', function () {
    return view('welcome', ['slug' => 'contact']);
})->name('contact');

Route::get('/login', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect('/dashboard');
    }
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect('/dashboard');
    }
    return view('auth.login');
})->name('register');

// Dashboard & Role Management (Hanya untuk pengguna yang sudah login)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/switch-role/{role}', [DashboardController::class, 'switchRole'])->name('dashboard.switch-role');
    Route::post('/orders', [DashboardController::class, 'createOrder'])->name('orders.create');
    Route::post('/orders/{slug}/take', [DashboardController::class, 'takeOrder'])->name('orders.take');
    Route::post('/orders/{slug}/complete', [DashboardController::class, 'completeOrder'])->name('orders.complete');
    Route::post('/orders/{slug}/cancel', [DashboardController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/dashboard/users/{slug}/role', [DashboardController::class, 'updateUserRole'])->name('users.update-role');
    Route::post('/dashboard/users/{slug}/suspend', [DashboardController::class, 'toggleSuspendUser'])->name('users.toggle-suspend');

    // Wallet & Isi Saldo via QRIS (DOKU)
    Route::post('/wallet/topup/qris', [WalletController::class, 'initiateTopUp'])->name('wallet.topup.qris');
    Route::get('/wallet/topup/status/{invoice}', [WalletController::class, 'checkStatus'])->name('wallet.topup.status');
    Route::get('/wallet/topup/simulate/{invoice}', [WalletController::class, 'simulatePayment'])->name('wallet.topup.simulate');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');
});

// DOKU Webhook Notification (Server-to-Server)
Route::post('/api/payment/doku/notification', [WalletController::class, 'handleNotification'])->name('doku.notification');
Route::post('/api/doku/notification', [WalletController::class, 'handleNotification']);

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Auth POST Routes
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', function () {
    return redirect('/');
});

