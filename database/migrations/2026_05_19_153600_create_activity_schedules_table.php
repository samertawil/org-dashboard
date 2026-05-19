<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول الجدول الزمني للأنشطة التعليمية للأطفال
     * مشروع التعلم والتعافي النفسي الاجتماعي - مرفق رقم: خطة الأنشطة الانتعاشية والدعم النفسي
     */
    public function up(): void
    {
        Schema::create('educational_activity_schedules', function (Blueprint $table) {
            $table->id();

            // ربط بالنشاط الرئيسي
            $table->foreignId('activity_id')->nullable()->constrained('activities')->nullOnDelete();

             $table->foreignId('group_id')->nullable()->constrained('student_groups')->nullOnDelete();

            // مجال النشاط (تدريب / استقبال الأطفال / تهيئة المكان / التعليم / الدعم النفسي)

            $table->foreignId('educational_activity_domain')->nullable()->references('id')->on('statuses')->nullOnDelete()->name('eas_activity_domain_fk');

            // الفئة المستهدفة (فريق العمل / الأطفال)
            $table->string('target_category', 100)->nullable();

            // اسم النشاط / وصفه
            $table->string('activity_name');
            $table->text('activity_description')->nullable();

            // الفترة الزمنية الأولى  8:30 - 10:00
            $table->dateTime('period_start') ;
            $table->dateTime('period_end') ;
            // المجموعات المعينة للفترة الأولى (مجموعة A, B, C, D, E, F)
                     // مثال: "A,B" أو "مجموعة 1,مجموعة 2"
            $table->foreignId('educational_period_groups')->nullable()->references('id')->on('statuses')->nullOnDelete()->name('eas_period_groups_fk');
          
   
            $table->text('notes')->nullable();        
            $table->integer('sort_order')->default(0);      
            $table->integer('activation');  
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();  
           
            // سجلات المستخدمين
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // فهارس لتسريع البحث
            $table->index('period_start');
            $table->index('period_end');
            $table->index('group_id');
            $table->index('activity_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_activity_schedules');
    }
};
