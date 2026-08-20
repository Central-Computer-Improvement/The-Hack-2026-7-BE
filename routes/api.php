<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::get('profile', [ProfileController::class, 'show'])->middleware(\App\Http\Middleware\JwtMiddleware::class);
    Route::put('profile', [ProfileController::class, 'update'])->middleware(\App\Http\Middleware\JwtMiddleware::class);

    Route::get('recommendations/jobs', [RecommendationController::class, 'getJobRecommendations'])->middleware(\App\Http\Middleware\JwtMiddleware::class);
    Route::get('recommendations/courses', [RecommendationController::class, 'getCourseRecommendations'])->middleware(\App\Http\Middleware\JwtMiddleware::class);
});
