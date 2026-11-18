<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_contracts', function (Blueprint $table) {
            $table->decimal('document_fee', 10, 2)->nullable()->after('amount');
        });

        Schema::table('insurance_documents', function (Blueprint $table) {
            $table->decimal('document_fee', 10, 2)->nullable()->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('loan_contracts', function (Blueprint $table) {
            $table->dropColumn('document_fee');
        });

        Schema::table('insurance_documents', function (Blueprint $table) {
            $table->dropColumn('document_fee');
        });
    }
};
