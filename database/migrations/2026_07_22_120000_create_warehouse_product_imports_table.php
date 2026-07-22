<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Logs each individual stock-in line from ImportProductAction. warehouse_products
     * itself only holds the current aggregate quantity per product/warehouse/price
     * batch — this table is the append-only history behind the "Tannarx"/kirim
     * movement view, since imports upsert in place with no other audit trail.
     */
    public function up(): void
    {
        Schema::create('warehouse_product_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('admin_id')->nullable()->constrained();
            $table->float('quantity');
            $table->float('net_price');
            $table->float('price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_product_imports');
    }
};
