<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('warehouse_product_id')
                ->nullable()
                ->after('product_id')
                ->constrained('warehouse_products')
                ->restrictOnDelete();
            $table->unsignedBigInteger('net_price')->default(0)->after('product_price');

            $table->unsignedBigInteger('product_price')->change();
            $table->decimal('quantity', 18, 3)->change();
            $table->unsignedBigInteger('total_price')->change();

            $table->index(['order_id', 'product_id'], 'order_items_order_product_index');
        });

        // Legacy order_items.product_id points to warehouse_products.id.
        // Preserve the consumed batch and normalize product_id to products.id.
        DB::table('order_items')
            ->select(['id', 'product_id'])
            ->orderBy('id')
            ->chunkById(500, function ($items) {
                foreach ($items as $item) {
                    $batch = DB::table('warehouse_products')
                        ->where('id', $item->product_id)
                        ->first(['id', 'product_id', 'net_price']);

                    if ($batch === null) {
                        continue;
                    }

                    DB::table('order_items')
                        ->where('id', $item->id)
                        ->update([
                            'warehouse_product_id' => $batch->id,
                            'product_id' => $batch->product_id,
                            'net_price' => max(0, (int) round((float) $batch->net_price)),
                        ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('order_items')
            ->whereNotNull('warehouse_product_id')
            ->update([
                'product_id' => DB::raw('warehouse_product_id'),
            ]);

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_order_product_index');
            $table->dropConstrainedForeignId('warehouse_product_id');
            $table->dropColumn('net_price');

            $table->float('product_price')->change();
            $table->float('quantity')->change();
            $table->float('total_price')->change();
        });
    }
};
