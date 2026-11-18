<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_documents', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 32)->nullable()->unique();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('locale', 5);
            $table->string('country', 2);
            $table->string('currency', 3);
            $table->string('first_name', 80);
            $table->string('last_name', 120);
            $table->string('phone', 40)->nullable();
            $table->text('document_number')->nullable(); // encrypted
            $table->unsignedInteger('amount');
            $table->unsignedSmallInteger('term_months');
            $table->string('lender', 120)->nullable();
            $table->json('guarantees');
            $table->unsignedTinyInteger('coverage_percent')->default(100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_documents');
    }
};
