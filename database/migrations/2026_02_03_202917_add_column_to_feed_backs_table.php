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
        Schema::table('feed_backs', function (Blueprint $table) {
           $table->foreignId('student_feed_back_time_based')->nullable()->constrained('statuses')->nullOnDelete()->after('activity_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feed_backs', function (Blueprint $table) {
            //
        });
    }
};
