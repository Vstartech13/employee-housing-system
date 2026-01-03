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
        Schema::create('room_occupancy_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_occupancy_id')->constrained('room_occupancies')->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_guest')->default(false);
            $table->string('guest_name')->nullable();
            $table->string('occupant_name'); // Store name at the time of action
            $table->enum('action', ['check_in', 'check_out', 'relocate', 'auto_checkout'])->comment('Type of action performed');
            $table->string('old_room_code')->nullable()->comment('For relocation tracking');
            $table->string('new_room_code')->nullable()->comment('For relocation tracking');
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null')->comment('User who performed the action');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_occupancy_histories');
    }
};
