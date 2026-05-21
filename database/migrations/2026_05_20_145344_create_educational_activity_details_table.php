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
        Schema::create('educational_activity_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educational_activity_id')->constrained('educational_activity_schedules')->onDelete('cascade');
            $table->integer('consistent')->nullable();      #منسجم
            $table->text('what_learned')->nullable();       #ماذا تعلمو
            $table->text('teacher_report_detail')->nullable();   #تقرير المعلم عن النشاط
            $table->json('attchments')->nullable();    #المرفقات
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educational_activity_details');
    }
};
