<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('title', 120)->nullable()->after('rating');
            // true only when the review is tied to a real submitted request.
            $table->boolean('is_verified')->default(false)->after('source');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['title', 'is_verified']);
        });
    }
};
