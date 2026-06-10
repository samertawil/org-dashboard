<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Merge duplicate educational activity names by core content.
 *
 * Known duplicate: ID 218 and ID 221 have the same core content
 * "المهارة الثالثة التعرف على الحروف".
 *
 * Strategy:
 * 1. Re-point all schedules referencing ID 221 → ID 218
 * 2. Normalize the surviving record's name (remove "(Hello abc)" prefix)
 * 3. Delete the duplicate record (ID 221)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Find all activity names grouped by core content
        $records = DB::table('educational_activity_names')->get(['id', 'activity_name']);

        $groups = [];
        foreach ($records as $record) {
            $core = $this->extractCoreName($record->activity_name);
            $groups[$core][] = $record;
        }

        // Process groups with duplicates
        foreach ($groups as $core => $duplicates) {
            if (count($duplicates) <= 1) {
                continue;
            }

            // Keep the record with the lowest ID as the "canonical" one
            usort($duplicates, fn($a, $b) => $a->id - $b->id);
            $canonical = $duplicates[0];

            // Normalize the canonical record's name (strip parenthetical prefixes etc.)
            $normalizedName = $this->normalizeName($canonical->activity_name);

            // Remove parenthetical content from the canonical name too
            $cleanName = preg_replace('/\([^)]*\)/', '', $normalizedName);
            $cleanName = $this->normalizeName($cleanName);

            DB::table('educational_activity_names')
                ->where('id', $canonical->id)
                ->update(['activity_name' => $cleanName]);

            // Merge all others into the canonical
            for ($i = 1; $i < count($duplicates); $i++) {
                $duplicate = $duplicates[$i];

                // Re-point schedules
                DB::table('educational_activity_schedules')
                    ->where('activity_name', $duplicate->id)
                    ->update(['activity_name' => $canonical->id]);

                // Delete the duplicate
                DB::table('educational_activity_names')
                    ->where('id', $duplicate->id)
                    ->delete();
            }
        }
    }

    public function down(): void
    {
        // This migration is not reversible — the duplicate data is cleaned up
    }

    private function normalizeName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/\s*[-_]+\s*/', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }

    private function extractCoreName(string $name): string
    {
        $name = preg_replace('/\([^)]*\)/', '', $name);
        $name = $this->normalizeName($name);
        return mb_strtolower($name, 'UTF-8');
    }
};
