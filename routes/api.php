<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\ScheduledEmailController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::middleware('auth:sanctum')->group(function () {
    // Schedule Routes
    Route::post('/schedule-email', [ScheduledEmailController::class, 'scheduleEmail']);
    Route::get('/schedule-emails', [ScheduledEmailController::class, 'listScheduledEmails']);
    Route::put('/schedule-email/update/{id}', [ScheduledEmailController::class, 'updateScheduledEmail']);
    Route::delete('/schedule-email/delete/{id}', [ScheduledEmailController::class, 'deleteScheduledEmail']);
    Route::post('/template/schedule-email', [ScheduledEmailController::class, 'scheduleEmailUsingTemplate']);

    // Template Routes
    Route::get('/templates', [EmailTemplateController::class, 'index']);
    Route::post('/templates', [EmailTemplateController::class, 'store']);
    Route::delete('/templates/{template_id}', [EmailTemplateController::class, 'destroy']);
});
