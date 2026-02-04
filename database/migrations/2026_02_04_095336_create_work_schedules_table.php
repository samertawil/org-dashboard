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
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('schedule_date');
            $table->string('name')->nullable(); // e.g., "Standard Week"
            $table->string('day', 20); // Sunday, Monday, etc.
            $table->time('start_time'); // e.g., 08:00:00
            $table->time('end_time'); // e.g., 16:00:00
            $table->integer('hours')->default(8); // Calculated or manual
            $table->boolean('is_off_day')->default(false);
            $table->boolean('is_half_day')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('student_group_id')->nullable()->constrained('student_groups')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('activation')->default(1);
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
