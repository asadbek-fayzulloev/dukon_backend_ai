<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin/v1')->name('admin.')->group(base_path('routes/api/admin/v1/index.php'));
Route::prefix('mobile/v1')->name('mobile.')->group(base_path('routes/api/mobile/v1/index.php'));
