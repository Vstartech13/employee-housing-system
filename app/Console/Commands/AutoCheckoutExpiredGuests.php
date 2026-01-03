<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RoomOccupancy;
use Carbon\Carbon;

class AutoCheckoutExpiredGuests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guests:auto-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically checkout guests who have exceeded their stay duration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired guest stays...');

        // Find all guests who are overdue for checkout
        $expiredGuests = RoomOccupancy::where('is_guest', true)
            ->whereNull('check_out_date')
            ->whereNotNull('estimated_checkout_date')
            ->where('estimated_checkout_date', '<', Carbon::now()->toDateString())
            ->with('room')
            ->get();

        if ($expiredGuests->isEmpty()) {
            $this->info('No expired guest stays found.');
            return 0;
        }

        $count = 0;
        foreach ($expiredGuests as $occupancy) {
            $occupancy->update([
                'check_out_date' => $occupancy->estimated_checkout_date
            ]);

            // Log auto checkout
            $occupancy->logHistory(
                'auto_checkout',
                null,
                null,
                'Auto checkout by system. Estimated checkout date: ' . $occupancy->estimated_checkout_date->format('Y-m-d')
            );

            $this->info("Auto-checked out guest: {$occupancy->guest_name} from room {$occupancy->room->room_code}");
            $count++;
        }

        $this->info("Successfully auto-checked out {$count} guest(s).");
        return 0;
    }
}
