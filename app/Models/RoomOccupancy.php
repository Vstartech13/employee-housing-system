<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class RoomOccupancy extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'room_id',
        'is_guest',
        'guest_name',
        'guest_purpose',
        'guest_duration_days',
        'estimated_checkout_date',
        'check_in_date',
        'check_out_date',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'estimated_checkout_date' => 'date',
        'is_guest' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function histories()
    {
        return $this->hasMany(RoomOccupancyHistory::class);
    }

    protected static function booted()
    {
        static::creating(function ($occupancy) {
            if ($occupancy->is_guest && $occupancy->guest_duration_days) {
                $occupancy->estimated_checkout_date = Carbon::parse($occupancy->check_in_date)
                    ->addDays($occupancy->guest_duration_days);
            }
        });

        static::created(function ($occupancy) {
            $occupancy->room->updateStatus();
            $occupancy->logHistory('check_in');
        });

        static::updated(function ($occupancy) {
            $occupancy->room->updateStatus();
            if ($occupancy->check_out_date && $occupancy->isDirty('check_out_date')) {
                $occupancy->logHistory('check_out');
            }
        });

        static::deleted(function ($occupancy) {
            $occupancy->room->updateStatus();
        });
    }

    public function logHistory($action, $oldRoomCode = null, $newRoomCode = null, $notes = null)
    {
        $name = $this->is_guest ? $this->guest_name : ($this->employee ? $this->employee->name : 'Unknown');

        RoomOccupancyHistory::create([
            'room_occupancy_id' => $this->id,
            'room_id' => $this->room_id,
            'employee_id' => $this->employee_id,
            'is_guest' => $this->is_guest,
            'guest_name' => $this->guest_name,
            'occupant_name' => $name,
            'action' => $action,
            'old_room_code' => $oldRoomCode,
            'new_room_code' => $newRoomCode,
            'performed_by' => auth()->id(),
            'notes' => $notes,
        ]);
    }

    /**
     * Check if guest checkout is overdue
     */
    public function isOverdue()
    {
        if (!$this->is_guest || !$this->estimated_checkout_date || $this->check_out_date) {
            return false;
        }

        return Carbon::now()->greaterThan($this->estimated_checkout_date);
    }
}
