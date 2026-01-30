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
        Schema::create('activity_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('beneficiary_type')->nullable()->constrained('statuses')->nullOnDelete();  // شخص او عائلة نوع المستفيدين
            $table->integer('beneficiaries_count'); // اعداد المستفيدين
            $table->decimal('cost_for_each_beneficiary', 7, 2); // متوسط التكلفة للمستفيد
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
            
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_beneficiaries');
    }
};
