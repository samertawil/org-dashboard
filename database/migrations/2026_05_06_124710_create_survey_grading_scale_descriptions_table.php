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
        Schema::create('survey_grading_scale_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained('statuses')->onDelete('cascade');
            $table->foreignId('survey_grading_scale_id')->constrained('survey_grading_scale_tables', indexName: 'sgs_desc_scale_id_foreign')->onDelete('cascade');
            $table->text('description');
            $table->text('need_processing')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_grading_scale_descriptions');
    }
};
