<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->whereNull('order_total_paid')
            ->update(['order_total_paid' => 0]);

        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('shop_id');
            $table->string('device_id')->nullable()->after('warehouse_id');
            $table->unsignedBigInteger('subtotal')->default(0)->after('device_id');
            $table->string('discount_type')->nullable()->after('subtotal');
            $table->decimal('discount_value', 18, 3)->nullable()->after('discount_type');
            $table->unsignedBigInteger('discount_amount')->default(0)->after('discount_value');
            $table->unsignedBigInteger('debt_amount')->default(0)->after('order_total_paid');
            $table->string('status')->default('completed')->after('debt_amount');
            $table->timestamp('sold_at')->nullable()->after('status');
            $table->timestamp('synced_at')->nullable()->after('sold_at');

            $table->unsignedBigInteger('order_total_price')->default(0)->change();
            $table->unsignedBigInteger('order_total_paid')->default(0)->change();
        });

        DB::table('orders')
            ->select([
                'id',
                'discount',
                'order_total_price',
                'order_total_paid',
                'created_at',
            ])
            ->orderBy('id')
            ->chunkById(500, function ($orders) {
                foreach ($orders as $order) {
                    $discountAmount = max(0, (int) round((float) ($order->discount ?? 0)));
                    $totalPrice = max(0, (int) $order->order_total_price);
                    $totalPaid = max(0, (int) $order->order_total_paid);

                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update([
                            'uuid' => (string) Str::uuid(),
                            'subtotal' => $totalPrice + $discountAmount,
                            'discount_type' => $discountAmount > 0 ? 'fixed' : null,
                            'discount_value' => $discountAmount > 0 ? $discountAmount : null,
                            'discount_amount' => $discountAmount,
                            'debt_amount' => max($totalPrice - $totalPaid, 0),
                            'status' => $totalPrice > $totalPaid ? 'debt' : 'completed',
                            'sold_at' => $order->created_at,
                            'synced_at' => $order->created_at,
                        ]);
                }
            });

        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
            $table->unique('uuid', 'orders_uuid_unique');
            $table->index(['shop_id', 'sold_at'], 'orders_shop_sold_at_index');
            $table->index(['warehouse_id', 'sold_at'], 'orders_warehouse_sold_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_warehouse_sold_at_index');
            $table->dropIndex('orders_shop_sold_at_index');
            $table->dropUnique('orders_uuid_unique');

            $table->dropColumn([
                'uuid',
                'warehouse_id',
                'device_id',
                'subtotal',
                'discount_type',
                'discount_value',
                'discount_amount',
                'debt_amount',
                'status',
                'sold_at',
                'synced_at',
            ]);

            $table->integer('order_total_price')->change();
            $table->float('order_total_paid')->nullable()->change();
        });
    }
};
