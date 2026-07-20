<?php

use Illuminate\Support\Facades\Route;

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

});
