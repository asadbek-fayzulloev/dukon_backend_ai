<?php

use App\Http\Controllers\Admin\V1\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AdminController::class, 'index']);
Route::get('/{admin}', [AdminController::class, 'show']);
Route::post('/', [AdminController::class, 'store']);
Route::put('/{admin}', [AdminController::class, 'update']);
Route::delete('/{admin}', [AdminController::class, 'destroy']);
