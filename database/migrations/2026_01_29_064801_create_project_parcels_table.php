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
        Schema::create('activity_parcels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('parcel_type')->nullable()->constrained('statuses')->nullOnDelete();  // شخص او عائلة نوع المستفيدين 
            $table->integer('distributed_parcels_count'); //عدد الطرود الموزعة
            $table->decimal('cost_for_each_parcel', 7, 2); // متوسط تكلفة الطرد
            $table->foreignId('status_id')->nullable()->constrained()->nullOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_parcels');
    }
};
