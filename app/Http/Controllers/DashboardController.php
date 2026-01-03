<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Room;
use App\Models\RoomOccupancy;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Just return the view, stats will be loaded via API
        return view('dashboard');
    }

    /**
     * Get dashboard statistics (API endpoint)
     */
    public function getStats()
    {
        $stats = [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'Aktif')->count(),
            'inactive_employees' => Employee::where('status', 'Non-aktif')->count(),
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'Kosong')->count(),
            'occupied_rooms' => Room::where('status', 'Terisi')->count(),
            'total_occupancies' => RoomOccupancy::whereNull('check_out_date')->count(),
            'capacity_1' => Room::where('capacity', 1)->count(),
            'capacity_2' => Room::where('capacity', 2)->count(),
        ];

        return response()->json($stats);
    }
}
