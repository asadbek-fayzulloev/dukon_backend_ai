<?php

use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WarehouseController::class, 'index']);
Route::post('/', [WarehouseController::class, 'store']);
Route::put('/{warehouse}', [WarehouseController::class, 'update']);
Route::get('/{warehouse}/products', [WarehouseController::class, 'listProducts']);
Route::post('/{warehouse}/products', [WarehouseController::class, 'addProduct']);