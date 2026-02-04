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
        Schema::rename('work_schedules', 'student_group_schedules');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('student_group_schedules', 'work_schedules');
    }
};
