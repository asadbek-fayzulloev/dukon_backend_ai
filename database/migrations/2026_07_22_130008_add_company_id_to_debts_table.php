<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained();
        });

        DB::statement('UPDATE debts SET company_id = (SELECT company_id FROM orders WHERE orders.id = debts.order_id) WHERE debts.order_id IS NOT NULL');

        $defaultCompanyId = DB::table('companies')->orderBy('id')->value('id');
        DB::table('debts')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
    }

    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
