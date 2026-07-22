<?php

use App\Http\Controllers\Admin\V1\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
