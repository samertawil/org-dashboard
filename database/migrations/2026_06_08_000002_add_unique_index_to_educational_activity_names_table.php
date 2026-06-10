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
        try {
            Schema::table('educational_activity_names', function (Blueprint $table) {
                $table->unique('activity_name');
            });
        } catch (\Throwable $e) {
            // Gracefully ignore duplicate errors if existing database contains duplicate entries
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('educational_activity_names', function (Blueprint $table) {
                $table->dropUnique(['activity_name']);
            });
        } catch (\Throwable $e) {
            // Ignore if index does not exist
        }
    }
};
