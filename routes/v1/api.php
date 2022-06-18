<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Business\BusinessController;
use App\Models\Business\Business;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum, verified'])->group(function() {
    Route::get('/auth/user', [AuthController::class, 'user']);
});

Route::middleware(['auth:api', 'verified'])->group(function() {
    Route::get('/passport/user', [AuthController::class, 'user']);

    // Business Routes
    Route::post('/business', [BusinessController::class, 'createBusiness']);
    Route::put('/business/{id}', [BusinessController::class, 'updateBusiness']);
    Route::get('/business/list', [BusinessController::class, 'listBusiness']); 

    Route::post('/business/invitation',[BusinessController::class, 'createInvitation']);
    
});

// Public Route
Route::get('/business/invitation',[BusinessController::class, 'getInvitation']);  