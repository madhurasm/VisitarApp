<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Models\VisitorCheckIn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class GuestController extends Controller
{
    public function getLogin()
    {
        return view('admin.guest.login', ['title' => __('admin.login')]);
    }

    public function postLogin(Request $request)
    {
        // Validate the request input
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Determine if username is an email or username
        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Prepare credentials with 'active' status requirement
        $credentials = [
            $loginField => $request->username,
            'password' => $request->password,
//            'status' => 'active',
        ];

        // Attempt authentication with the specified credentials and remember option
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            if ($user->status === 'inactive') {
                Auth::logout();
                $supportEmail = GeneralSetting::getSiteSettingValue(1, 'SUPPORT_EMAIL');
                $errorMessage = 'Your account has been deactivated. Please contact support.';
                if ($supportEmail) {
                    $errorMessage .= ' For assistance, please reach out to: ' . $supportEmail;
                }
                flash_session('error', $errorMessage);
                return redirect()->back();
            }
            return redirect()->route(get_dashboard_route_name());
        }

        // Set error message based on login field type
        $errorMessage = $loginField === 'email'
            ? 'Please enter a valid email or password'
            : 'Please enter a valid username or password';

        // Store the error message in session and redirect back
        flash_session('error', $errorMessage);
        return redirect()->back();
    }

    public function showLinkRequestForm()
    {
        return view('admin.guest.forgot-password', ['title' => __('admin.forgot_password')]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        User::passwordReset($request->email);
        return redirect()->back();
    }

    public function showResetForm($token)
    {
        return view('admin.guest.reset-password', ['title' => __('admin.reset_password'), 'token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'reset_token' => ['required', Rule::exists('users', 'reset_token')->whereNull('deleted_at')],
            'password' => ['required'],
        ], [
            'reset_token.exists' => 'Invalid password token',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 6 characters long.',
        ]);
        $user = User::where('reset_token', $request->reset_token)->firstOrFail();
        $user->update([
            'password' => $request->password,
            'reset_token' => null,
        ]);
        flash_session('success', 'Password updated successfully');;
        return redirect()->route('admin.login');
    }

//    public function acceptOrRejectNotification($token, $action)
//    {
//        $visitor = VisitorCheckIn::where('visitor_token', $token)->firstOrFail();
//        if (!$visitor){
//            flash_session('success', "Visitor visit request not found please try again.");
//            return redirect()->route('admin.login');
//        }
//
//        $email_sent_time = Carbon::parse($visitor->last_email_sent_at);
//        $current_time = Carbon::now();
//        if ($email_sent_time->diffInMinutes($current_time) > config('constants.email_validate_min')) {
//            $visitor->update([
//                'visitor_token' => null,
//            ]);
//            flash_session('error', 'The link has expired as it exceeded '.config('constants.email_validate_min').' minutes since the check-in time.');
//            return redirect()->route('admin.login');
//        }
//        $visitor->update([
//            'visit_request_status' => $action,
//            'visitor_token' => null,
//        ]);
//
//        flash_session('success', "Visitor visit request " . $action . " successfully");
//        return redirect()->route('admin.login');
//    }

//    public function acceptOrRejectNotificationVerify(Request $request)
//    {
//        $request->validate([
//            'visitor_token' => ['required', Rule::exists('visitor_check_ins', 'visitor_token')],
//            'action' => ['required', 'in:accepted,rejected'],
//        ]);
//
//        $visitor = VisitorCheckIn::where('visitor_token', $request->visitor_token)->firstOrFail();
//        $email_sent_time = Carbon::parse($visitor->last_email_sent_at);
//        $current_time = Carbon::now();
//
//        if ($email_sent_time->diffInMinutes($current_time) > config('constants.email_validate_min')) {
//            $visitor->update([
//                'visitor_token' => null,
//            ]);
//            flash_session('error', 'The link has expired as it exceeded '.config('constants.email_validate_min').' minutes since the check-in time.');
//            return redirect()->route('admin.login');
//        }
//        $visitor->update([
//            'visit_request_status' => $request->action,
//            'visitor_token' => null,
//        ]);
//
//        flash_session('success', "Visitor visit request " . $request->action . " successfully");
//        return redirect()->route('admin.login');
//    }
}
