<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('leads') && ! Schema::hasColumn('leads', 'admin_email')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->string('admin_email', 100)->nullable()->default('info@venatomar.com')->index()->after('email');
                $table->string('channel', 40)->nullable()->default('info')->index()->after('admin_email');
            });
        }

        if (Schema::hasTable('loan_contracts') && ! Schema::hasColumn('loan_contracts', 'admin_email')) {
            Schema::table('loan_contracts', function (Blueprint $table) {
                $table->string('admin_email', 100)->nullable()->index()->after('email');
            });
        }

        if (Schema::hasTable('insurance_documents') && ! Schema::hasColumn('insurance_documents', 'admin_email')) {
            Schema::table('insurance_documents', function (Blueprint $table) {
                $table->string('admin_email', 100)->nullable()->index()->after('email');
            });
        }

        if (Schema::hasTable('loan_approval_documents') && ! Schema::hasColumn('loan_approval_documents', 'admin_email')) {
            Schema::table('loan_approval_documents', function (Blueprint $table) {
                $table->string('admin_email', 100)->nullable()->index()->after('email');
            });
        }

        if (Schema::hasTable('bank_clients') && ! Schema::hasColumn('bank_clients', 'admin_email')) {
            Schema::table('bank_clients', function (Blueprint $table) {
                $table->string('admin_email', 100)->nullable()->default('info@venatomar.com')->index()->after('email');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'admin_email')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropColumn(['admin_email', 'channel']);
            });
        }

        if (Schema::hasTable('loan_contracts') && Schema::hasColumn('loan_contracts', 'admin_email')) {
            Schema::table('loan_contracts', function (Blueprint $table) {
                $table->dropColumn('admin_email');
            });
        }

        if (Schema::hasTable('insurance_documents') && Schema::hasColumn('insurance_documents', 'admin_email')) {
            Schema::table('insurance_documents', function (Blueprint $table) {
                $table->dropColumn('admin_email');
            });
        }

        if (Schema::hasTable('loan_approval_documents') && Schema::hasColumn('loan_approval_documents', 'admin_email')) {
            Schema::table('loan_approval_documents', function (Blueprint $table) {
                $table->dropColumn('admin_email');
            });
        }

        if (Schema::hasTable('bank_clients') && Schema::hasColumn('bank_clients', 'admin_email')) {
            Schema::table('bank_clients', function (Blueprint $table) {
                $table->dropColumn('admin_email');
            });
        }
    }
};
