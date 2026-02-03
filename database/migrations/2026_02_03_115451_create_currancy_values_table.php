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
        Schema::create('currancy_values', function (Blueprint $table) {
            $table->id();
            $table->date('exchange_date');
            $table->decimal('currency_value');
            $table->unique(['exchange_date', 'currency_value']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currancy_values');
    }
};
