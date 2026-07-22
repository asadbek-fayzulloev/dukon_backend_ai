<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // one_c | soliq_uz | ...
            $table->string('url')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_integrations');
    }
};
