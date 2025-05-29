<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeRoom;
use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks to avoid constraint errors
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear all existing data
        Reservation::truncate();
        Room::truncate();
        TypeRoom::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create new room types
        $conference = TypeRoom::create(['name' => 'Conference']);
        $meeting = TypeRoom::create(['name' => 'Meeting']);
        $standard = TypeRoom::create(['name' => 'Standard']);
        $deluxe = TypeRoom::create(['name' => 'Deluxe']);

        // Create rooms for each type
        $conferenceRooms = [
            ['name' => 'Conference Room A', 'type_room_id' => $conference->id],
            ['name' => 'Conference Room B', 'type_room_id' => $conference->id],
        ];
        
        $meetingRooms = [
            ['name' => 'Meeting Room 1', 'type_room_id' => $meeting->id],
            ['name' => 'Meeting Room 2', 'type_room_id' => $meeting->id],
        ];
        
        $standardRooms = [
            ['name' => 'Standard Room 101', 'type_room_id' => $standard->id],
            ['name' => 'Standard Room 102', 'type_room_id' => $standard->id],
        ];
        
        $deluxeRooms = [
            ['name' => 'Deluxe Room 201', 'type_room_id' => $deluxe->id],
            ['name' => 'Deluxe Room 202', 'type_room_id' => $deluxe->id],
        ];

        // Insert all rooms
        Room::insert(array_merge($conferenceRooms, $meetingRooms, $standardRooms, $deluxeRooms));

        // Create some sample reservations
        Reservation::create([
            'room_id' => Room::where('name', 'Conference Room A')->first()->id,
            'client_name' => 'Ahmed',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'activity_type' => 'conference'
        ]);

        Reservation::create([
            'room_id' => Room::where('name', 'Deluxe Room 201')->first()->id,
            'client_name' => 'Marie',
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(5),
            'activity_type' => 'stay'
        ]);
    }
}