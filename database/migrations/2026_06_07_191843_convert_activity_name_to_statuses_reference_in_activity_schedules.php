<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Get unique activity names from educational_activity_schedules
        $uniqueNames = DB::table('educational_activity_schedules')
            ->whereNotNull('activity_name')
            ->where('activity_name', '!=', '')
            ->distinct()
            ->pluck('activity_name')
            ->map(fn($name) => trim($name))
            ->filter()
            ->unique();

        // 2. Insert unique names into the statuses table under p_id_sub = 197 if they do not exist
        $nameToIdMap = [];
        foreach ($uniqueNames as $name) {
            $status = DB::table('statuses')
                ->where('p_id_sub', 197)
                ->where('status_name', $name)
                ->first();

            if ($status) {
                $nameToIdMap[$name] = $status->id;
            } else {
                $id = DB::table('statuses')->insertGetId([
                    'status_name' => $name,
                    'p_id_sub' => 197,
                    'used_in_system_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $nameToIdMap[$name] = $id;
            }
        }

        // Forget the status cache since we added new ones
        Cache::forget('statuses-all');

        // 3. Add temporary unsignedBigInteger column to educational_activity_schedules
        Schema::table('educational_activity_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('temp_activity_name')->nullable();
        });

        // 4. Map existing rows to new status IDs
        $schedules = DB::table('educational_activity_schedules')->select('id', 'activity_name')->get();
        foreach ($schedules as $schedule) {
            $trimmedName = trim($schedule->activity_name);
            if (isset($nameToIdMap[$trimmedName])) {
                DB::table('educational_activity_schedules')
                    ->where('id', $schedule->id)
                    ->update(['temp_activity_name' => $nameToIdMap[$trimmedName]]);
            }
        }

        // 5. Drop the old string column
        Schema::table('educational_activity_schedules', function (Blueprint $table) {
            $table->dropColumn('activity_name');
        });

        // 6. Rename the temporary column to activity_name
        Schema::table('educational_activity_schedules', function (Blueprint $table) {
            $table->renameColumn('temp_activity_name', 'activity_name');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add temp string column if it doesn't exist
        if (!Schema::hasColumn('educational_activity_schedules', 'temp_activity_name')) {
            Schema::table('educational_activity_schedules', function (Blueprint $table) {
                $table->string('temp_activity_name')->nullable();
            });
        }

        // 2. Fetch all statuses for p_id_sub = 197
        $statuses = DB::table('statuses')
            ->where('p_id_sub', 197)
            ->pluck('status_name', 'id')
            ->toArray();

        // 3. Update temp string column with the status name
        if (Schema::hasColumn('educational_activity_schedules', 'activity_name')) {
            $schedules = DB::table('educational_activity_schedules')->select('id', 'activity_name')->get();
            foreach ($schedules as $schedule) {
                if ($schedule->activity_name && isset($statuses[$schedule->activity_name])) {
                    DB::table('educational_activity_schedules')
                        ->where('id', $schedule->id)
                        ->update(['temp_activity_name' => $statuses[$schedule->activity_name]]);
                }
            }
        }

        // 4. Drop the ID column
        if (Schema::hasColumn('educational_activity_schedules', 'activity_name')) {
            try {
                Schema::table('educational_activity_schedules', function (Blueprint $table) {
                    $table->dropForeign('educational_activity_schedules_activity_name_foreign');
                });
            } catch (\Throwable $e) {
                // Ignore if foreign key constraint does not exist
            }

            Schema::table('educational_activity_schedules', function (Blueprint $table) {
                $table->dropColumn('activity_name');
            });
        }

        // 5. Rename temp column to activity_name
        if (Schema::hasColumn('educational_activity_schedules', 'temp_activity_name') && !Schema::hasColumn('educational_activity_schedules', 'activity_name')) {
            Schema::table('educational_activity_schedules', function (Blueprint $table) {
                $table->renameColumn('temp_activity_name', 'activity_name');
            });
        }

        // 6. Make activity_name non-nullable as it was originally
        if (Schema::hasColumn('educational_activity_schedules', 'activity_name')) {
            DB::table('educational_activity_schedules')
                ->whereNull('activity_name')
                ->update(['activity_name' => '']);

            Schema::table('educational_activity_schedules', function (Blueprint $table) {
                $table->string('activity_name')->nullable(false)->change();
            });
        }

        // Forget the status cache
        Cache::forget('statuses-all');
    }
};
