<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('checkout-user', function () {
    Artisan::call('auto:checkout-users');
});

Route::redirect('/', 'panel/login')->name('dashboard');

Route::get('accept-or-reject-notification/{token}/{action}', [\App\Http\Controllers\WebController::class, 'acceptOrRejectNotification'])->name('accept_or_reject_notification');
