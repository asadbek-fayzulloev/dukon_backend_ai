<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('payed_price')->change();
            $table->index(['order_id', 'payment_type'], 'order_payments_order_type_index');
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->unsignedBigInteger('amount')->change();
            $table->unsignedBigInteger('remaining_amount')->nullable()->after('amount');
            $table->timestamp('paid_at')->nullable()->after('return_date');
            $table->index(['status', 'return_date'], 'debts_status_return_date_index');
        });

        DB::table('debts')->update([
            'remaining_amount' => DB::raw('amount'),
        ]);

        Schema::table('debts', function (Blueprint $table) {
            $table->unsignedBigInteger('remaining_amount')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropIndex('debts_status_return_date_index');
            $table->dropColumn(['remaining_amount', 'paid_at']);
            $table->integer('amount')->change();
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropIndex('order_payments_order_type_index');
            $table->float('payed_price')->change();
        });
    }
};
