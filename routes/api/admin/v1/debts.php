<?php

use App\Http\Controllers\Admin\V1\DebtController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DebtController::class, 'index'])->name('debts.index');
Route::get('/{id}', [DebtController::class, 'show'])->name('debts.show');
Route::put('/{id}', [DebtController::class, 'update'])->name('debts.update');
Route::post('/{id}/payments', [DebtController::class, 'pay'])->name('debts.pay');
