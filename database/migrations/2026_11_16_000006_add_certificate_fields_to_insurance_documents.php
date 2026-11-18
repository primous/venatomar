<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('insurance_documents', function (Blueprint $table) {
            $table->string('document_type', 30)->default('certificate')->after('reference');
            $table->date('birth_date')->nullable()->after('last_name');
            $table->string('email', 191)->nullable()->after('phone');
            $table->string('address', 255)->nullable()->after('email');
            $table->date('effective_date')->nullable()->after('term_months');
        });
    }

    public function down(): void
    {
        Schema::table('insurance_documents', function (Blueprint $table) {
            $table->dropColumn(['document_type', 'birth_date', 'email', 'address', 'effective_date']);
        });
    }
};
