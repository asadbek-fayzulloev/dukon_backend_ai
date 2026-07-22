<?php

use App\Http\Controllers\Admin\V1\IntegrationController;
use Illuminate\Support\Facades\Route;

Route::get('one-c', [IntegrationController::class, 'oneCShow']);
Route::put('one-c', [IntegrationController::class, 'oneCUpdate']);
Route::post('one-c/test', [IntegrationController::class, 'oneCTest']);
