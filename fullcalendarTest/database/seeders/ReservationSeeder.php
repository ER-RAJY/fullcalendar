<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run()
    {
        Reservation::create([
            'client_name' => 'John Doe',
            'room_id' => 1, // مثال على Room ID
            'start_date' => Carbon::now()->addDays(2),
            'end_date' => Carbon::now()->addDays(5),
        ]);

        Reservation::create([
            'client_name' => 'Jane Smith',
            'room_id' => 2,
            'start_date' => Carbon::now()->addDays(6),
            'end_date' => Carbon::now()->addDays(8),
        ]);
    }
}
