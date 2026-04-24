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
        Schema::create('purchase_quotation_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_response_id')->constrained('purchase_quotation_responses')->onDelete('cascade');
            $table->foreignId('purchase_requisition_item_id')->constrained('purchase_requisition_items')->onDelete('cascade');
            $table->decimal('offered_price', 15, 2);
            $table->text('vendor_item_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_quotation_prices');
    }
};
