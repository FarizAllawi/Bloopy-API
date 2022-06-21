<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Business\BusinessController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

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

// Auth for API

Route::get('/passport/verify-email/{id}/{hash}', [AuthController::class, 'emailVerify'])
     ->name('verification.verify');

Route::post('/passport/verify-email/{id}/{hash}', [AuthController::class, 'emailVerify'])
     ->name('verification.verify');

Route::post('/passport/email/verification-notification', [AuthController::class, 'emailNotification'])
                ->middleware(['auth:api', 'throttle:6,1'])
                ->name('verification.send');

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