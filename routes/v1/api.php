<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Business\BusinessController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Bank\BankController;
use App\Http\Controllers\Bank\UserBankController;
use App\Http\Controllers\User\UserController;
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

// Auth for Website
Route::middleware(['auth:sanctum, verified'])->group(function() {
     Route::get('/auth/user', [AuthController::class, 'user']);
    Route::get('/user/realtime-data', [UserController::class, 'getUserRealtimeData']);

});

// Auth for API
Route::post('/passport/register', [AuthController::class, 'register'])
                ->middleware('guest')
                ->name('register');

Route::post('/passport/login', [AuthController::class, 'login'])
                ->middleware('guest')
                ->name('login');

Route::post('/passport/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->middleware('guest')
                ->name('password.email');

Route::post('/passport/reset-password', [NewPasswordController::class, 'store'])
                ->middleware('guest')
                ->name('password.update');

Route::get('/passport/verify-email/{id}/{hash}', [AuthController::class, 'emailVerify'])
     ->name('verification.verify');

Route::post('/passport/verify-email/{id}/{hash}', [AuthController::class, 'emailVerify'])
     ->name('verification.verify');

Route::post('/passport/email/verification-notification', [AuthController::class, 'emailNotification'])
                ->middleware(['auth:api', 'throttle:6,1'])
                ->name('verification.send');

Route::post('/passport/logout', [AuthController::class, 'logout'])
     ->middleware('auth:api')
     ->name('logout');

Route::middleware(['auth:api', 'verified'])->group(function() {
    Route::get('/passport/user', [AuthController::class, 'user']);

    // Business Routes
    Route::post('/business', [BusinessController::class, 'createBusiness']);
    Route::put('/business/{id}', [BusinessController::class, 'updateBusiness']);
    Route::get('/business/list', [BusinessController::class, 'listBusiness']); 

    Route::post('/business/invitation',[BusinessController::class, 'createInvitation']);

    // Bank
    Route::get('/bank', [BankController::class, 'listBank']);
    Route::get('/bank/virtual-acount', [BankController::class, 'listVa']);
    Route::get('/bank/generate', [BankController::class, 'generateBank']);
    Route::delete('/bank/{id}', [BankController::class, 'deleteBank']);
    Route::post('/bank/validate', [BankController::class, 'validateBank']);

    // User Bank
    Route::get('/bank/user/link', [UserBankController::class, 'linkBank']);

    // User
    Route::put('/user', [UserController::class, 'update']);
});

// Public Route
    Route::get('/user/realtime-data', [UserController::class, 'getUserRealtimeData']);
    Route::get('/business/invitation',[BusinessController::class, 'getInvitation']);  