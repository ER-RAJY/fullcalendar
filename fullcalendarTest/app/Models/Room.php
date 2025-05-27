<?php

// app/Models/Room.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type_room_id'];

    public function typeRoom()
    {
        return $this->belongsTo(TypeRoom::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
