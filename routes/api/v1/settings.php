<?php

use App\Http\Controllers\V1\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SettingsController::class, 'index'])->name('index');
Route::put('/{setting}', [SettingsController::class, 'update'])->name('update');
Route::post('/', [SettingsController::class, 'store'])->name('store');
Route::get('/{setting}', [SettingsController::class, 'show'])->name('show');
Route::delete('/{setting}', [SettingsController::class, 'destroy'])->name('destroy');
