<?php

// app/Models/Reservation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'client_name',
        'client_phone', // nullable
        'start_date',
        'end_date',
        'activity_type',
        'notes'         // nullable
    ];
    

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
