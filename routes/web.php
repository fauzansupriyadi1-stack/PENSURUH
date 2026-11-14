<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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

// Dashboard & Role Management
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/switch-role/{role}', [DashboardController::class, 'switchRole'])->name('dashboard.switch-role');
Route::post('/orders', [DashboardController::class, 'createOrder'])->name('orders.create');
Route::post('/orders/{id}/take', [DashboardController::class, 'takeOrder'])->name('orders.take');
Route::post('/orders/{id}/complete', [DashboardController::class, 'completeOrder'])->name('orders.complete');
Route::post('/dashboard/users/{id}/role', [DashboardController::class, 'updateUserRole'])->name('users.update-role');

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

