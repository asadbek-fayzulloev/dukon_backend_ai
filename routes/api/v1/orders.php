<?php

use App\Http\Controllers\V1\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [OrderController::class, 'index']);
Route::get('/statistics', [OrderController::class, 'statistics']);
Route::get('/{order}', [OrderController::class, 'show']);
Route::post('/', [OrderController::class, 'store']);
