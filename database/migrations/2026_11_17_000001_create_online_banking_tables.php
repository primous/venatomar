<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Bank Clients (Accounts created by admin only)
        Schema::create('bank_clients', function (Blueprint $table) {
            $table->id();
            $table->string('account_number', 32)->unique();
            $table->string('iban', 34)->unique();
            $table->string('bic', 11)->default('VTMARSM1XXX');
            $table->string('first_name', 80);
            $table->string('last_name', 120);
            $table->string('email', 191)->unique();
            $table->string('password');
            $table->string('phone', 32)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('document_number', 60)->nullable();
            $table->string('country', 100)->default('España');
            $table->string('country_code', 3)->default('ES');
            $table->string('city', 100)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('currency', 5)->default('EUR');
            $table->decimal('balance', 14, 2)->default(0.00);
            $table->decimal('savings_balance', 14, 2)->default(0.00);
            $table->string('status', 20)->default('active'); // active, frozen, blocked
            $table->string('tier', 20)->default('standard'); // standard, silver, gold, vip
            $table->boolean('can_receive_otp_email')->default(false); // Admin authorization for OTP via email
            $table->decimal('authorized_overdraft', 10, 2)->default(0.00);
            $table->string('avatar', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Bank Transactions (Transfers, deposits, withdrawals)
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_client_id')->constrained('bank_clients')->cascadeOnDelete();
            $table->string('reference', 32)->unique();
            $table->string('type', 30); // transfer_out, transfer_in, savings_deposit, savings_withdrawal, loan_payout, loan_repayment, fee
            $table->string('category', 40)->default('virement');
            $table->decimal('amount', 14, 2);
            $table->string('currency', 5)->default('EUR');
            $table->string('status', 20)->default('pending'); // pending, completed, cancelled, failed
            $table->string('target_bank_name', 120)->nullable();
            $table->string('target_account_holder', 120)->nullable();
            $table->string('target_account_number', 60)->nullable();
            $table->text('description')->nullable();
            $table->string('otp_code', 10)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->boolean('is_otp_authorized')->default(false);
            $table->boolean('approved_by_admin')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 3. Bank Client Loans (Active loans and revolving credits)
        Schema::create('bank_client_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_client_id')->constrained('bank_clients')->cascadeOnDelete();
            $table->string('reference', 32)->unique();
            $table->string('title', 120)->default('Prêt Personnel Venatomar');
            $table->decimal('amount', 14, 2);
            $table->decimal('remaining_amount', 14, 2);
            $table->unsignedSmallInteger('term_months');
            $table->decimal('interest_rate', 5, 2)->default(3.50);
            $table->decimal('monthly_payment', 10, 2);
            $table->decimal('total_repayable', 14, 2);
            $table->string('status', 20)->default('active'); // pending_approval, active, completed, rejected
            $table->date('disbursement_date')->nullable();
            $table->date('next_payment_date')->nullable();
            $table->timestamps();
        });

        // 4. Bank Loan Repayments (Repayment schedule)
        Schema::create('bank_loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_client_loan_id')->constrained('bank_client_loans')->cascadeOnDelete();
            $table->unsignedSmallInteger('installment_number');
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->decimal('principal_amount', 10, 2);
            $table->decimal('interest_amount', 10, 2);
            $table->string('status', 20)->default('pending'); // pending, paid, late
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // 5. Bank Support Messages & Fraud Reports
        Schema::create('bank_support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_client_id')->constrained('bank_clients')->cascadeOnDelete();
            $table->foreignId('bank_transaction_id')->nullable()->constrained('bank_transactions')->nullOnDelete();
            $table->string('subject', 191);
            $table->string('category', 40)->default('general'); // general, fraud_alert, transaction_dispute, loan_inquiry, technical
            $table->text('message');
            $table->text('admin_reply')->nullable();
            $table->string('status', 20)->default('open'); // open, in_progress, resolved, closed
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_support_messages');
        Schema::dropIfExists('bank_loan_repayments');
        Schema::dropIfExists('bank_client_loans');
        Schema::dropIfExists('bank_transactions');
        Schema::dropIfExists('bank_clients');
    }
};
