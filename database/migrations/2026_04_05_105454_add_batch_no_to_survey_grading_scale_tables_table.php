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
        Schema::table('survey_grading_scale_tables', function (Blueprint $table) {
               $table->foreignId('survey_for_section')->nullable()->constrained('statuses')->after('type')->nullOnDelete;
                 $table->tinyInteger('batch_no')->unsigned()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_grading_scale_tables', function (Blueprint $table) {
            $table->dropColumn('survey_for_section');
            $table->dropColumn('batch_no');
        });
    }
};
