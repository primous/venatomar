<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_clients', function (Blueprint $table) {
            $table->string('locale', 10)->default('es')->after('country_code');
        });
    }

    public function down(): void
    {
        Schema::table('bank_clients', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
