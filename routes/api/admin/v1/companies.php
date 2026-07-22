<?php

use App\Http\Controllers\Admin\V1\CompanyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CompanyController::class, 'index']);
Route::post('/', [CompanyController::class, 'store']);
Route::put('/{company}', [CompanyController::class, 'update']);
Route::delete('/{company}', [CompanyController::class, 'destroy']);
