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
        Schema::create('educational_activity_names', function (Blueprint $table) {
            $table->id();
            $table->string('activity_name');
            $table->unsignedBigInteger('activity_domain')->nullable();
            $table->boolean('available_in_active_groups')->default(true);
            $table->text('description')->nullable();
            $table->json('teachers')->nullable();
            $table->tinyInteger('activation')->default(1);
            $table->timestamps();

            // Foreign key to statuses
            $table->foreign('activity_domain')->references('id')->on('statuses')->onDelete('set null');
        });

        // Migrate existing status records with p_id_sub = 197 or id = 197
        $statuses = DB::table('statuses')
            ->where('p_id_sub', 197)
            ->orWhere('id', 197)
            ->get();
        $schedules = DB::table('educational_activity_schedules')->get();

        foreach ($statuses as $status) {
            // Find a schedule using this activity_name status ID to fetch the domain
            $domainId = null;
            foreach ($schedules as $sch) {
                if ($sch->activity_name == $status->id) {
                    $domainId = $sch->educational_activity_domain;
                    break;
                }
            }

            DB::table('educational_activity_names')->insert([
                'id' => $status->id,
                'activity_name' => $status->status_name,
                'activity_domain' => $domainId,
                'available_in_active_groups' => true,
                'activation' => 1,
                'created_at' => $status->created_at ?: now(),
                'updated_at' => $status->updated_at ?: now(),
            ]);
        }

        // Reset the auto-increment starting ID to prevent primary key collision
        $maxId = DB::table('educational_activity_names')->max('id');
        if ($maxId) {
            DB::statement("ALTER TABLE educational_activity_names AUTO_INCREMENT = " . ($maxId + 1));
        }

        // Drop the old foreign key constraint pointing to statuses if it exists
        try {
            Schema::table('educational_activity_schedules', function (Blueprint $table) {
                if (DB::connection()->getDriverName() !== 'sqlite') {
                    $table->dropForeign(['activity_name']);
                }
            });
        } catch (\Throwable $e) {
            // Ignore if constraint does not exist
        }

        // Clean up orphaned activity_name values in educational_activity_schedules
        // that do not exist in educational_activity_names (e.g. ID 197 or other non-existent IDs)
        DB::table('educational_activity_schedules')
            ->whereNotNull('activity_name')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('educational_activity_names')
                    ->whereColumn('educational_activity_names.id', 'educational_activity_schedules.activity_name');
            })
            ->update(['activity_name' => null]);

        // Add foreign key constraint on educational_activity_schedules
        Schema::table('educational_activity_schedules', function (Blueprint $table) {
            $table->foreign('activity_name')
                ->references('id')
                ->on('educational_activity_names');
        });

        // Delete the old status records only after successfully migrating and establishing constraints
        DB::table('statuses')
            ->where('p_id_sub', 197)
            ->orWhere('id', 197)
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('educational_activity_schedules', function (Blueprint $table) {
                if (DB::connection()->getDriverName() !== 'sqlite') {
                    $table->dropForeign(['activity_name']);
                }
            });
        } catch (\Throwable $e) {
            // Ignore if foreign key does not exist
        }

        // Restore the deleted statuses back to statuses table
        if (Schema::hasTable('educational_activity_names')) {
            $activityNames = DB::table('educational_activity_names')->get();
            foreach ($activityNames as $activity) {
                $exists = DB::table('statuses')->where('id', $activity->id)->exists();
                if (!$exists) {
                    DB::table('statuses')->insert([
                        'id' => $activity->id,
                        'status_name' => $activity->activity_name ?? $activity->status_name ?? '',
                        'p_id_sub' => $activity->id == 197 ? null : 197,
                        'created_at' => $activity->created_at ?: now(),
                        'updated_at' => $activity->updated_at ?: now(),
                    ]);
                }
            }
        }

        Schema::dropIfExists('educational_activity_names');
    }
};
