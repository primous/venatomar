<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_approval_documents', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 32)->nullable()->unique();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('locale', 5);
            $table->string('country', 80);
            $table->string('currency', 3);
            $table->string('first_name', 80);
            $table->string('last_name', 120);
            $table->string('phone', 40)->nullable();
            $table->string('email', 191)->nullable();
            $table->text('document_number')->nullable(); // encrypted
            $table->date('birth_date')->nullable();
            $table->string('address', 255)->nullable();
            $table->unsignedInteger('amount');
            $table->unsignedSmallInteger('term_months');
            $table->unsignedInteger('monthly_payment')->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->unsignedInteger('total_repayable')->nullable();
            $table->string('lender', 120)->nullable();
            $table->string('payout_method', 40)->default('iban');
            $table->string('payout_delay', 60)->default('24_48h');
            $table->date('approval_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_approval_documents');
    }
};
