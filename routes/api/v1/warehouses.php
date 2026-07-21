<?php

use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WarehouseController::class, 'index'])->name('index');
Route::post('/', [WarehouseController::class, 'store'])->name('store');
Route::put('/{warehouse}', [WarehouseController::class, 'update'])->name('update');
Route::get('/{warehouse}/products', [WarehouseController::class, 'listProducts'])->name('listProducts');
Route::post('/{warehouse}/products', [WarehouseController::class, 'addProduct'])->name('addProduct');
