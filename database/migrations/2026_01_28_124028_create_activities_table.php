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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignId('sector_id')->nullable()->constrained('statuses')->nullOnDelete();;
            $table->foreignId('specific_of_sector')->nullable()->constrained('statuses')->nullOnDelete();;
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('region')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('city')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('neighbourhood')->nullable()->constrained('neighbourhoods')->nullOnDelete();
            $table->foreignId('location')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('address_details')->nullable();
            $table->float('cost')->default(0);
            $table->foreignId('status')->constrained('statuses');
            $table->string('status_name')->nullable();
       
            $table->enum('activation', [0,1]);
            $table->string('activation_name')->nullable();

            $table->timestamps();
        });
    }
    // ['planned', 'in_progress', 'completed', 'on_hold']
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activites');
    }
};
