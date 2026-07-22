<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained();
        });

        DB::statement('UPDATE invoices SET company_id = (SELECT company_id FROM warehouses WHERE warehouses.id = invoices.warehouse_id) WHERE invoices.warehouse_id IS NOT NULL');

        $defaultCompanyId = DB::table('companies')->orderBy('id')->value('id');
        DB::table('invoices')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
