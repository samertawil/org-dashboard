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
        Schema::table('educational_activity_schedules', function (Blueprint $table) {
            $table->unique(['group_id', 'period_start', 'educational_period_groups'], 'act_sched_grp_start_edu_grp_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('educational_activity_schedules', function (Blueprint $table) {
            $table->dropUnique('act_sched_grp_start_edu_grp_unique');
        });
    }
};
