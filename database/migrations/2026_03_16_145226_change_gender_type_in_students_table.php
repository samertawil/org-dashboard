<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('students')->where('gender', 'female')->update(['gender' => '3']);
        DB::table('students')->where('gender', 'male')->update(['gender' => '2']);

        Schema::table('students', function (Blueprint $table) {
            $table->integer('gender')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('gender')->change();
        });

        DB::table('students')->where('gender', '3')->update(['gender' => 'female']);
        DB::table('students')->where('gender', '2')->update(['gender' => 'male']);
    }
};
