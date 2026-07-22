<?php

use App\Http\Controllers\Admin\V1\CompanyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CompanyController::class, 'index']);
Route::post('/', [CompanyController::class, 'store']);
Route::put('/{company}', [CompanyController::class, 'update']);
Route::delete('/{company}', [CompanyController::class, 'destroy']);
Route::post('/{company}/activate', [CompanyController::class, 'activate']);
Route::post('/{company}/deactivate', [CompanyController::class, 'deactivate']);
