<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'room_code',
        'capacity',
        'status',
        'occupied_count',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($room) {
            if (empty($room->room_code) && $room->capacity) {
                $prefix = 'M-' . $room->capacity;

                $lastRoom = static::where('room_code', 'LIKE', $prefix . '%')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastRoom) {
                    $lastNumber = intval(substr($lastRoom->room_code, strlen($prefix) + 1));
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }

                $room->room_code = $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function occupancies()
    {
        return $this->hasMany(RoomOccupancy::class);
    }

    public function currentOccupants()
    {
        return $this->hasMany(RoomOccupancy::class)
            ->whereNull('check_out_date')
            ->with('employee');
    }

    public function updateStatus()
    {
        $occupiedCount = $this->currentOccupants()->count();
        $this->occupied_count = $occupiedCount;
        $this->status = $occupiedCount > 0 ? 'Terisi' : 'Kosong';
        $this->save();
    }

    public function isAvailable()
    {
        $count = $this->currentOccupants()->count();
        return $count < $this->capacity;
    }
}
