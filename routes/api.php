<?php

use App\Http\Controllers\Api\SyncApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', [SyncApiController::class, 'health']);

Route::prefix('sync')->group(function () {
    Route::get('/bootstrap', [SyncApiController::class, 'bootstrap']);
    Route::get('/changes', [SyncApiController::class, 'changes']);
    Route::post('/transactions', [SyncApiController::class, 'syncTransactions']);
    Route::post('/shifts', [SyncApiController::class, 'syncShift']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
