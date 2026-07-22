<?php

use App\Http\Controllers\Admin\V1\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'index']);
Route::post('/', [ShopController::class, 'store']);
Route::put('/{shop}', [ShopController::class, 'update']);
Route::delete('/{shop}', [ShopController::class, 'destroy']);
