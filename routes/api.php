<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FeedController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/debug-db', function() {
    return [
        'activities_count' => \App\Models\Activity::count(),
        'prs_count' => \App\Models\PurchaseRequisition::count(),
        'quotations_count' => \App\Models\PurchaseQuotationResponse::count(),
    ];
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
 
    Route::get('/feed', [FeedController::class, 'index']);
    Route::post('/activities/{activity}/comments', [FeedController::class, 'addComment']);
});
 
