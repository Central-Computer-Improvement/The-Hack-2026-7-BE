<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Public job posting routes
Route::get('/job-postings', [JobPostingController::class, 'index']);
Route::get('/job-postings/{job_posting}', [JobPostingController::class, 'show']);

// Public course catalogue
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{course}', [CourseController::class, 'show']);

// Protected routes
Route::middleware('jwt')->group(function () {
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Job postings
    Route::post('/job-postings', [JobPostingController::class, 'store']);
    Route::put('/job-postings/{job_posting}', [JobPostingController::class, 'update']);
    Route::patch('/job-postings/{job_posting}', [JobPostingController::class, 'update']);
    Route::delete('/job-postings/{job_posting}', [JobPostingController::class, 'destroy']);

    // Courses
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{course}', [CourseController::class, 'update']);
    Route::delete('/courses/{course}', [CourseController::class, 'destroy']);

    // Applications
    Route::apiResource('applications', ApplicationController::class);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{notification}', [NotificationController::class, 'update']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // Recommendations
    Route::get('/recommendations/jobs', [RecommendationController::class, 'getJobRecommendations']);
    Route::get('/recommendations/courses', [RecommendationController::class, 'getCourseRecommendations']);
});