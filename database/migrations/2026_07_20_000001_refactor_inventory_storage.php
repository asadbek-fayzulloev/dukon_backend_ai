<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('notify_limit', 18, 3)->nullable()->change();
            $table->index(['category_id', 'name'], 'products_category_name_index');
        });

        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->decimal('quantity', 18, 3)->default(0)->change();
            $table->unsignedBigInteger('net_price')->change();
            $table->unsignedBigInteger('price')->change();
            $table->index(
                ['warehouse_id', 'product_id', 'created_at'],
                'warehouse_products_fifo_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->dropIndex('warehouse_products_fifo_index');
            $table->float('quantity')->change();
            $table->float('net_price')->change();
            $table->float('price')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_category_name_index');
            $table->unsignedBigInteger('notify_limit')->nullable()->change();
        });
    }
};
