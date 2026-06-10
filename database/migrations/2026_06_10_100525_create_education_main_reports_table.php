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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_name');
            $table->foreignId('report_period_type')->constrained('statuses');  // زمن التقرير سواء كان شهري او ربع سنوي او 
            $table->foreignId('report_main_type')->constrained('statuses'); // نوع التقرير مثلا تقرير عن الانشطة او غيره
            $table->date('report_date'); // تاريخ التقرير 
            $table->date('date_from'); // تاريخ التقرير من 
            $table->date('date_to'); // تاريخ التقرير الى 
            $table->tinyInteger('batch_no')->nullable(); // رقم الدفعة 
            $table->json('student_group_ids')->nullable(); // النقاط التعليمية
            $table->foreignId('employee_id')->constrained('employees'); // من انشأ التقرير 
            $table->foreignId('required_from')->nullable()->constrained('statuses'); // التقرير مطلوب من 
            $table->json('addressed_to_dept_types'); // التقرير موجه الى مثلا مدير مركز او غيره
            $table->foreignId('addressed_to_employees')->constrained('employees'); // الموظفين التقرير موجه الى 
            $table->json('follow_up_by')->nullable(); // نسخة من التقرير الي  
            $table->json('covered_educational_activities_ids')->nullable(); // الانشطة المغطاة في التقرير
            $table->json('covered_educational_activity_schedules_ids')->nullable(); // جداول الانشطة المغطاة في التقرير
            $table->json('covered_educational_activity_details_ids')->nullable(); // تفاصيل الانشطة المغطاة في التقرير
            $table->string('note')->nullable(); // ملاحظات التقرير 
            $table->timestamps();
        });


        Schema::create('report_body', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->smallInteger('item_order')->nullable(); // ترتيب الصفح (1, 2, 3...)
            $table->text('content'); // محتوى التقرير
            $table->text('observation')->nullable(); // ملاحظة على البند هذا
            $table->foreignId('status_id')->nullable()->constrained('statuses');
            $table->json('attachments')->nullable(); // مرفقات التقرير
            $table->json('report_body_attachments')->nullable(); // مرفقات التقرير
            $table->timestamps();
        });


        Schema::create('report_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('employee_respondent_id')->constrained('employees'); // الموظف الذي رد
            $table->date('response_date'); // تاريخ الرد
            $table->foreignId('response_type_id')->nullable()->constrained('statuses'); // نوع الرد:_APPROVAL, REJECTION, QUESTION, COMMENT, ACTION
            $table->text('message'); // نص الرد/الملاحظة
            $table->json('follow_up_by')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_main_reports');
    }
};
