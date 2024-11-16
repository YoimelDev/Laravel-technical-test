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
        Schema::table('historical_rates', function (Blueprint $table) {
            $table->decimal('rate', 15, 8)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historical_rates', function (Blueprint $table) {
            $table->decimal('rate', 15, 6)->change();
        });
    }
};
