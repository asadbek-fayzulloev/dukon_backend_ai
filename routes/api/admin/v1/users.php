<?php

use App\Http\Controllers\Admin\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'index'])->name('index');
Route::get('/list', [UserController::class, 'list'])->name('list');
Route::put('{user}', [UserController::class, 'update'])->name('update');
Route::get('{user}', [UserController::class, 'show'])->name('show');
Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
Route::post('/', [UserController::class, 'store'])->name('store');
