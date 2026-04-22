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
        Schema::create('purchase_requisitions', function (Blueprint $table) { // طلب شراء 
            $table->id();
            $table->mediumInteger('request_number');
            $table->date('request_date'); 
            $table->string('description')->nullable();    // وصف عام عن التوريد المطلوب
            $table->string('justification')->nullable();    // لماذا نحتاج الي هذا التوريد   اذكر مبررات
            $table->json('suggested_vendor_ids')->nullable();
            $table->date('need_by_date')->nullable(); //  متى نحتاج الي هذا التوريد
            $table->string('budget_details')->nullable(); //  من اين الميزانية التي سيتم بها الشراء
            $table->double('estimated_total_dollar')->nullable(); // السعر بالدولار
          
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete(); //draft, submitted, under_review, approved, rejected,converted_to_po
            $table->json('attachments')->nullable(); //  مرفقات مثل عروض الأسعار أو الموافقات أو أي مستندات داعمة أخرى
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisitions');
    }
};
