<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'employee_id',
        'name',
        'department_id',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->employee_id) && $employee->department_id) {
                $dept = Department::find($employee->department_id);
                $prefix = $dept ? $dept->code : 'EMP';

                $lastEmp = static::where('employee_id', 'LIKE', $prefix . '%')
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNum = 1;
                if ($lastEmp) {
                    $lastNum = intval(substr($lastEmp->employee_id, strlen($prefix)));
                    $nextNum = $lastNum + 1;
                }

                $employee->employee_id = $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function occupancies()
    {
        return $this->hasMany(RoomOccupancy::class);
    }

    public function currentRoom()
    {
        return $this->hasOne(RoomOccupancy::class)
            ->whereNull('check_out_date')
            ->latest();
    }
}
