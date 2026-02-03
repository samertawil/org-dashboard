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
            $table->foreignId('activity_id')->nullable()->change();
            $table->foreignId('subject_learning_id')->nullable()->constrained('student_subject_for_learns')->nullOnDelete()->after('activity_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_attchments', function (Blueprint $table) {
            //
        });
    }
};
