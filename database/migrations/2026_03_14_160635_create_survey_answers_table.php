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
        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->nullable();
            $table->integer('survey_no');
            $table->foreignId('question_id')->nullable()->constrained('survey_questions')->nullOnDelete();
            $table->string('answer_ar_text')->nullable();
            $table->string('answer_en_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
    }
};
