<?php

use App\Http\Controllers\V1\WarehouseProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WarehouseProductController::class, 'index'])->name('index');
Route::post('import', [WarehouseProductController::class, 'import'])->name('import');
