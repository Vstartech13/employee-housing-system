<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Employee;
use App\Models\RoomOccupancy;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('currentOccupants.employee')->get();
        return view('rooms.index', compact('rooms'));
    }

    public function getData(Request $request)
    {
        $rooms = Room::with(['currentOccupants.employee.department'])->get();
        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'capacity' => 'required|integer|min:1|max:4',
        ]);

        $room = Room::create([
            'capacity' => $validated['capacity'],
            'status' => 'Kosong',
            'occupied_count' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kamar berhasil ditambahkan dengan kode: ' . $room->room_code,
            'data' => $room
        ]);
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'capacity' => 'required|integer|min:1|max:4',
        ]);

        if ($validated['capacity'] < $room->occupied_count) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengurangi kapasitas kamar. Saat ini ada ' . $room->occupied_count . ' penghuni. Lakukan checkout terlebih dahulu atau pilih kapasitas minimal ' . $room->occupied_count . '.'
            ], 400);
        }

        $room->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data kamar berhasil diupdate',
            'data' => $room
        ]);
    }

    public function destroy(Room $room)
    {
        if ($room->occupied_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus kamar yang masih terisi'
            ], 400);
        }

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kamar berhasil dihapus'
        ]);
    }

    /**
     * Assign employee to room (Check-in)
     */
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'is_guest' => 'required|boolean',
            'employee_id' => 'required_if:is_guest,false|exists:employees,id',
            'guest_name' => 'required_if:is_guest,true|string|max:255',
            'guest_purpose' => 'required_if:is_guest,true|string|max:255',
            'guest_duration_days' => 'required_if:is_guest,true|integer|min:1|max:90',
            'check_in_date' => 'required|date|after_or_equal:today'
        ]);

        $room = Room::findOrFail($validated['room_id']);

        // Check if room is available
        if (!$room->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Kamar sudah penuh'
            ], 400);
        }

        $occupancy = null;
        $activeOccupancy = null;

        if (!$validated['is_guest']) {
            // Employee assignment
            $employee = Employee::findOrFail($validated['employee_id']);

            // Check if employee status is active
            if ($employee->status !== 'aktif') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya karyawan dengan status aktif yang dapat di-assign ke kamar'
                ], 400);
            }

            // Check if employee already has active occupancy
            $activeOccupancy = RoomOccupancy::where('employee_id', $employee->id)
                ->whereNull('check_out_date')
                ->first();

            if ($activeOccupancy) {
                // Log relocation history
                $oldRoomCode = $activeOccupancy->room->room_code;

                // Automatically checkout from old room (same day as check-in to new room)
                $activeOccupancy->update([
                    'check_out_date' => $validated['check_in_date']
                ]);
            }

            // Create new occupancy for employee
            $occupancy = RoomOccupancy::create([
                'room_id' => $room->id,
                'employee_id' => $employee->id,
                'is_guest' => false,
                'check_in_date' => $validated['check_in_date'],
            ]);

            // Log relocation if applicable
            if ($activeOccupancy) {
                $occupancy->logHistory('relocate', $oldRoomCode, $room->room_code, 'Relokasi dari kamar lama ke kamar baru');
            }

            $message = $activeOccupancy
                ? 'Karyawan berhasil dipindahkan ke kamar baru'
                : 'Karyawan berhasil ditambahkan ke kamar';
        } else {
            // Guest assignment
            $occupancy = RoomOccupancy::create([
                'room_id' => $room->id,
                'is_guest' => true,
                'guest_name' => $validated['guest_name'],
                'guest_purpose' => $validated['guest_purpose'],
                'guest_duration_days' => $validated['guest_duration_days'],
                'check_in_date' => $validated['check_in_date'],
            ]);

            $message = 'Tamu berhasil ditambahkan ke kamar untuk ' . $validated['guest_duration_days'] . ' hari';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $occupancy->load('employee', 'room')
        ]);
    }

    /**
     * Remove employee from room (Check-out)
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'occupancy_id' => 'required|exists:room_occupancies,id',
            'check_out_date' => 'required|date'
        ]);

        $occupancy = RoomOccupancy::findOrFail($validated['occupancy_id']);
        $occupancy->update([
            'check_out_date' => $validated['check_out_date']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil',
            'data' => $occupancy->load('employee', 'room')
        ]);
    }
}
