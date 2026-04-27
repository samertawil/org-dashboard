<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FeedController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/feed', [FeedController::class, 'index']);
    Route::get('/feed/metadata', [FeedController::class, 'metadata']);
    Route::get('/activities/{activity}/comments', [FeedController::class, 'getComments']);
    Route::post('/activities/{activity}/comments', [FeedController::class, 'addComment']);
});
