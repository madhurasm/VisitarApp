<?php

use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\EntityController;
use App\Http\Controllers\Admin\EntitySiteController;
use App\Http\Controllers\Admin\GeneralController;
use App\Http\Controllers\Admin\HostController;
use App\Http\Controllers\Admin\UserController;
use \App\Http\Controllers\Admin\VisitorCheckInController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GuestController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

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

Route::group(['middleware' => ['auth', 'is_admin']], function () {
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
    Route::post('visitor-check-ins/checkout', [VisitorCheckInController::class, 'checkout'])->name('visitor-check-ins.checkout');
    //Entity
    Route::resource('entity', EntityController::class);
    Route::get('entity/reset-password/{id}', [EntityController::class, 'resetPassword'])->name('entity.reset-password');
    //Entity Sites
    Route::resource('entity-sites', EntitySiteController::class);

    Route::get('entity/update-status/{id}', [EntityController::class, 'updateStatus'])->name('entity.update-status');
});
Route::get('user-availability-checker', [GeneralController::class, 'availabilityCheckerUser'])->name('user-availability-checker');
Route::get('availability-checker', [GeneralController::class, 'availabilityChecker'])->name('availability-checker');
