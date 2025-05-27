<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\TypeRoom;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $typeRooms = TypeRoom::with('rooms')->get();
        return view('calendar', compact('typeRooms'));
    }

    public function getEvents()
    {
        $events = Reservation::all()->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'title' => $reservation->client_name,
                'start' => $reservation->start_date,
                'end' => $reservation->end_date,
                'room_id' => $reservation->room_id,
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'client_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'activity_type' => 'required|string|in:stay,conference,meeting', // حسب أنواع النشاط
        ]);
        
        $isBooked = Reservation::where('room_id', $validatedData['room_id'])
            ->where('activity_type', $validatedData['activity_type']) // ✅ فقط نفس نوع النشاط
            ->where(function ($query) use ($validatedData) {
                $query->whereBetween('start_date', [$validatedData['start_date'], $validatedData['end_date']])
                      ->orWhereBetween('end_date', [$validatedData['start_date'], $validatedData['end_date']])
                      ->orWhere(function ($query) use ($validatedData) {
                          $query->where('start_date', '<=', $validatedData['start_date'])
                                ->where('end_date', '>=', $validatedData['end_date']);
                      });
            })->exists();

        if ($isBooked) {
            return response()->json([
                'success' => false,
                'message' => 'الغرفة محجوزة في هذه المدة.',
            ], 409);
        }

        $reservation = Reservation::create($validatedData);

        return response()->json([
            'success' => true,
            'data' => $reservation,
        ]);
    }

    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
    
        $startDate = Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($request->end_date)->format('Y-m-d H:i:s');

        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
    
        $validatedData['start_date'] = $startDate;
        $validatedData['end_date'] = $endDate;
    
        // تحقق من تداخل الحجز هنا لو تريد (ممكن تضيف نفس شروط التحقق من store)

        $reservation->update($validatedData);
    
        return response()->json([
            'success' => true,
            'data' => $reservation,
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'activity_type' => 'required|string|in:stay,conference,meeting', // ✅ ضروري
        ]);

        $exists = Reservation::where('room_id', $request->room_id)
            ->where('activity_type', $request->activity_type) // ✅ مهم
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function ($query) use ($request) {
                          $query->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                      });
            })->exists();

        return response()->json([
            'available' => !$exists,
        ]);
    }
}
