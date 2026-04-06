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
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_for_section')->nullable()->constrained('statuses')->nullOnDelete();
            $table->tinyInteger('question_order')->nullable();
            $table->string('question_ar_text');
            $table->string('question_en_text')->nullable();
            $table->tinyInteger('min_score')->nullable();
            $table->tinyInteger('max_score')->nullable();
            $table->string('domain')->nullable();
            $table->tinyInteger('answer_input_type'); 
            $table->tinyInteger('require_detail')->default(0); 
            $table->text('detail')->nullable();; 
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};
