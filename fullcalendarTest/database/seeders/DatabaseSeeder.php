<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeRoom;
use App\Models\Room;
use App\Models\Reservation;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $conference = TypeRoom::create(['name' => 'Conference']);
        $meeting = TypeRoom::create(['name' => 'Meeting']);

        $room1 = Room::create(['name' => 'Room A', 'type_room_id' => $conference->id]);
        $room2 = Room::create(['name' => 'Room B', 'type_room_id' => $meeting->id]);

        Reservation::create([
            'room_id' => $room1->id,
            'client_name' => 'Ahmed',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
        ]);
    }
}

