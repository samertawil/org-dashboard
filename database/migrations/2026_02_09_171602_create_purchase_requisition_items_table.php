<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 
    public function up(): void
    {
        Schema::create('purchase_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_requisition_id')->constrained('purchase_requisitions')->cascadeOnDelete();
            $table->tinyInteger('line_number')->increments();// رقم السطر داخل الطلب
            $table->string('item_name');
            $table->string('item_description')->nullable();
            $table->integer('quantity');
            $table->foreignId('unit_id')->nullable()->constrained('statuses')->nullOnDelete(); //litter, KG, piece
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->tinyInteger('currency')->nullable(); //  العملة للصنف  
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();

            $table->timestamps();
        });
    }

 
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_items');
    }
};
