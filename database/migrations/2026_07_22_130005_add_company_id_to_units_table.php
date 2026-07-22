<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained();
        });

        $defaultCompanyId = DB::table('companies')->orderBy('id')->value('id');
        DB::table('units')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
