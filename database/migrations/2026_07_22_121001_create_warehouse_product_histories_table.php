<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Line items behind an Invoice — one row per product per stock
     * movement (buy/sold/transfer). warehouse_products itself only ever
     * holds the current aggregate quantity per product/warehouse/price
     * batch; this is the append-only ledger behind it.
     */
    public function up(): void
    {
        Schema::create('warehouse_product_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('admin_id')->nullable()->constrained();
            $table->string('type'); // buy | sold | transfer
            $table->float('quantity');
            $table->float('price')->nullable();
            $table->float('net_price')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_product_histories');
    }
};
