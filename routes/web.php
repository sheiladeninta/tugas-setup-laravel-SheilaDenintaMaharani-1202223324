<?php

use App\Http\Controllers\CashierController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/sistem-kasir', [CashierController::class, 'index'])->name('cashier.index');
    Route::post('/transactions', [CashierController::class, 'store'])->name('transactions.store');
    Route::get('/products', [CashierController::class, 'getProducts'])->name('products.index');
    Route::get('/transactions', [CashierController::class, 'getTransactions'])->name('transactions.index');
});