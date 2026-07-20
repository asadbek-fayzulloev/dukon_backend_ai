<?php

use App\Http\Controllers\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'index']);
Route::get('/list', [UserController::class, 'list']);
Route::put('{user}', [UserController::class, 'update']);
Route::get('{user}', [UserController::class, 'show']);
Route::delete('{user}', [UserController::class, 'destroy']);
Route::post('/', [UserController::class, 'store']);
