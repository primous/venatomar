<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A person who submitted at least one loan request (magic-link login, no password).
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('phone', 32);
            $table->string('locale', 5);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->nullable()->unique();
            $table->foreignId('applicant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('received')->index();
            $table->string('locale', 5);
            $table->string('country', 2)->index();
            $table->string('currency', 3);

            // Step 1 · Income
            $table->unsignedInteger('amount');
            $table->unsignedSmallInteger('term_months');
            $table->unsignedInteger('monthly_income');
            $table->string('employment_status', 40);
            $table->boolean('defaulter_list');

            // Step 2 · Personal data (encrypted columns are TEXT)
            $table->string('first_name', 80);
            $table->string('last_name', 120);
            $table->text('document_number')->nullable();
            $table->text('birthday');
            $table->string('email')->index();
            $table->string('phone', 32);

            // Step 3 · Address
            $table->string('geo_zip', 12);
            $table->string('geo_province', 80);
            $table->string('geo_city', 120);
            $table->string('home_street', 160);
            $table->string('home_number', 20);
            $table->string('apartment_floor', 40)->nullable();

            // Step 4 · Bank account / payout
            $table->string('payout_method', 20)->default('iban');
            $table->text('bank_account')->nullable();
            $table->string('mm_operator', 20)->nullable();
            $table->text('mm_number')->nullable();

            // Step 5 · Additional information
            $table->string('gender', 20);
            $table->string('loan_purpose', 30);
            $table->string('marital_status', 30);
            $table->string('company_sector', 30);
            $table->string('education', 30);
            $table->string('home_status', 30);
            $table->unsignedInteger('regular_expenses');
            $table->boolean('has_debt');
            $table->boolean('has_car');
            $table->string('car_number', 20)->nullable();

            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('utm')->nullable();
            $table->timestamp('consent_at');
            $table->string('consent_version', 20);

            // E-mail delivery tracking
            $table->timestamp('internal_mail_sent_at')->nullable();
            $table->timestamp('confirmation_mail_sent_at')->nullable();
            $table->timestamp('mail_failed_at')->nullable()->index();

            $table->timestamps();
        });

        // Only REAL reviews. The home section is hidden while this table has no published row.
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 5)->index();
            $table->string('author_name', 80);
            $table->unsignedTinyInteger('rating');
            $table->text('body');
            $table->date('reviewed_at');
            $table->string('source', 80)->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('magic_login_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->char('token_hash', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('deletion_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email');
            $table->string('locale', 5);
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deletion_requests');
        Schema::dropIfExists('magic_login_tokens');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('applicants');
    }
};
