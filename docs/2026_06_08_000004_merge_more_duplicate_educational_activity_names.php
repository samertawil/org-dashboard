<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Merge ID 227 (Eالمهارة الثالثة - الحروف والأصوات) into ID 222 (E الجلسة الثالثة - الحروف والأصوات)
        if (DB::table('educational_activity_names')->where('id', 227)->exists() &&
            DB::table('educational_activity_names')->where('id', 222)->exists()) {
            
            // Re-point schedules
            DB::table('educational_activity_schedules')
                ->where('activity_name', 227)
                ->update(['activity_name' => 222]);

            // Delete ID 227
            DB::table('educational_activity_names')
                ->where('id', 227)
                ->delete();
        }

        // 2. Merge ID 228 (المهارة الثالثة - استكشاف الهوية الحقيقة من انا) into ID 223 (الجلسة الثالثة - استكشاف الهوية الحقيقة من انا)
        if (DB::table('educational_activity_names')->where('id', 228)->exists() &&
            DB::table('educational_activity_names')->where('id', 223)->exists()) {

            // Re-point schedules
            DB::table('educational_activity_schedules')
                ->where('activity_name', 228)
                ->update(['activity_name' => 223]);

            // Delete ID 228
            DB::table('educational_activity_names')
                ->where('id', 228)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One-way migration. Merges cannot be undone automatically.
    }
};
