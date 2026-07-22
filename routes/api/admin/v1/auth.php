<?php

use App\Http\Controllers\Admin\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::get('me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
