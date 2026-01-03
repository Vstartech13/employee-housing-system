<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Employee;
use App\Models\RoomOccupancy;
use Carbon\Carbon;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20 rooms with capacity 1
        for ($i = 1; $i <= 20; $i++) {
            Room::create([
                'room_code' => 'M-1-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'capacity' => 1,
                'status' => 'Kosong',
                'occupied_count' => 0
            ]);
        }

        // Create 25 rooms with capacity 2
        for ($i = 1; $i <= 25; $i++) {
            Room::create([
                'room_code' => 'M-2-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'capacity' => 2,
                'status' => 'Kosong',
                'occupied_count' => 0
            ]);
        }

        // Simulate some occupancies (rata-rata 4 tamu per minggu)
        // Let's occupy some rooms with active employees
        $activeEmployees = Employee::where('status', 'Aktif')->inRandomOrder()->take(30)->get();
        $availableRooms = Room::all();
        $roomOccupancyCount = [];

        foreach ($activeEmployees as $employee) {
            // Find a room that still has space
            foreach ($availableRooms as $room) {
                $currentCount = $roomOccupancyCount[$room->id] ?? 0;

                // Check if room still has capacity
                if ($currentCount < $room->capacity) {
                    RoomOccupancy::create([
                        'employee_id' => $employee->id,
                        'room_id' => $room->id,
                        'check_in_date' => Carbon::now()->subDays(rand(1, 30)),
                        'check_out_date' => null // Still occupied
                    ]);

                    // Track occupancy count
                    $roomOccupancyCount[$room->id] = $currentCount + 1;
                    break; // Move to next employee
                }
            }
        }

        // Add some guest house occupancies (simulate weekly guests - 4 people)
        $guestRooms = Room::where('room_code', 'like', 'M-1-%')
            ->whereNotIn('id', array_keys($roomOccupancyCount))
            ->take(4)
            ->get();
        $guestEmployees = Employee::where('status', 'Aktif')
            ->whereNotIn('id', $activeEmployees->pluck('id'))
            ->inRandomOrder()
            ->take(4)
            ->get();

        foreach ($guestEmployees as $index => $employee) {
            if (isset($guestRooms[$index])) {
                RoomOccupancy::create([
                    'employee_id' => $employee->id,
                    'room_id' => $guestRooms[$index]->id,
                    'check_in_date' => Carbon::now()->startOfWeek(),
                    'check_out_date' => null
                ]);
            }
        }

        // Update all room statuses after seeding occupancies
        Room::all()->each(function($room) {
            $room->updateStatus();
        });
    }
}
