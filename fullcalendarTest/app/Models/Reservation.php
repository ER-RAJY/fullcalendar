<?php

// app/Models/Reservation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'room_id',
        'start_date',
        'end_date',
        'activity_type'
    ];
    

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
