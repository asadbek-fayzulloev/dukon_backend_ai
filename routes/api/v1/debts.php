<?php

use App\Http\Controllers\V1\DebtController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DebtController::class, 'index']);
Route::get('/{id}', [DebtController::class, 'show']);
Route::put('/{id}', [DebtController::class, 'update']);
Route::post('/{id}/payments', [DebtController::class, 'pay']);
