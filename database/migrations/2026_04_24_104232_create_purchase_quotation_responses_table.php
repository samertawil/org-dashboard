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
        Schema::create('purchase_quotation_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_requisition_id')->constrained('purchase_requisitions')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('partner_institutions')->onDelete('cascade');
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('statuses');
            $table->foreignId('status_id')->nullable()->constrained('statuses');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_quotation_responses');
    }
};
