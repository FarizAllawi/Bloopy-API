<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register'])
                ->middleware('guest')
                ->name('register');

Route::post('/auth/login', [AuthController::class, 'login'])
                ->middleware('guest')
                ->name('login');

Route::post('/auth/forgot-password', [PasswordResetLinkController::class, 'store'])
                ->middleware('guest')
                ->name('password.email');

Route::post('/auth/reset-password', [NewPasswordController::class, 'store'])
                ->middleware('guest')
                ->name('password.update');

Route::get('/auth/verify-email/{id}/{hash}', [AuthController::class, 'emailVerify'])
                ->name('verification.verify');

Route::post('/auth/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware(['auth', 'throttle:6,1'])
                ->name('verification.send');

Route::post('auth/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->middleware('auth')
                ->name('logout');

Route::get('auth/signin-with-google', [AuthController::class, 'google']);
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);