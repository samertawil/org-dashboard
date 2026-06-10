<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Helper to extract core name for semantic duplicate detection
        $extractCoreName = function (string $name): string {
            // Remove anything inside parentheses
            $name = preg_replace('/\([^)]*\)/', '', $name);

            // Replace tabs with space
            $name = str_replace("\t", ' ', $name);

            // Replace any dash (hyphen, en-dash, em-dash), underscore, or dot optionally surrounded by spaces with a space
            $name = preg_replace('/\s*[-_–—.]+\s*/u', ' ', $name);

            // Strip prefixes: (المهارة / المهارة / الجلسة / الجلسه / مهارة / جلسة) + (الأولى/الثانية/... or digits)
            $ordinals = '(الأولى|الاولى|الثانية|الثانيه|الثالثة|الثالثه|الرابعة|الرابعه|الخامسة|الخامسه|السادسة|السادسه|السابعة|السابعه|الثامنة|الثامنه|التاسعة|التاسعه|العاشرة|العاشره|الاول|الأول|الثاني|الثالث|الرابع|الخامس|السادس|السابع|الثامن|التاسع|العاشر|\d+)';
            $prefixPattern = '/(المهارة|المهاره|المهارات|المهاراه|الجلسة|الجلسه|جلسة|جلسه|مهارة|مهاره)\s+' . $ordinals . '/u';
            $name = preg_replace($prefixPattern, ' ', $name);

            // Collapse multiple spaces into one
            $name = preg_replace('/\s+/', ' ', $name);

            return trim(mb_strtolower($name, 'UTF-8'));
        };

        // 1. Fetch all existing educational activity names
        $activities = DB::table('educational_activity_names')->get();

        // 2. Group them by their core/semantic name
        $groups = $activities->groupBy(function ($item) use ($extractCoreName) {
            return $extractCoreName($item->activity_name);
        });

        foreach ($groups as $coreName => $group) {
            // Ignore empty names
            if ($coreName === '') {
                continue;
            }

            if ($group->count() > 1) {
                // Keep the record with the lowest ID
                $sorted = $group->sortBy('id');
                $keep = $sorted->first();
                $duplicates = $sorted->slice(1);

                $duplicateIds = $duplicates->pluck('id')->toArray();
                $keepId = $keep->id;

                // Update educational_activity_schedules to point to the kept activity name ID
                DB::table('educational_activity_schedules')
                    ->whereIn('activity_name', $duplicateIds)
                    ->update(['activity_name' => $keepId]);

                // Delete the duplicate activity names
                DB::table('educational_activity_names')
                    ->whereIn('id', $duplicateIds)
                    ->delete();
            }
        }

        // 3. Try adding the unique index on activity_name if it doesn't already exist
        try {
            Schema::table('educational_activity_names', function (Blueprint $table) {
                $table->unique('activity_name');
            });
        } catch (\Throwable $e) {
            // Ignore if index already exists
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
