<?php


use App\Http\Controllers\Mobile\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('list', [ProductController::class, 'list'])->name('list');
Route::get('/', [ProductController::class, 'index'])->name('index');;
Route::get('low-stock-export', [ProductController::class, 'exportLowStock'])->name('low-stock.export');

Route::get('{product}', [ProductController::class, 'show'])->name('show');
Route::post('/', [ProductController::class, 'store'])->name('store');
Route::put('{product}', [ProductController::class, 'update'])->name('update');

Route::delete('delete-all', [ProductController::class, 'destroyAll'])->name('destroyAll');
Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');
