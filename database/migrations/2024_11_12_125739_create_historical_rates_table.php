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
        Schema::create('historical_rates', function (Blueprint $table) {
            $table->id();
            $table->date('rate_date');
            $table->string('currency', 3);
            $table->decimal('rate', 15, 6);
            $table->timestamps();

            $table->unique(['rate_date', 'currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_rates');
    }
};
