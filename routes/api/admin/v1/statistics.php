<?php

use App\Http\Controllers\Admin\V1\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::get('widgets', [StatisticsController::class, 'widgets'])->name('widgets');
Route::get('seller-stats', [StatisticsController::class, 'sellerStat'])->name('sellerStat');
Route::get('sales-stats', [StatisticsController::class, 'salesStat'])->name('salesStat');
Route::get('payment-stats', [StatisticsController::class, 'paymentStat'])->name('paymentStat');
Route::get('shop-sales-stats', [StatisticsController::class, 'shopSalesStat'])->name('shopSalesStat');
Route::get('warehouse-stock-stats', [StatisticsController::class, 'warehouseStockStat'])->name('warehouseStockStat');
Route::get('top-products-stats', [StatisticsController::class, 'topProductsStat'])->name('topProductsStat');
