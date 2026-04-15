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
        Schema::table('survey_table', function (Blueprint $table) {
          $table->text('conditions')->nullable()->after('survey_target');
          $table->text('notes')->nullable()->after('survey_target');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_table', function (Blueprint $table) {
            //
        });
    }
};
