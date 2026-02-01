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
        Schema::create('student_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('max_students')->default(0);
            $table->integer('min_students')->default(0);
            $table->integer('current_student_count')->default(0);
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('Moderator')->nullable();
            $table->string('Moderator_phone')->nullable();
            $table->string('Moderator_email')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('student_groups');
    }
};
