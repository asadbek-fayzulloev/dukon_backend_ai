<?php

use Illuminate\Support\Facades\Route;

Route::get('ping', function () {
    return response()->json([
        'status' => 'ok',
        'time' => now()->toIso8601String(),
    ]);
});

Route::module('auth');

Route::middleware(['auth:api'])->group(static function () {
    Route::module('orders');
    Route::module('settings');
    Route::module('statistics');
    Route::module('debts');
    Route::module('permissions');
    Route::module('products');
    Route::module('product-categories');
    Route::module('users');
    Route::module('units');
    Route::module('settings');
    Route::module('warehouses');
    Route::module('warehouse-products');
    Route::module('static-data');
    Route::module('shops');
    Route::module('admins');

});
