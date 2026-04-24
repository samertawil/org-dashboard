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
            $table->foreignId('purchase_requisition_id')->nullable()->after('notes')->constrained('purchase_requisitions')->nullOnDelete();
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
