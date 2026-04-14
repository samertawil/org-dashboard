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
        Schema::create('survey_table', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('for_id')->nullable();
            $table->integer('from_age')->nullable();
            $table->integer('to_age')->nullable();
            $table->integer('survey_for_section');
       
            $table->string('survey_name', 255)->nullable();
            $table->integer('semester')->nullable();
            
            $table->index('semester', 'idx_semester');
            $table->index('survey_for_section', 'idx_survey_for_section');
            $table->index('survey_target', 'idx_survey_target');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_table');
    }
};
