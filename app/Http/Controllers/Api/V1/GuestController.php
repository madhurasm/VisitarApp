<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\ResponseController;
use App\Mail\User_Password_Reset_Mail;
use App\Models\Content;
use App\Models\EntitySite;
use App\Models\UserEmailLog;
use App\Models\VersionHistory;
use App\Services\TwilioService;
use App\Services\ValidationService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class GuestController extends ResponseController
{
    protected $validationService;

    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    public function signup(Request $request)
    {
        // Validate request input
        if ($this->directValidation([
            'entity_id' => ['required', 'exists:users,id'],
//            'site_id' => ['required', 'exists:entity_sites,id'],
            'first_name' => ['required', 'max:100'],
            'last_name' => ['required', 'max:100'],
            'email' => $this->validationService->emailUniqueRules(),
            'country_code' => ['required', 'max:10'],
            'mobile' => $this->validationService->mobileRules($request),
            'location' => ['nullable', 'max:100'],
            'password' => $this->validationService->passwordRules(),
            'device_id' => ['required', 'max:255'],
            'device_token' => ['required', 'max:255'],
            'device_type' => ['required', 'in:android,ios'],
        ], [
            'email.unique' => __('api.error_already_taken', ['attribute' => 'email']),
            'mobile.unique' => __('api.error_already_taken', ['attribute' => 'mobile']),
            'password.regex' => __('api.error_password'),
        ])) ;

        // User creation and authentication logic
        $user = User::create([
            'entity_id' => $request->entity_id ?? null,
//            'site_id' => $request->site_id ?? null,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'username' => generateUsername($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'password' => $request->password,
            'country_code' => $request->country_code,
            'mobile' => $request->mobile,
            'location' => $request->location ?? null
        ]);

        if ($user) {
            // Log in the user after signup (Authenticate user)
            Auth::login($user);
            User::AddTokenToUser();
            // Generate token
            $token = $user->createCustomToken('API Token', 60);
            return ResponseHelper::send(200, __('api.success_register'), $this->get_user_data($token));
        }
        return ResponseHelper::send(412, __('api.error_something_went_wrong'));
    }

    public function login(Request $request)
    {
        $this->directValidation([
            'email' => $this->validationService->emailExistsRules(),
            'password' => $this->validationService->passwordRules(),
        ], [
            'email.exists' => __('api.error_not_exists', ['attribute' => 'email']),
            'password.regex' => __('api.error_password'),
        ]);

        // Authentication logic
        $credentials = $request->only('email', 'password') + ['type' => 'user'];
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if the user is active
            if ($user->status === 'active') {
                User::AddTokenToUser();
                // Generate API token
                $token = $user->createCustomToken('API Token', 60);
                return ResponseHelper::send(200, __('api.success_login'), $this->get_user_data($token));
            }

            // If the user is inactive, log out and return error response
            Auth::logout();
            return ResponseHelper::send(401, __('api.error_account_inactive'));
        }

        // If authentication fails, return invalid credentials response
        return ResponseHelper::send(401, __('api.error_invalid_credentials'));
    }

    public function forgotPassword(Request $request)
    {
        $this->directValidation([
            'email' => $this->validationService->emailExistsRules(),
        ]);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update([
                'reset_token' => genUniqueStr('users', 'reset_token', 30, '', true)
            ]);
            try {
                Mail::to($user->email)->send(new User_Password_Reset_Mail($user));

                return ResponseHelper::send(200, __('api.success_password_reset_sent'));
            } catch (\Exception $exception) {
                return ResponseHelper::send(412, __('api.error_something_went_wrong'));
            }
        }
        return ResponseHelper::send(412, __('api.error_something_went_wrong'));
    }

    public function sendOtp(Request $request)
    {
        // Apply rate limiting (throttle: 1 request per minute per email)
        $this->directValidation([
            'email' => $this->validationService->emailExistsRules(),
        ]);

        if (RateLimiter::tooManyAttempts($request->email, 5)) {
            return ResponseHelper::send(429, __('api.error_many_request'));
        }

        RateLimiter::hit($request->email, 60); // 60 seconds window

        // Proceed with OTP generation and sending
        if (register_user_email($request->email)) {
            return ResponseHelper::send(200, __('api.success_send_otp'));
        }
        return ResponseHelper::send(400, __('api.error_something_went_wrong'));
    }

    public function verifyOtp(Request $request)
    {
        $this->validate($request, [
            'email' => $this->validationService->emailLogsExistsRules(),
            'otp' => ['required', 'numeric'],
        ]);

        $user = UserEmailLog::where('email', $request->email)->first();

        if (!$user || $user->otp !== $request->otp) {
            return ResponseHelper::send(400, __('api.error_invalid_otp'));
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return ResponseHelper::send(400, __('api.error_otp_expired'));
        }

        // OTP is valid, proceed with the password reset or other action
        return ResponseHelper::send(200, __('api.success_otp_valid'));
    }

    public function resetPassword(Request $request)
    {
        // Validate the incoming request
        $this->directValidation([
            'email' => $this->validationService->emailExistsRules(),
            'new_password' => $this->validationService->passwordRules(),
            'conf_new_password' => ['required', 'same:new_password'],
        ], [
            'conf_new_password.same' => __('api.error_password_mismatch'),
        ]);

        // Find the user by email
        $user = User::where(['email' => $request->email])->first();

        // Check if the user was found and proceed
        if ($user) {
            // Hash the new password before saving it
            $user->update(['password' => $request->new_password]);

            // Remove OTP logs after successful password reset
            UserEmailLog::where('email', $request->email)->delete();
            return ResponseHelper::send(200, __('api.success_reset_password'));
        }
        return ResponseHelper::send(412, __('api.error_something_went_wrong'));
    }

    public function getContent($slug, $lang)
    {
        $validSlugs = ['term-condition', 'privacy-policy'];

        if (!in_array($slug, $validSlugs)) {
            return ResponseHelper::send(404, __('api.error_invalid_content_type'));
        }

        // Assuming content is stored in the database
        $content = Content::where('slug', $slug)->where('lang', $lang)->first();

        if ($content) {
            return ResponseHelper::send(200, __('api.success_get_content'), $content);
        }

        return ResponseHelper::send(404, __('api.error_content_not_found'));
    }

    public function versionChecker(Request $request)
    {
        $this->directValidation([
            'type' => ['required', 'in:android,ios'],
            'version' => ['required'],
        ]);

        // Fetch the latest version from the database based on type
        $latestVersion = VersionHistory::where('type', $request->type)
            ->orderBy('created_at', 'desc') // Assuming there's a timestamp to determine the latest version
            ->value('version');

        // Check if there's a forced update
        $isForceUpdate = VersionHistory::where([
            'type' => $request->type,
            'is_force' => '1'
        ])->where('version', '>', $request->version)->exists();

        $responseData = [
            'is_force_update' => $isForceUpdate ? 1 : 0,
            'APP_LOGO' => checkFileExist(GeneralSetting::getSiteSettingValue(1, 'APP_LOGO')),
            'BADGE_LOGO' => checkFileExist(GeneralSetting::getSiteSettingValue(1, 'BADGE_LOGO')),
        ];

        // Compare versions and determine response
        if (version_compare($request->version, $latestVersion, '<')) {
            // New version available
            return ResponseHelper::send(200, __('api.success_new_version_available'), array_merge($responseData, ['latest_version' => $latestVersion]));
        }

        // Up-to-date response
        return ResponseHelper::send(200, __('api.success_version_up_to_date'), $responseData);
    }

    public function entities(Request $request)
    {
        $limit = $request->input('limit', null);
        $offset = $request->input('offset', 0);

        $query = User::query()->select('id', 'name', 'email', 'profile_image')->where('type', 'entity')->latest();
        if (!is_null($limit)) {
            $query->limit($limit);
        }
        if (!is_null($offset)) {
            $query->offset($offset);
        }

        $data = $query->get();

        return ResponseHelper::send(200, __('api.success'), $data);
    }

    public function entitySites(Request $request)
    {
        $this->directValidation([
            'entity_id' => ['required', 'exists:users,id'],
        ]);

        $limit = $request->input('limit', null);
        $offset = $request->input('offset', 0);

        $query = EntitySite::query()->select('id', 'name', 'location')->where('entity_id', $request->entity_id)->latest();
        if (!is_null($limit)) {
            $query->limit($limit);
        }
        if (!is_null($offset)) {
            $query->offset($offset);
        }
        $data = $query->get();

        return ResponseHelper::send(200, __('api.success'), $data);
    }
}
