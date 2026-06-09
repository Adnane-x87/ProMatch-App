<?php

use App\Http\Controllers\Api\CniController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FieldController;
use App\Http\Controllers\Api\MobileController;
use App\Http\Controllers\Api\PublicFieldController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/public-fields', [PublicFieldController::class, 'index']);
Route::get('/public-fields/{id}', [PublicFieldController::class, 'show']);
Route::get('/available-slots', [ReservationController::class, 'availableSlots']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::get('/mobile/use-case', [MobileController::class, 'useCase']);
Route::get('/mobile/fields', [MobileController::class, 'fields']);
Route::get('/mobile/fields/{id}', [MobileController::class, 'fieldDetails']);
Route::get('/mobile/available-slots', [MobileController::class, 'availableSlots']);
Route::post('/mobile/reservations', [MobileController::class, 'storeReservation']);

Route::middleware([\App\Http\Middleware\RequireApiToken::class])->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/planning', [ReservationController::class, 'planning']);
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::put('/reservations/{id}/validate', [ReservationController::class, 'validateReservation']);

    Route::apiResource('fields', FieldController::class);
    Route::post('/fields/{id}/slots', [FieldController::class, 'addSlots']);
    Route::get('/dashboard/slots', [DashboardController::class, 'getSlots']);
    Route::post('/dashboard/slots', [DashboardController::class, 'storeSlot']);
    Route::put('/dashboard/slots/{id}', [DashboardController::class, 'updateSlot']);
    Route::delete('/dashboard/slots/{id}', [DashboardController::class, 'destroySlot']);

    Route::get('/user', fn (Request $request) => response()->json(session('user')));
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/cni/upload', [CniController::class, 'upload']);
    Route::post('/mobile/cni/upload', [CniController::class, 'upload']);
    Route::get('/mobile/sync', [MobileController::class, 'sync']);
    Route::get('/mobile/reservations', [MobileController::class, 'myReservations']);
    Route::get('/mobile/admin/reservations', [MobileController::class, 'ownerReservations']);
    Route::get('/mobile/admin/stats', [MobileController::class, 'stats']);
    Route::put('/mobile/admin/reservations/{id}/validate', [MobileController::class, 'validateReservation']);
});
