<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\ResponseController;
use App\Mail\Send_Visit_Notification_Mail;
use App\Mail\User_Password_Reset_Mail;
use App\Models\Content;
use App\Models\GeneralSetting;
use App\Models\Host;
use App\Models\Notification;
use App\Models\User;
use App\Models\VisitorCheckIn;
use App\Services\ValidationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends ResponseController
{
    protected $validationService;

    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function getProfile(Request $request)
    {
        return ResponseHelper::send(200, __('api.success'), $this->get_user_data());
    }

    public function editProfile(Request $request)
    {
        $userId = $request->user()->id;
        $this->directValidation([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => $this->validationService->emailUniqueRules($userId),
            'country_code' => ['required', 'max:10'],
            'mobile' => $this->validationService->mobileRules($request, $userId),
            'profile_image' => ['nullable', 'image'],
            'location' => ['required', 'max:100'],
        ]);
        $user = $request->user();
        // Check if profile image exists in the request and upload
        $profile_image = $user->getRawOriginal('profile_image');
        if ($request->hasFile('profile_image')) {
            $imagePath = upload_file('profile_image', 'profile_images');
            // Optionally delete the old profile image
            if ($imagePath) {
                un_link_file($profile_image);
                $profile_image = $imagePath;
            }

            // Update user profile with new image path
            $user->profile_image = $profile_image;
        }
        $username = generateUsername($request->input('first_name') . ' ' . $request->input('last_name'));
        $user->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'name' => $request->input('first_name') . ' ' . $request->input('last_name'),
            'username' => $username,
            'country_code' => $request->input('country_code'),
            'mobile' => $request->input('mobile'),
            'location' => $request->input('location'),
        ]);

        return ResponseHelper::send(200, __('api.success_profile_update'), $this->get_user_data());
    }

    public function notificationFlag(Request $request)
    {
        $this->directValidation([
            'notification_enabled' => ['required', 'in:yes,no'],
        ]);

        $user = $request->user();
        $user->update(['notification' => $request->notification_enabled]);

        return ResponseHelper::send(200, __('api.success_profile_update'), $this->get_user_data());
    }

    public function notificationList(Request $request)
    {
        $userId = $request->user()->id;
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;
        $notifications = Notification::where(['user_id' => $userId])->latest()->limit($limit)->offset($offset)->get();
        Notification::where(['user_id' => $userId])->latest()->limit($limit)->offset($offset)->update(['read' => 1]);
        return ResponseHelper::send(200, __('api.success'), $notifications);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ResponseHelper::send(200, __('api.success_logout'));
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        $user->delete();
        return ResponseHelper::send(200, __('api.success_delete_account'));
    }

    public function getUserList(Request $request)
    {
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;
        $search = $request->input('search', '');
        $family_id = $request->input('family_id', '');
        $userId = $request->user()->id;

        $members = FamilyMember::where('family_id', $family_id)->get()->pluck('member_id')->toArray();
        $query = User::where('id', '!=', $userId)->Type()->Active();
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('country_code', 'like', "%{$search}%")->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere(DB::raw('CONCAT_WS(" ", country_code, mobile)'), 'like', "%{$search}%");
            });
        }
        $data = $query->whereNotIn('id', $members)->List()
            ->limit($limit)
            ->offset($offset)
            ->latest()
            ->get();

        return ResponseHelper::send(200, __('api.success'), $data);
    }

    public function changePassword(Request $request)
    {
        $this->directValidation([
            'old_password' => ['required'],
            'new_password' => $this->validationService->passwordRules(),
            'confirm_password' => ['required', 'same:new_password'],
        ], [
            'new_password.regex' => __('api.error_password'),
            'conf_new_password.same' => __('api.error_password_mismatch'),
        ]);

        $user = $request->user();
        if (Hash::check($request->old_password, $user->password)) {
            $is_update = $user->update(['password' => $request->new_password]);
            if ($is_update) {
                return ResponseHelper::send(200, __('api.success_password_updated'));
            } else {
                return ResponseHelper::send(412, __('api.error_something_went_wrong'));
            }
        } else {
            return ResponseHelper::send(412, __('api.error_old_password_not_match'));
        }

    }

    public function changeLanguage(Request $request)
    {
        $this->directValidation([
            'language' => ['required', 'in:en,es']
        ]);

        $user = $request->user();
        $user->update(['language' => $request->language]);
        App::setLocale($user->language);

        return ResponseHelper::send(200, __('api.success_language_changed'));
    }

    public function hosts(Request $request)
    {
        $limit = $request->input('limit', null);
        $offset = $request->input('offset', 0);
        $user = $request->user();

        $query = Host::query();
        $query = $query->where('user_id', $user->entity_id)->select('id', 'name', 'email')->latest();
        if (!is_null($limit)) {
            $query->limit($limit);
        }
        if (!is_null($offset)) {
            $query->offset($offset);
        }

        $data = $query->get();

        return ResponseHelper::send(200, __('api.success'), $data);
    }

    public function checkInUserList(Request $request)
    {
        $limit = $request->input('limit', null);
        $offset = $request->input('offset', 0);
        $user = $request->user();

        $query = VisitorCheckIn::where('user_id', $user->entity_id)->whereNUll('check_out')->selectRaw('id, name, email, host_id, check_in')
            ->whereIn('id', function ($subquery) {
                $subquery->selectRaw('MAX(id)')
                    ->from('visitor_check_ins')
                    ->groupBy('name');
            })
            ->orderByDesc('check_in');
        if (!is_null($limit)) {
            $query->limit($limit);
        }
        if (!is_null($offset)) {
            $query->offset($offset);
        }

        $data = $query->get();

        return ResponseHelper::send(200, __('api.success'), $data);
    }

    public function checkEmailExists(Request $request)
    {
        $this->directValidation([
            'email' => ['required', 'email', 'max:100'],
        ]);
        $user = $request->user();
        $check_email = VisitorCheckIn::where('user_id', $user->entity_id)->where('email', $request->email)->whereNull('check_out')->first();
        if ($check_email) {
            return ResponseHelper::send(412, __('api.error_email_in_use'));
        }
        return ResponseHelper::send(200, __('api.success_email_available'));

    }

    public function checkIn(Request $request)
    {
        $this->directValidation([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'country_code' => ['required', 'max:10'],
            'mobile' => ['required', 'numeric', 'digits_between:7,14'],
            'company_name' => ['required', 'string', 'max:100'],
            'purpose_of_visit' => ['required', 'string', 'max:5000'],
            'host_id' => ['required', 'exists:hosts,id'],
            'host_name' => ['required', 'string', 'max:100'],
            'profile_image' => ['required', 'image'],
        ]);

        $user = $request->user();

        $badge_template = $user->template_id;
        $current_time = Carbon::now();
        if ($request->hasFile('profile_image')) {
            $imagePath = upload_file('profile_image', 'profile_images');
        }

        $visitorData = VisitorCheckIn::where('user_id', $user->entity_id)->where('host_id', $request->host_id)->where('email', $request->email)->whereNull('check_out')->first();
        if ($visitorData) {
            return ResponseHelper::send(412, __('api.error_email_in_use'));
        }
        if (!$visitorData) {
            $visitorData = new VisitorCheckIn();
            $visitorData->receptionist_id = $user->id;
            $visitorData->site_id = $user->site_id;
            $visitorData->user_id = $user->entity_id;
            $visitorData->host_id = $request->host_id;
            $visitorData->email = $request->email;
            $visitorData->visit_request_status = 'pending';
        }
        $visitorData->check_in = $current_time;
        $visitorData->name = $request->name;
        $visitorData->country_code = $request->country_code;
        $visitorData->mobile = $request->mobile;
        $visitorData->company_name = $request->company_name;
        $visitorData->purpose_of_visit = $request->purpose_of_visit;
        $visitorData->host_name = $request->host_name;
        $visitorData->profile_image = $imagePath ?? null;
        $visitorData->check_out = null;
        $visitorData->save();

        $print_data = [
            'APP_LOGO' => GeneralSetting::getSiteSettingValue($user->entity_id, 'APP_LOGO'),
            'BADGE_LOGO' => GeneralSetting::getSiteSettingValue($user->entity_id, 'BADGE_LOGO'),
            'profile_image' => $visitorData->getRawOriginal('profile_image'),
            'name' => $visitorData->name,
            'email' => $visitorData->email,
            'country_code' => $request->country_code,
            'mobile' => $request->mobile,
            'company_name' => $visitorData->company_name,
            'host_name' => $visitorData->host_name,
            'purpose_of_visit' => $request->purpose_of_visit,
            'check_in' => badge_template_date_format($visitorData->check_in),
            'badge_template' => $badge_template,
        ];

        $check_in_time = Carbon::parse($visitorData->check_in);
        $host = Host::where('id', $request->host_id)->first();

        if ($request->is_retry) {
            $token = genUniqueStr('visitor_check_ins', 'visitor_token', 30, '', true);
            $visitorData->update([
                'visit_request_status' => 'pending',
                'visitor_token' => $token,
                'last_email_sent_at' => $current_time,
            ]);
            try {
                Mail::to($host->email)->send(new Send_Visit_Notification_Mail($host, $token, $print_data));
            } catch (\Exception $exception) {
                return ResponseHelper::send(412, __('api.error_unable_to_notify') . ' ' . $print_data['host_name'] . '.');
            }
        } else {
            if ($host && $visitorData->visit_request_status == 'pending') {
                if (is_null($visitorData->last_email_sent_at) || $check_in_time->diffInMinutes($visitorData->last_email_sent_at) > config('constants.email_validate_min')) {
                    $token = genUniqueStr('visitor_check_ins', 'visitor_token', 30, '', true);
                    $visitorData->update([
                        'visitor_token' => $token,
                        'last_email_sent_at' => $current_time,
                    ]);
                    try {
                        Mail::to($host->email)->send(new Send_Visit_Notification_Mail($host, $token, $print_data));
                    } catch (\Exception $exception) {
                        return ResponseHelper::send(412, __('api.error_unable_to_notify') . ' ' . $print_data['host_name'] . '.');
                    }
                }
            }
        }

//        if ($host && $visitorData->visit_request_status == 'rejected') {
//            $token = genUniqueStr('visitor_check_ins', 'visitor_token', 30, '', true);
//            $visitorData->update([
////                'visit_request_status' => 'pending',
//                'visitor_token' => $token,
//                'last_email_sent_at' => $current_time,
//            ]);
//            try {
//                Mail::to($host->email)->send(new Send_Visit_Notification_Mail($host, $token, $print_data));
//            } catch (\Exception $exception) {
//                return ResponseHelper::send(412, __('api.error_unable_to_notify') . ' ' . $print_data['host_name'] . '.');
//            }
//        }

        $data = [
            'APP_LOGO' => checkFileExist(GeneralSetting::getSiteSettingValue($user->entity_id, 'APP_LOGO')),
            'BADGE_LOGO' => checkFileExist(GeneralSetting::getSiteSettingValue($user->entity_id, 'BADGE_LOGO')),
            'profile_image' => url($visitorData->profile_image),
            'id' => $visitorData->id,
            'name' => $visitorData->name,
            'email' => $visitorData->email,
            'country_code' => $request->country_code,
            'mobile' => $request->mobile,
            'company_name' => $visitorData->company_name,
            'host_name' => $visitorData->host_name,
            'purpose_of_visit' => $request->purpose_of_visit,
            'check_in_date_format' => badge_template_date_format($visitorData->check_in),
            'check_in' => $visitorData->check_in,
            'visit_request_status' => $visitorData->visit_request_status,
            'badge_template' => $badge_template,
        ];

        try {
//            $pdf_url = generate_template_pdf($badge_template, $print_data);
            $data['visitor_pdf'] = '';

            return ResponseHelper::send(200, __('api.success_check_in_complete'), $data);
        } catch (\Exception $e) {
            return ResponseHelper::send(412, __('api.error_unable_to_print_badge'));
        }
    }

    public function checkInEmailAvailability(Request $request)
    {
        $this->directValidation([
            'email' => ['required']
        ]);
        $user = $request->user();
        $email = strtolower($request->email);
        $isAvailable = !VisitorCheckIn::where('user_id', $user->entity_id)->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('check_out')
            ->exists();

        $data = [
            'is_available' => $isAvailable
        ];

        return ResponseHelper::send(200, __('api.success'), $data);
    }

    public function checkOut(Request $request)
    {
        $this->directValidation([
            'email' => ['required', 'email', 'max:100'],
            'host_id' => ['required']
        ]);
        $user = $request->user();
        $visitor = VisitorCheckIn::where('user_id', $user->entity_id)->where('email', $request->email)->where('host_id', $request->host_id)->whereNull('check_out')->first();
        if (!$visitor) {
            return ResponseHelper::send(412, __('api.error_visitor_not_found'));
        }

        $visitor->update([
            'check_out' => now(),
        ]);

        return ResponseHelper::send(200, __('api.success_check_out_complete'));
    }

    public function passwordConfirmation(Request $request)
    {
        $this->directValidation([
            'password' => ['required']
        ]);

        $user = $request->user();
        if (Hash::check($request->password, $user->password)) {
            return ResponseHelper::send(200, __('api.success_password_confirmation'));
        }
        return ResponseHelper::send(412, __('api.error_password_not_match'));
    }

    public function changeTemplate(Request $request)
    {
        $this->directValidation([
            'template_id' => ['required', 'in:1,2,3,4']
        ]);

        $user = $request->user();
        if ($user->template_id == $request->template_id) {
            return ResponseHelper::send(412, __('api.error_already_selected_this_template'));
        }
        $user->update([
            'template_id' => $request->template_id,
        ]);

        return ResponseHelper::send(200, __('api.success_template_updated'), $this->get_user_data());
    }

    public function verifyCheckInUser(Request $request)
    {
        $this->directValidation([
//            'name' => ['required'],
            'email' => ['required']
        ]);

        $user = $request->user();
        $name = strtolower($request->name);
        $email = strtolower($request->email);
        $visitor = VisitorCheckIn::where('user_id', $user->entity_id)->select('id', 'name', 'email', 'host_id', 'check_in')
//            ->whereRaw('LOWER(name) = ?', [$name])
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('check_out')->first();
        if (!$visitor) {
            return ResponseHelper::send(412, __('api.error_visitor_not_found'));
        }
        return ResponseHelper::send(200, __('api.success'), $visitor);
    }

    public function getWaiverPolicyContent(Request $request, $lang)
    {
        $user = $request->user();

        if ($user->entity_id) {
            $content = Content::where('user_id', $user->entity_id)->where('slug', 'waiver-policy')->where('lang', $lang)->first();
        } else {
            $content = Content::where('slug', 'waiver-policy')->where('lang', $lang)->first();
        }

        if ($content) {
            return ResponseHelper::send(200, __('api.success_get_content'), $content);
        }

        return ResponseHelper::send(404, __('api.error_content_not_found'));
    }

    public function updateLoginSite(Request $request)
    {
        $this->directValidation([
            'site_id' => ['required', 'exists:entity_sites,id']
        ]);
        $user = $request->user();

        $user->site_id = $request->site_id;
        $user->save();

        return ResponseHelper::send(200, __('api.success'), $this->get_user_data());
    }
}
