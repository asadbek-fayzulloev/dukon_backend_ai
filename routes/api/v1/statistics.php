<?php

use App\Http\Controllers\V1\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::get('widgets', [StatisticsController::class, 'widgets']);
Route::get('seller-stats', [StatisticsController::class, 'sellerStat']);
Route::get('sales-stats', [StatisticsController::class, 'salesStat']);

