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
        Schema::table('educational_activity_details', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->string('replaced_activity')->nullable(); // نشاط بديل 
            $table->string('replaced_reason')->nullable(); // سبب النشاط البديل 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('educational_activity_details', function (Blueprint $table) {
            //
        });
    }
};
