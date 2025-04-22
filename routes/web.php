<?php

use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CashierController::class, 'index'])->name('cashier.index');
Route::post('/transactions', [CashierController::class, 'store'])->name('transactions.store');
Route::get('/products', [CashierController::class, 'getProducts'])->name('products.index');
Route::get('/transactions', [CashierController::class, 'getTransactions'])->name('transactions.index');