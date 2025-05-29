<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [ReservationController::class, 'index']);

// API Routes
Route::prefix('events')->group(function () {
    Route::get('/', [ReservationController::class, 'getEvents']);
    Route::post('/', [ReservationController::class, 'store']);
    Route::put('/{id}', [ReservationController::class, 'update']);
    Route::delete('/{id}', [ReservationController::class, 'destroy']);
    Route::post('/{id}/extend', [ReservationController::class, 'extend']);
    Route::post('/check-availability', [ReservationController::class, 'checkAvailability']); // Moved inside events prefix and changed to POST
});

Route::get('/type-rooms/{id}/rooms', [ReservationController::class, 'getRoomsByType']);