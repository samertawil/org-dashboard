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
        // اسماء سكان المخيم
        Schema::create('displacement_camp_residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('displacement_camp_id')->constrained('displacement_camps');      
            $table->foreignId('resident_type')->constrained('statuses'); // Beneficiaries Types;
            $table->integer('identity_number')->unique();
            $table->string('full_name');
            $table->date('birth_date')->nullable();
            $table->string('phone',15)->nullable();
            $table->tinyInteger('gender')->nullable()->unsigned()->after('phone');
            $table->integer('activation')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('displacement_camp_residents');
    }
};
