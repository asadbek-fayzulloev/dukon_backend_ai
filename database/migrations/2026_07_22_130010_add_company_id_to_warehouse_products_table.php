<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained();
        });

        DB::statement('UPDATE warehouse_products SET company_id = (SELECT company_id FROM warehouses WHERE warehouses.id = warehouse_products.warehouse_id) WHERE warehouse_products.warehouse_id IS NOT NULL');

        $defaultCompanyId = DB::table('companies')->orderBy('id')->value('id');
        DB::table('warehouse_products')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
    }

    public function down(): void
    {
        Schema::table('warehouse_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
