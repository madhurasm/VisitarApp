<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Models\EntitySite;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Services\ValidationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ResponseController extends Controller
{
    public $errors;
    protected $validationService;

    public function __construct(ValidationService $validationService)
    {
        $this->errors = null;
        $this->validationService = $validationService;
    }

    public function directValidation(array $rules, array $messages = [], $data = null)
    {
        $data = $data ?? request()->all(); // Use null coalescing to get data.
        $validator = Validator::make($data, $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            $this->errors = $validator->errors(); // Capture all error messages
            return $this->sendError(); // Call sendError to handle the response
        }

        // Return validated data if successful.
        return $validator->validated();
    }

    public function sendError($message = null, $array = true)
    {
        // Use the captured errors if any
        $message = $this->errors ? $this->errors->first() : ($message ?: __('api.err_something_went_wrong'));

        // Send the response with the error messages
        return ResponseHelper::send(412, $message, ($array) ? [] : new \stdClass());
    }

    public function sendResponse($status, $message, $result = null, $extra = null)
    {
        return ResponseHelper::send($status, $message, $result, $extra);
    }

    public function get_user_data($token = null)
    {
        // Retrieve only the required fields (signup fields) from the user
        $user_data = User::where('id', Auth::id())->Details()->first();
        $entity = User::where('id', $user_data->entity_id)->first();

        if ($entity->site_id) {
            $siteIds = explode(',', $entity->site_id);
            $sites = EntitySite::select('id', 'name', 'location')->whereIn('id', $siteIds)->get();
        } else {
            $sites = collect();
        }
        return [
            'id' => $user_data->id,
            'entity_id' => $user_data->entity_id ?? '',
            'first_name' => $user_data->first_name ?? '',
            'last_name' => $user_data->last_name ?? '',
            'name' => $user_data->name ?? '',
            'email' => $user_data->email ?? '',
            'country_code' => $user_data->country_code,
            'mobile' => $user_data->mobile,
            'profile_image' => $user_data->profile_image,
            'location' => $user_data->location ?? '',
            'notification' => $user_data->notification,
            'language' => $user_data->language,
            'template_id' => $user_data->template_id,
            'entity' => $user_data->entity,
            'sites' => $sites,
            'APP_LOGO' => checkFileExist(GeneralSetting::getSiteSettingValue($user_data->entity_id, 'APP_LOGO')),
            'BADGE_LOGO' => checkFileExist(GeneralSetting::getSiteSettingValue($user_data->entity_id, 'BADGE_LOGO')),
            'token' => $token ?? '',
        ];
    }
}
