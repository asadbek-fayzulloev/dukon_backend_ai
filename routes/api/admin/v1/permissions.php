<?php

use App\Http\Controllers\Admin\V1\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/list', [PermissionController::class, 'list'])->name('index');
