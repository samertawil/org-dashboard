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
        Schema::create('activity_work_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('employee_mission_title')->nullable()->constrained('statuses')->nullOnDelete(); // نوع مهمة الموظف مثل مشرف او عامل مساعد
            $table->foreignId('employee_id')->constrained('employees');  //الموظف    
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
        Schema::dropIfExists('activity_work_teams');
    }
};
