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
        Schema::table('teaching_groups', function (Blueprint $table) {
            if (Schema::hasColumn('teaching_groups', 'region_id')) {
                $table->dropColumn('region_id');
            }
            if (Schema::hasColumn('teaching_groups', 'city_id')) {
                $table->dropColumn('city_id');
            }
            if (Schema::hasColumn('teaching_groups', 'neighbourhood_id')) {
                $table->dropColumn('neighbourhood_id');
            }
            if (Schema::hasColumn('teaching_groups', 'location_id')) {
                $table->dropColumn('location_id');
            }
            if (Schema::hasColumn('teaching_groups', 'address_details')) {
                $table->dropColumn('address_details');
            }
            if (Schema::hasColumn('teaching_groups', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('teaching_groups', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teaching_groups', function (Blueprint $table) {
            //
        });
    }
};
