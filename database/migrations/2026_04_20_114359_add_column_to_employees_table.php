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
        Schema::table('employees', function (Blueprint $table) {
          $table->foreignId('employee_in_partner_id')->nullable()->constrained('partner_institutions')->nullOnDelete()->after('email');  
          $table->foreignId('job_title')->nullable()->constrained('statuses')->nullOnDelete()->after('hire_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
};
