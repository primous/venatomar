<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('insurance_documents', function (Blueprint $table) {
            $table->string('issuer_address', 255)->nullable()->after('address');
            $table->string('issuer_phone', 60)->nullable()->after('issuer_address');
            $table->string('issuer_city', 80)->nullable()->after('issuer_phone');
        });

        Schema::table('loan_approval_documents', function (Blueprint $table) {
            $table->string('issuer_address', 255)->nullable()->after('address');
            $table->string('issuer_phone', 60)->nullable()->after('issuer_address');
            $table->string('issuer_city', 80)->nullable()->after('issuer_phone');
        });

        Schema::table('loan_contracts', function (Blueprint $table) {
            $table->string('issuer_address', 255)->nullable()->after('address');
            $table->string('issuer_phone', 60)->nullable()->after('issuer_address');
            $table->string('issuer_city', 80)->nullable()->after('issuer_phone');
        });
    }

    public function down(): void
    {
        Schema::table('insurance_documents', function (Blueprint $table) {
            $table->dropColumn(['issuer_address', 'issuer_phone', 'issuer_city']);
        });

        Schema::table('loan_approval_documents', function (Blueprint $table) {
            $table->dropColumn(['issuer_address', 'issuer_phone', 'issuer_city']);
        });

        Schema::table('loan_contracts', function (Blueprint $table) {
            $table->dropColumn(['issuer_address', 'issuer_phone', 'issuer_city']);
        });
    }
};
