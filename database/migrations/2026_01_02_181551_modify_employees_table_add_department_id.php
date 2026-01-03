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
            // Remove old enum department column
            $table->dropColumn('department');

            // Remove employee_id unique constraint, make it auto-generated
            $table->dropUnique(['employee_id']);
            $table->string('employee_id')->nullable()->change();

            // Add department_id foreign key
            $table->foreignId('department_id')->after('name')->constrained()->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');

            $table->string('employee_id')->unique()->change();
            $table->enum('department', ['HR', 'Finance', 'Produksi', 'Sarana', 'Safety'])->after('name');
        });
    }
};
