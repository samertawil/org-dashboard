<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teaching_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('teaching_groups', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('teaching_groups', 'end_date')) {
                $table->date('end_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('teaching_groups', function (Blueprint $table) {
            if (Schema::hasColumn('teaching_groups', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('teaching_groups', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });
    }
};
