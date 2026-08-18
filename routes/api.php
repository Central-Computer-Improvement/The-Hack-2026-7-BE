<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobPostingController;

Route::apiResource('job-postings', JobPostingController::class);