<?php

use App\Http\Controllers\Admin\GeneralController;
use App\Http\Controllers\Admin\GuestController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'guest'], function () {
    Route::get('login', [GuestController::class, 'getLogin'])->name('login');
    Route::post('login', [GuestController::class, 'postLogin'])->name('login.post');
    Route::get('forgot-password', [GuestController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [GuestController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [GuestController::class, 'showResetForm'])->name('password.reset');
    Route::get('accept-or-reject-notification/{token}/{action}', [GuestController::class, 'acceptOrRejectNotification'])->name('accept_or_reject_notification');
//    Route::post('accept-or-reject-notification-verify', [GuestController::class, 'acceptOrRejectNotificationVerify'])->name('accept_or_reject_notification_verify');
    Route::post('reset-password', [GuestController::class, 'reset'])->name('password.update');
});

Route::group(['middleware' => ['auth', 'is_entity']], function () {
    Route::get('/', [GeneralController::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [GeneralController::class, 'logout'])->name('logout');
    // Profile
    Route::get('profile', [GeneralController::class, 'getProfile'])->name('profile');
    Route::post('profile', [GeneralController::class, 'updateProfile'])->name('profile.update');
    // Change Password
    Route::get('change-password', [GeneralController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('change-password', [GeneralController::class, 'updatePassword'])->name('change-password.update');
    // Site Settings
    Route::get('site-settings', [GeneralController::class, 'getSetting'])->name('site-settings');
    Route::post('site-settings', [GeneralController::class, 'settingUpdate'])->name('site-settings.update');
    // Version Settings
    Route::get('version-settings', [GeneralController::class, 'getVersionSetting'])->name('version-settings');
    Route::get('version-index', [GeneralController::class, 'VersionList'])->name('version.index');
    // Credentials
    Route::get('credentials', [GeneralController::class, 'getCredentials'])->name('credentials');
    Route::post('credentials', [GeneralController::class, 'credentialsUpdate'])->name('credentials.update');
    //Contents
    Route::resource('contents', ContentController::class);
    //Users
    Route::resource('users', UserController::class);
    Route::get('users/update-status/{id}', [UserController::class, 'updateStatus'])->name('users.update-status');
    //Hosts
    Route::resource('hosts', HostController::class);
    //Visitor Check-IN
    Route::resource('visitor-check-ins', VisitorCheckInController::class);

});
Route::get('user-availability-checker', [GeneralController::class, 'availabilityCheckerUser'])->name('user-availability-checker');
Route::get('availability-checker', [GeneralController::class, 'availabilityChecker'])->name('availability-checker');
