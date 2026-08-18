<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\ApplicationController;

Route::apiResource('job-postings', JobPostingController::class);

Route::apiResource('applications', ApplicationController::class);