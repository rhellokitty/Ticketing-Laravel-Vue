<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('user', AuthController::class);

    Route::apiResource('ticket', TicketController::class);

    Route::post('ticket/{code}/reply', [TicketController::class, 'storeReply']);

    Route::get('dashboard/statistic', [DashboardController::class, 'getStatistics']);
});
