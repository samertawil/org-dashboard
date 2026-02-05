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
        Schema::create('teaching_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->string('Moderator')->nullable();
            $table->string('Moderator_phone')->nullable();
            $table->string('Moderator_email')->nullable();
            $table->foreignId('status')->nullable()->constrained('statuses')->nullOnDelete();
            $table->integer('activation')->default(1);
            $table->decimal('cost_usd', 7, 2)->default(0);
            $table->decimal('cost_nis', 7, 2)->default(0);
            $table->foreignId('partner_id')->nullable()->constrained('partner_institutions')->nullOnDelete();
            $table->string('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_groups');
    }
};
