<?php

use App\Http\Controllers\Admin\V1\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('sales', [ReportController::class, 'sales']);
Route::get('profit-loss', [ReportController::class, 'profitLoss']);
Route::get('stock', [ReportController::class, 'stock']);
