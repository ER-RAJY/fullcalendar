<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;  // أو اسم الكونترولر الصحيح

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


// Route الرئيسية
Route::get('/', [ReservationController::class, 'index']);
Route::get('/events', [ReservationController::class, 'getEvents']);
Route::post('/events', [ReservationController::class, 'store']);
Route::put('/events/{id}', [ReservationController::class, 'update']);
Route::get('/check-availability', [ReservationController::class, 'checkAvailability']);