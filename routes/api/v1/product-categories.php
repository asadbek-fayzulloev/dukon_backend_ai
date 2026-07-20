<?php


use App\Http\Controllers\V1\ProductCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductCategoryController::class, 'index']);
Route::get('/{product}', [ProductCategoryController::class, 'show']);
Route::post('/', [ProductCategoryController::class, 'store']);
Route::put('/{product}', [ProductCategoryController::class, 'update']);
Route::delete('/{product}', [ProductCategoryController::class, 'destroy']);