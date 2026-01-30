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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number')->unique();
            $table->string('full_name')->unique();
            $table->enum('gender', [2, 3]);
            $table->date('date_of_birth')->nullable();
            $table->foreignId('marital_status')->nullable()->constrained('statuses')->nullOnDelete(); // الحالة الاجتماعية
            $table->string('phone')->nullable();
            $table->foreignId('regions')->nullable()->constrained('statuses')->nullOnDelete(); //  المدينة
          
            $table->string('email')->nullable()->unique();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('type_of_employee_hire')->nullable()->constrained('statuses')->nullOnDelete(); // نوع التوظيف  عقد ام مثبت  الخ
            $table->date('date_of_joining')->nullable();
            $table->foreignId('position')->nullable()->constrained('statuses')->nullOnDelete(); // الوظيفة
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
          
            $table->enum('activation', [0, 1]); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
