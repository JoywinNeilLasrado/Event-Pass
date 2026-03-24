<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| Mobile API Routes
|--------------------------------------------------------------------------
*/

// Public Endpoints
Route::get('/welcome', function () {
    return response()->json([
        'message' => 'Welcome to the EventPass Mobile API!',
        'status' => 'success'
    ]);
});

// Public Authentication Endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Endpoints using JWT Tokens
Route::middleware('auth:api')->group(function () {
    // Auth Profile
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Scanner Endpoint
    Route::post('/scan', [\App\Http\Controllers\Api\ScannerController::class, 'scan']);
});
