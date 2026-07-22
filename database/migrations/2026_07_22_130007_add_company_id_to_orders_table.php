<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained();
        });

        DB::statement('UPDATE orders SET company_id = (SELECT company_id FROM shops WHERE shops.id = orders.shop_id) WHERE orders.shop_id IS NOT NULL');

        $defaultCompanyId = DB::table('companies')->orderBy('id')->value('id');
        DB::table('orders')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
