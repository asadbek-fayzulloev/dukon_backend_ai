<?php

use App\Http\Controllers\V1\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SettingsController::class, 'index']);
Route::put('/{setting}', [SettingsController::class, 'update']);
Route::post('/', [SettingsController::class, 'store']);
Route::get('/{setting}', [SettingsController::class, 'show']);
Route::delete('/{setting}', [SettingsController::class, 'destroy']);
