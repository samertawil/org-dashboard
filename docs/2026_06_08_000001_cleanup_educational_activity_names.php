<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $activities = DB::table('educational_activity_names')->get();

        $groups = [];
        foreach ($activities as $activity) {
            $norm = $this->normalizeName($activity->activity_name);
            $groups[$norm][] = $activity;
        }

        foreach ($groups as $normName => $items) {
            if (count($items) > 1) {
                // Determine the primary item to keep
                $primaryItem = null;
                $maxSchedules = -1;

                foreach ($items as $item) {
                    $scheduleCount = DB::table('educational_activity_schedules')->where('activity_name', $item->id)->count();
                    if ($scheduleCount > $maxSchedules) {
                        $maxSchedules = $scheduleCount;
                        $primaryItem = $item;
                    }
                }

                // If no primary item found (should not happen), take the first one
                if (!$primaryItem) {
                    $primaryItem = $items[0];
                }

                // Update the primary item's name to the normalized name
                DB::table('educational_activity_names')
                    ->where('id', $primaryItem->id)
                    ->update(['activity_name' => $normName]);

                // Merge metadata from duplicates into primary
                foreach ($items as $item) {
                    if ($item->id !== $primaryItem->id) {
                        $this->mergeMetadata($primaryItem, $item);

                        // Update references in schedules
                        DB::table('educational_activity_schedules')
                            ->where('activity_name', $item->id)
                            ->update(['activity_name' => $primaryItem->id]);

                        // Delete the duplicate
                        DB::table('educational_activity_names')
                            ->where('id', $item->id)
                            ->delete();
                    }
                }
            } else {
                // Single item, just normalize name
                $item = $items[0];
                DB::table('educational_activity_names')
                    ->where('id', $item->id)
                    ->update(['activity_name' => $normName]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversing data normalization and deduplication is not fully reversible 
        // without keeping a backup mapping of original IDs.
    }

    private function normalizeName(string $name): string
    {
        // 1. Replace multiple spaces/tabs/newlines with a single space
        $name = preg_replace('/\s+/', ' ', $name);

        // 2. Trim leading/trailing spaces and special characters
        $name = trim($name, " \t\n\r\0\x0B-_–—.");

        // 3. Normalize internal separators (dashes, underscores, en-dashes, em-dashes) to " - "
        $name = preg_replace('/\s*[_\-–—]+\s*/u', ' - ', $name);

        // 4. Fix common typos and standardize Arabic spelling
        $replacements = [
            'الاولى' => 'الأولى',
            'االخامسة' => 'الخامسة',
            'حوف الجر' => 'حروف الجر',
            'مشارعي' => 'مشاعري',
            'حوف' => 'حروف',
        ];

        foreach ($replacements as $old => $new) {
            $name = str_replace($old, $new, $name);
        }

        // Clean up spaces again
        $name = preg_replace('/\s+/', ' ', $name);
        $name = trim($name);

        return $name;
    }

    private function mergeMetadata($primary, $duplicate): void
    {
        $updates = [];

        // Merge domain
        if (empty($primary->activity_domain) && !empty($duplicate->activity_domain)) {
            $updates['activity_domain'] = $duplicate->activity_domain;
            $primary->activity_domain = $duplicate->activity_domain;
        }

        // Merge description
        if (empty($primary->description) && !empty($duplicate->description)) {
            $updates['description'] = $duplicate->description;
            $primary->description = $duplicate->description;
        } elseif (!empty($primary->description) && !empty($duplicate->description) && stripos($primary->description, $duplicate->description) === false) {
            $updates['description'] = $primary->description . "\n" . $duplicate->description;
            $primary->description = $updates['description'];
        }

        // Merge teachers
        $primaryTeachers = json_decode($primary->teachers, true) ?: [];
        $duplicateTeachers = json_decode($duplicate->teachers, true) ?: [];
        if (!empty($duplicateTeachers)) {
            $mergedTeachers = array_values(array_unique(array_merge($primaryTeachers, $duplicateTeachers)));
            $updates['teachers'] = json_encode($mergedTeachers);
            $primary->teachers = json_encode($mergedTeachers);
        }

        // Merge activation
        if ($primary->activation == 0 && $duplicate->activation == 1) {
            $updates['activation'] = 1;
            $primary->activation = 1;
        }

        if (!empty($updates)) {
            DB::table('educational_activity_names')
                ->where('id', $primary->id)
                ->update($updates);
        }
    }
};
