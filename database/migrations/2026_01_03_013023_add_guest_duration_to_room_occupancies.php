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
            $table->integer('guest_duration_days')->nullable()->after('guest_purpose')->comment('Duration in days for guests only');
            $table->date('estimated_checkout_date')->nullable()->after('guest_duration_days')->comment('Auto-calculated checkout date for guests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_occupancies', function (Blueprint $table) {
            $table->dropColumn(['guest_duration_days', 'estimated_checkout_date']);
        });
    }
};
