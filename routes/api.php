<?php

use App\Http\Controllers\Api\V1\GuestController;
use App\Http\Controllers\Api\V1\UserController;
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
Route::prefix('V1')->middleware('CheckLanguage')->group(function () {
    Route::post('signup', [GuestController::class, 'signup']);
    Route::post('login', [GuestController::class, 'login']);
    Route::post('forgot-password', [GuestController::class, 'forgotPassword']);
    Route::post('reset-password', [GuestController::class, 'resetPassword']);
    Route::get('content/{slug}/{lang}', [GuestController::class, 'getContent']);
    Route::post('version-checker', [GuestController::class, 'versionChecker']);
    Route::post('send-otp', [GuestController::class, 'sendOtp']);
    Route::post('verify-otp', [GuestController::class, 'verifyOtp']);
    Route::post('entities', [GuestController::class, 'entities']);
    Route::post('entity-sites', [GuestController::class, 'entitySites']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('profile', [UserController::class, 'getProfile']);
        Route::put('profile', [UserController::class, 'editProfile']);
        Route::put('change-password', [UserController::class, 'changePassword']);
        Route::put('change-email', [UserController::class, 'updateEmail']);
        Route::put('change-mobile', [UserController::class, 'updateMobile']);
        Route::post('notification-flag', [UserController::class, 'notificationFlag']);
        Route::post('notifications', [UserController::class, 'notificationList']);
        Route::post('logout', [UserController::class, 'logout']);
        Route::delete('account', [UserController::class, 'deleteAccount']);
        Route::post('change-language', [UserController::class, 'changeLanguage']);
        Route::post('password-confirmation', [UserController::class, 'passwordConfirmation']);
        Route::post('change-template', [UserController::class, 'changeTemplate']);

        Route::post('hosts', [UserController::class, 'hosts']);
        Route::post('check-email-exists', [UserController::class, 'checkEmailExists']);
        Route::post('check-in', [UserController::class, 'checkIn']);
        Route::post('check-in-email-availability', [UserController::class, 'checkInEmailAvailability']);
        Route::post('check-in-user-lists', [UserController::class, 'checkInUserList']);
        Route::post('check-out', [UserController::class, 'checkOut']);
        Route::post('verify-chek-in-user', [UserController::class, 'verifyCheckInUser']);

        Route::get('content-waiver-policy/{lang}', [UserController::class, 'getWaiverPolicyContent']);

        //New Flow
        Route::post('update-login-site', [UserController::class, 'updateLoginSite']);
    });
});
