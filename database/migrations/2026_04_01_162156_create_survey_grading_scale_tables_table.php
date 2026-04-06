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
        Schema::create('survey_grading_scale_tables', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('from_percentage')->unsigned()->min(0)->max(100);
            $table->tinyInteger('to_percentage')->unsigned()->min(0)->max(100);
            $table->string('evaluation');
            $table->string('description');
            $table->foreignId('type')->nullable()->constrained('statuses')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_grading_scale_tables');
    }
};
