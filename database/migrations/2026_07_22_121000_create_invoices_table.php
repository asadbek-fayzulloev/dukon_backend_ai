<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * A nakladnoy (waybill) — the document header behind every stock
     * movement. warehouse_product_histories holds the line items;
     * this holds the movement itself: who/what/when/why.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // buy | sold | transfer
            $table->string('number')->nullable();
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('destination_warehouse_id')->nullable()->constrained('warehouses');
            $table->foreignId('order_id')->nullable()->constrained();
            $table->foreignId('admin_id')->nullable()->constrained();
            $table->float('total_amount')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
