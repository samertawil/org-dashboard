<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('regions')) {
            Schema::create('regions', function (Blueprint $table) {
                $table->id();
                $table->string('region_name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table) {
                $table->id();
                $table->string('city_name');
                $table->foreignId('region_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('neighbourhoods')) {
            Schema::create('neighbourhoods', function (Blueprint $table) {
                $table->id();
                $table->string('neighbourhood_name');
                $table->foreignId('city_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('locations')) {
            Schema::create('locations', function (Blueprint $table) {
                $table->id();
                $table->string('location_name');
                $table->foreignId('neighbourhood_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
        Schema::dropIfExists('neighbourhoods');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('regions');
    }
};
