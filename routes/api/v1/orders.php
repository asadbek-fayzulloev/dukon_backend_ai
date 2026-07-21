<?php

use App\Http\Controllers\V1\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [OrderController::class, 'index'])->name('index');
Route::get('/statistics', [OrderController::class, 'statistics'])->name('statistics');
Route::get('/{order}', [OrderController::class, 'show'])->name('show');
Route::post('/', [OrderController::class, 'store'])->name('store');
