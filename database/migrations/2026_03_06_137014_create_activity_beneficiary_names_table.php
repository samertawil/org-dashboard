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
        // الاسماء المستفيدين من النشاط
        Schema::create('activity_beneficiary_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained();
            $table->foreignId('displacement_camps_id')->nullable()->constrained('displacement_camps');
            $table->integer('identity_number');
            $table->string('full_name');
            $table->string('phone',15)->nullable();  
            $table->date('receipt_date'); // تاريخ استلام المساعدة
            $table->foreignId('receive_method')->nullable()->constrained('statuses'); // طريقة استلام المساعدة
            $table->string('receive_by_name')->nullable(); // اسم المستلم بحال كان شخص غير المستفيد هو من استلم المساعدة
            $table->unique(['activity_id', 'identity_number']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_beneficiary_names');
    }
};
