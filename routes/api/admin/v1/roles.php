<?php

use App\Http\Controllers\Admin\V1\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RoleController::class, 'index']);
Route::get('/{id}', [RoleController::class, 'show']);
Route::post('/', [RoleController::class, 'store']);
Route::put('/{id}', [RoleController::class, 'update']);
Route::delete('/{id}', [RoleController::class, 'destroy']);
