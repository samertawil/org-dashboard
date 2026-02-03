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
        Schema::table('activity_attchments', function (Blueprint $table) {
            $table->dropForeign(['subject_learning_id']);
            $table->foreign('subject_learning_id')
              ->references('id')
              ->on('student_subject_for_learns')
              ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('your_table_name', function (Blueprint $table) {
            //
        });
    }
};
