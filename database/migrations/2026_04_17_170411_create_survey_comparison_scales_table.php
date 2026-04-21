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
        Schema::create('survey_comparison_scales', function (Blueprint $table) {
            $table->id();
            $table->decimal('from_percentage', 8, 2);
            $table->decimal('to_percentage', 8, 2);
            $table->string('evaluation');
            $table->string('description')->nullable();
            $table->string('color')->nullable();
            $table->foreignId('domain_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->string('batch_no')->nullable();
            $table->foreignId('survey_for_section')->nullable()->constrained('statuses')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_comparison_scales');
    }
};
