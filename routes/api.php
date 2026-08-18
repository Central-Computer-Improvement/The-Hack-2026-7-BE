<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\NotificationController;

Route::apiResource('job-postings', JobPostingController::class);

Route::apiResource('applications', ApplicationController::class);
Route::get('/notifications', [NotificationController::class, 'index']);
Route::put('/notifications/{notification}', [NotificationController::class, 'update']);