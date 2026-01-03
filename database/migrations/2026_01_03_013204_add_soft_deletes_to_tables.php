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
        // Add soft deletes to employees
        Schema::table('employees', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to departments
        Schema::table('departments', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to rooms
        Schema::table('rooms', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to room_occupancies
        Schema::table('room_occupancies', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('room_occupancies', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
