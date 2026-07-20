<?php

use App\Http\Controllers\V1\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/list', [PermissionController::class, 'list']);