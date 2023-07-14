<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpyController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Kept as a test
Route::middleware('auth:sanctum')->get('/my_profile', function (Request $request) {
    return $request->user();
});

// Registered User Login Logout and refresh token
Route::post('/token',[AuthController::class,'login']);
Route::middleware('auth:sanctum')->get('/token', [AuthController::class,'refreshToken']);
Route::middleware('auth:sanctum')->delete('/token', [AuthController::class,'logout']);

// Spy Test
Route::middleware('auth:sanctum')->put('/spy', [SpyController::class,'add']);
Route::middleware(['auth:sanctum'])->get('/spies', [SpyController::class,'spies']);
Route::middleware(['auth:sanctum','throttle:10,1'])->get('/spies/random', [SpyController::class,'randomSpies']);
