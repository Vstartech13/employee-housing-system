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
        Schema::table('room_occupancies', function (Blueprint $table) {
            $table->boolean('is_guest')->default(false)->after('employee_id');
            $table->string('guest_name')->nullable()->after('is_guest');
            $table->string('guest_purpose')->nullable()->after('guest_name');

            // Make employee_id nullable since guests don't have employee_id
            $table->foreignId('employee_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_occupancies', function (Blueprint $table) {
            $table->dropColumn(['is_guest', 'guest_name', 'guest_purpose']);
            $table->foreignId('employee_id')->nullable(false)->change();
        });
    }
};