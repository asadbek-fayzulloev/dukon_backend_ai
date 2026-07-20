<?php

use App\Http\Controllers\V1\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UnitController::class, 'index']);
Route::post('/', [UnitController::class, 'store']);
Route::put('/{unit}', [UnitController::class, 'update']);
Route::delete('/{unit}', [UnitController::class, 'destroy']);