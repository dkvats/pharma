<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\PincodeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "api" middleware group. These routes are
| automatically prefixed with /api.
|
*/

// Simple test endpoint
Route::get('/test', function () {
    return response()->json([
        'message' => 'API working successfully',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Status endpoint using controller
Route::get('/status', [TestController::class, 'index']);

// PIN Code Lookup API
Route::get('/pincode/{pin}', [PincodeController::class, 'lookup'])->name('api.pincode.lookup');
