<?php

namespace App\Http\Controllers;

use App\Models\VisitorCheckIn;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function acceptOrRejectNotification($token, $action)
    {
        $visitor = VisitorCheckIn::where('visitor_token', $token)->first();
        if (!$visitor) {
            $action = 'not_found';
//            flash_session('success', "Visitor visit request not found please try again.");
            return view('visitor-accept-reject', compact('action'));
        }

        $email_sent_time = Carbon::parse($visitor->last_email_sent_at);
        $current_time = Carbon::now();
        if ($email_sent_time->diffInMinutes($current_time) > config('constants.email_validate_min')) {
            $visitor->update([
                'visit_request_status' => $action,
                'visitor_token' => null,
            ]);
//            flash_session('error', 'The link has expired as it exceeded ' . config('constants.email_validate_min') . ' minutes since the check-in time.');
            return view('visitor-accept-reject', compact('action'));
        }
        $visitor->update([
            'visit_request_status' => $action,
            'visitor_token' => null,
        ]);
//        flash_session('success', "Visitor visit request " . $action . " successfully");
        return view('visitor-accept-reject', compact('action'));
    }
}
