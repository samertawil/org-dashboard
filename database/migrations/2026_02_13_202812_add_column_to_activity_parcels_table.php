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
        Schema::table('activity_parcels', function (Blueprint $table) {
           $table->foreignId('unit_id')->nullable()->constrained('statuses')->nullOnDelete()->after('cost_for_each_parcel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_parcels', function (Blueprint $table) {
            //
        });
    }
};
