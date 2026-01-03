<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomOccupancyHistory extends Model
{
    protected $fillable = [
        'room_occupancy_id',
        'room_id',
        'employee_id',
        'is_guest',
        'guest_name',
        'occupant_name',
        'action',
        'old_room_code',
        'new_room_code',
        'performed_by',
        'notes',
    ];

    public function roomOccupancy()
    {
        return $this->belongsTo(RoomOccupancy::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
