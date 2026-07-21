<?php

use App\Http\Controllers\V1\StaticDataController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StaticDataController::class, 'index'])->name('index');
