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
        if (!Schema::hasTable('statuses')) {
            Schema::create('statuses', function (Blueprint $table) {
                $table->id();
                $table->string('status_name');
                $table->integer('p_id')->nullable();
                $table->integer('p_id_sub')->nullable();
                $table->string('route_system_name')->nullable();
                $table->string('description')->nullable();
                $table->integer('used_in_system_id')->nullable();
                $table->integer('c_id_sub')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
