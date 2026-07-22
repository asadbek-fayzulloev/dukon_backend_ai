<?php

use App\Http\Controllers\Mobile\V1\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/list', [PermissionController::class, 'list'])->name('index');
