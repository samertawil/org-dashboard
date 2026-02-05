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
        //   $table->dropColumn('region_id');
          $table->dropColumn('city_id');
          $table->dropColumn('neighbourhood_id');
          $table->dropColumn('location_id');
          $table->dropColumn('address_details');
          $table->dropColumn('start_date');
          $table->dropColumn('end_date');
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
