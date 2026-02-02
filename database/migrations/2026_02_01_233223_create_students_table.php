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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->integer('identity_number')->unique();
            $table->string('full_name');
            $table->date('birth_date');
            $table->foreignId('student_groups_id')->nullable()->constrained('student_groups')->nullOnDelete();
            $table->string('gender');
            $table->integer('activation')->default(1);
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->string('parent_phone')->nullable();
            $table->foreignId('living_parent_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
