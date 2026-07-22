<?php

use App\Http\Controllers\Mobile\V1\WarehouseProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WarehouseProductController::class, 'index'])->name('index');
Route::get('movements', [WarehouseProductController::class, 'movements'])->name('movements');
Route::post('import', [WarehouseProductController::class, 'import'])->name('import');
