<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect('/login');
});

// Only for visitors who are NOT logged in
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Only for logged-in users
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('suppliers', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('items', ItemController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/stock', [StockController::class, 'store']);
    Route::post('/sales', [SaleController::class, 'store']);
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
});