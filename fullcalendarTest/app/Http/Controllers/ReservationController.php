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
            // Format dates properly for FullCalendar
            $startDate = Carbon::parse($reservation->start_date)->format('Y-m-d');
            $endDate = Carbon::parse($reservation->end_date)->addDay()->format('Y-m-d'); // Add day for FullCalendar

            return [
                'id' => $reservation->id,
                'title' => $reservation->client_name,
                'start' => $startDate,
                'end' => $endDate,
                'extendedProps' => [
                    'room_id' => $reservation->room_id,
                    'activity_type' => $reservation->activity_type,
                    'client_name' => $reservation->client_name
                ],
                'backgroundColor' => $this->getEventColor($reservation->activity_type),
                'borderColor' => $this->getEventColor($reservation->activity_type),
                'allDay' => true // Ensure events are treated as all-day
            ];
        });
    
        return response()->json($events);
    }
    
    private function getEventColor($activityType)
    {
        switch ($activityType) {
            case 'stay': return '#28a745';
            case 'conference': return '#dc3545';
            case 'meeting': return '#ffc107';
            default: return '#007bff';
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'client_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'activity_type' => 'required|string|in:stay,conference,meeting',
        ]);

        // Room 1 cannot be reserved
        if ($validatedData['room_id'] == 1) {
            return response()->json([
                'success' => false,
                'message' => 'La chambre 1 ne peut jamais être réservée.',
            ], 403);
        }

        // Normalize dates
        $startDate = Carbon::parse($validatedData['start_date'])->startOfDay();
        $endDate = Carbon::parse($validatedData['end_date'])->endOfDay();

        // Check for conflicts
        $isBooked = $this->checkConflict(
            $validatedData['room_id'],
            $startDate,
            $endDate,
            $validatedData['activity_type']
        );

        if ($isBooked) {
            return response()->json([
                'success' => false,
                'message' => 'La chambre est déjà réservée pour cette période et ce type d\'activité.',
            ], 409);
        }

        // Create reservation (adjust end date for FullCalendar)
        $reservation = Reservation::create([
            'room_id' => $validatedData['room_id'],
            'client_name' => $validatedData['client_name'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'activity_type' => $validatedData['activity_type']
        ]);

        return response()->json([
            'success' => true,
            'data' => $reservation,
            'message' => 'Réservation créée avec succès.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
    
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Normalize dates
        $startDate = Carbon::parse($validatedData['start_date'])->startOfDay();
        $endDate = Carbon::parse($validatedData['end_date'])->endOfDay();

        // Check for conflicts (excluding current reservation)
        $isBooked = $this->checkConflict(
            $reservation->room_id,
            $startDate,
            $endDate,
            $reservation->activity_type,
            $reservation->id
        );

        if ($isBooked) {
            return response()->json([
                'success' => false,
                'message' => 'Conflit de réservation pour cette période.',
            ], 409);
        }

        // Update reservation
        $reservation->update([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    
        return response()->json([
            'success' => true,
            'data' => $reservation,
            'message' => 'Réservation mise à jour avec succès.'
        ]);
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Réservation supprimée avec succès.',
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'activity_type' => 'required|string|in:stay,conference,meeting',
        ]);

        // Room 1 cannot be reserved
        if ($validatedData['room_id'] == 1) {
            return response()->json([
                'available' => false,
                'message' => 'La chambre 1 ne peut jamais être réservée.',
            ]);
        }

        // Normalize dates
        $startDate = Carbon::parse($validatedData['start_date'])->startOfDay();
        $endDate = Carbon::parse($validatedData['end_date'])->endOfDay();

        $exists = $this->checkConflict(
            $validatedData['room_id'],
            $startDate,
            $endDate,
            $validatedData['activity_type']
        );

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Chambre occupée pour cette période' : 'Chambre disponible'
        ]);
    }

    /**
     * Check for reservation conflicts
     */
    private function checkConflict($roomId, $startDate, $endDate, $activityType, $excludeId = null)
    {
        $query = Reservation::where('room_id', $roomId)
            ->where(function($q) use ($activityType) {
                // Check for same activity type OR any activity type that conflicts
                $q->where('activity_type', $activityType)
                  ->orWhere(function($subQ) {
                      // Additional conditions if you want to block certain combinations
                      // For example, maybe 'stay' conflicts with 'conference'
                      $subQ->where('activity_type', '!=', 'meeting')
                           ->where('activity_type', '!=', 'conference');
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->where(function($query) use ($startDate, $endDate) {
            $query->where(function($q) use ($startDate, $endDate) {
                // Existing reservation starts during new reservation
                $q->where('start_date', '>=', $startDate)
                  ->where('start_date', '<', $endDate);
            })->orWhere(function($q) use ($startDate, $endDate) {
                // Existing reservation ends during new reservation
                $q->where('end_date', '>', $startDate)
                  ->where('end_date', '<=', $endDate);
            })->orWhere(function($q) use ($startDate, $endDate) {
                // New reservation is completely within existing reservation
                $q->where('start_date', '<=', $startDate)
                  ->where('end_date', '>=', $endDate);
            });
        })->exists();
    }

    /**
     * Extend a reservation
     */
    public function extend(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $validatedData = $request->validate([
            'new_end_date' => 'required|date|after:' . $reservation->end_date,
        ]);

        $newEndDate = Carbon::parse($validatedData['new_end_date'])->endOfDay();

        // Check for conflicts in extended period
        $isBooked = $this->checkConflict(
            $reservation->room_id,
            $reservation->start_date,
            $newEndDate,
            $reservation->activity_type,
            $reservation->id
        );

        if ($isBooked) {
            return response()->json([
                'success' => false,
                'message' => 'Cette chambre est déjà réservée pour ' . $activityType . ' à cette période.',
            ], 409);
        }

        $reservation->update(['end_date' => $newEndDate]);

        return response()->json([
            'success' => true,
            'data' => $reservation,
            'message' => 'Réservation prolongée avec succès.',
        ]);
    }

    public function getRoomsByType($typeId)
    {
        $typeRoom = TypeRoom::with('rooms')->findOrFail($typeId);
        return response()->json($typeRoom->rooms);
    }
}