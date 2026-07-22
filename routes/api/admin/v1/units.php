<?php

use App\Http\Controllers\Admin\V1\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UnitController::class, 'index'])->name('index');
Route::post('/', [UnitController::class, 'store'])->name('store');
Route::put('/{unit}', [UnitController::class, 'update'])->name('update');
Route::delete('/{unit}', [UnitController::class, 'destroy'])->name('destroy');
