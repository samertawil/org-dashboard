<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Root Status for Report Period Type
        $periodTypeId = DB::table('statuses')->insertGetId([
            'status_name' => 'Report Period Type',
            'p_id_sub' => null,
            'route_system_name' => 'report_period_type',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create children
        DB::table('statuses')->insert([
            [
                'status_name' => 'شهري (Monthly)',
                'p_id_sub' => $periodTypeId,
                'route_system_name' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'ربع سنوي (Quarterly)',
                'p_id_sub' => $periodTypeId,
                'route_system_name' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'سنوي (Annual)',
                'p_id_sub' => $periodTypeId,
                'route_system_name' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. Create Root Status for Report Main Type
        $mainTypeId = DB::table('statuses')->insertGetId([
            'status_name' => 'Report Main Type',
            'p_id_sub' => null,
            'route_system_name' => 'report_main_type',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create children
        DB::table('statuses')->insert([
            [
                'status_name' => 'تقرير الأنشطة (Activities Report)',
                'p_id_sub' => $mainTypeId,
                'route_system_name' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'تقرير إداري (Administrative Report)',
                'p_id_sub' => $mainTypeId,
                'route_system_name' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Cache::forget('statuses-all');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Find roots
        $periodType = DB::table('statuses')->where('route_system_name', 'report_period_type')->first();
        if ($periodType) {
            DB::table('statuses')->where('p_id_sub', $periodType->id)->delete();
            DB::table('statuses')->where('id', $periodType->id)->delete();
        }

        $mainType = DB::table('statuses')->where('route_system_name', 'report_main_type')->first();
        if ($mainType) {
            DB::table('statuses')->where('p_id_sub', $mainType->id)->delete();
            DB::table('statuses')->where('id', $mainType->id)->delete();
        }

        Cache::forget('statuses-all');
    }
};
