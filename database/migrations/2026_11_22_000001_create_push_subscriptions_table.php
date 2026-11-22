<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('subscribable');
            $table->string('email')->nullable()->index();
            $table->text('endpoint');
            $table->string('endpoint_hash', 64)->unique();
            $table->text('public_key'); // p256dh key
            $table->string('auth_token'); // auth secret
            $table->string('content_encoding')->default('aes128gcm');
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
