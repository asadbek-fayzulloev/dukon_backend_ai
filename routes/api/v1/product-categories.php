<?php


use App\Http\Controllers\V1\ProductCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
Route::get('/{product}', [ProductCategoryController::class, 'show'])->name('show');
Route::post('/', [ProductCategoryController::class, 'store'])->name('store');
Route::put('/{product}', [ProductCategoryController::class, 'update'])->name('update');
Route::delete('/{product}', [ProductCategoryController::class, 'destroy'])->name('destroy');
