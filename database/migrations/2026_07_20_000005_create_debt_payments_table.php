<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')
                ->constrained('debts')
                ->cascadeOnDelete();
            $table->string('payment_type');
            $table->unsignedBigInteger('amount');
            $table->timestamp('paid_at');
            $table->timestamps();

            $table->index(['debt_id', 'paid_at'], 'debt_payments_debt_paid_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_payments');
    }
};
