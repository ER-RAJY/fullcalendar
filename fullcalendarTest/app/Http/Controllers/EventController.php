<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation; // استيراد الموديل ديال الـ Reservation

class EventController extends Controller
{
    // جلب البيانات من قاعدة البيانات و إرسالها إلى FullCalendar
    public function index() 
    {
        $events = Reservation::all(); // جلب جميع الـ reservations
        return response()->json($events);
    }

    // إضافة Reservation جديدة بناءً على البيانات المرسلة من FullCalendar
    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $reservation = Reservation::create([
            'client_name' => $request->client_name,
            'room_id' => $request->room_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json($reservation, 201); // إرجاع الـ reservation بعد إضافتها
    }
}
