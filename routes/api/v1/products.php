<?php


use App\Http\Controllers\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('list', [ProductController::class, 'list']);
Route::get('/', [ProductController::class, 'index']);
Route::get('low-stock-export', [ProductController::class, 'exportLowStock']);

Route::get('{product}', [ProductController::class, 'show']);

Route::post('/', [ProductController::class, 'store']);
Route::put('{product}', [ProductController::class, 'update']);

Route::delete('delete-all', [ProductController::class, 'destroyAll']);
Route::delete('{product}', [ProductController::class, 'destroy']);