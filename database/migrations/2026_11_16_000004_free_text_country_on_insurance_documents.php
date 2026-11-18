<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** The client's country is now typed freely (any country), not picked from config/countries.php. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('insurance_documents', function (Blueprint $table) {
            $table->string('country', 80)->change();
        });

        foreach (config('countries') as $code => $country) {
            DB::table('insurance_documents')->where('country', $code)->update(['country' => $country['name']]);
        }
    }

    public function down(): void
    {
        // Free-text names cannot be shortened back safely; keep the wider column.
    }
};
