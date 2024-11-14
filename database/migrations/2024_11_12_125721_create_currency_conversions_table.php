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
        Schema::create('currency_conversions', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency', 3);
            $table->string('to_currency', 3);
            $table->decimal('amount', 15, 6);
            $table->decimal('converted_amount', 15, 6);
            $table->decimal('rate', 15, 6);
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamp('conversion_date');
            $table->timestamps();

            $table->index(['from_currency', 'to_currency', 'conversion_date'], 'currency_conversion_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_conversions');
    }
};
