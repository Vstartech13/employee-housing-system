<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'code',
        'name'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // When department code is updated, regenerate all employee IDs
        static::updating(function ($department) {
            if ($department->isDirty('code')) {
                $oldCode = $department->getOriginal('code');
                $newCode = $department->code;

                // Get all employees with the old department code
                $employees = $department->employees()
                    ->where('employee_id', 'LIKE', $oldCode . '%')
                    ->orderBy('employee_id')
                    ->get();

                // Regenerate employee IDs with new code
                foreach ($employees as $index => $employee) {
                    $employee->employee_id = $newCode . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                    $employee->saveQuietly(); // Save without triggering events
                }
            }
        });
    }

    /**
     * Get all employees in this department
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
