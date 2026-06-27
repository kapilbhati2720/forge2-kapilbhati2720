<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\CommentController;

use App\Http\Controllers\Api\SlaPolicyController;
use App\Http\Controllers\Api\DashboardController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tickets', TicketController::class)->only(['index', 'store', 'show', 'update']);
    Route::post('/tickets/{ticket}/comments', [CommentController::class, 'store']);
    Route::apiResource('sla-policies', SlaPolicyController::class);
    Route::get('/stats', [DashboardController::class, 'index']);
});
