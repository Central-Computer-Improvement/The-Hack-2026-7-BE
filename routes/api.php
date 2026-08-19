<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/job-postings', [JobPostingController::class, 'index']);
Route::get('/job-postings/{job_posting}', [JobPostingController::class, 'show']);

Route::middleware('jwt')->group(function () {
    Route::post('/job-postings', [JobPostingController::class, 'store']);
    Route::put('/job-postings/{job_posting}', [JobPostingController::class, 'update']);
    Route::patch('/job-postings/{job_posting}', [JobPostingController::class, 'update']);
    Route::delete('/job-postings/{job_posting}', [JobPostingController::class, 'destroy']);

    Route::apiResource('applications', ApplicationController::class);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{notification}', [NotificationController::class, 'update']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});
