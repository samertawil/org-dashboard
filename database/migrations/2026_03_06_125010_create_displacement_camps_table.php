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
        // اسماء المخيمات
        Schema::create('displacement_camps', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();

            $table->foreignId('region_id')->constrained();
            $table->foreignId('city_id')->nullable()->constrained();
            $table->foreignId('neighbourhood_id')->nullable()->constrained();
            $table->foreignId('location_id')->nullable()->constrained();
            $table->string('address_details')->nullable();
            $table->string('longitudes')->nullable(); // خطوط الطول
            $table->string('latitude')->nullable(); // خطوط العرض

            $table->integer('number_of_families')->nullable();
            $table->integer('number_of_individuals')->nullable();

            $table->string('Moderator')->nullable();
            $table->string('Moderator_phone')->nullable();
          
            $table->json('camp_main_needs')->nullable();
            $table->json('attchments')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('displacement_camps');
    }
};
