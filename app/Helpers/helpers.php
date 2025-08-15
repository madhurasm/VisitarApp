<?php

use App\Models\Language;
use App\Models\User;
use App\Models\UserEmailLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

if (!function_exists('get_constants')) {
    function get_constants($name)
    {
        return config('constants.' . $name);
    }
}

if (!function_exists('generateUsername')) {
    function generateUsername($name)
    {
        // Convert the name to a URL-friendly version
        $baseUsername = Str::slug($name);

        // Step 1: Check if username exists
        $username = $baseUsername;
        $counter = 1;

        // Step 2: Append a number to make it unique if needed
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
}

if (!function_exists('get_header_auth_token')) {
    function get_header_auth_token()
    {
        $full_token = request()->header('VAuthorization');
        return (substr($full_token, 0, 7) === 'Bearer ') ? substr($full_token, 7) : null;
    }
}

if (!function_exists('get_header_language')) {
    function get_header_language($language = '')
    {
        if (request()->is('api/*')) {
            $language = request()->header('Accept-Language');
        } elseif (Auth::user()) {
            $language = Auth::user()->language;
        }

        return (!empty($language)) ? $language : config('constants.default_lang');
    }
}

if (!function_exists('checkFileExist')) {
    function checkFileExist($path = '', $no_image = 'no_image')
    {
        if (!empty($path) && file_exists(public_path($path))) {
            $url = url($path);  // This will return the full URL
        } else {
            if ($no_image == 'no_user_image') {
                $url = url('default/no_user_image.png');
            } else {
                $url = url('default/' . $no_image . '.png');
            }
        }
        return $url;
    }
}

if (!function_exists('upload_file')) {
    function upload_file($file_name = "", $path = "")
    {
        $file = "";
        $request = \request();

        // Check if file exists and path is provided
        if ($request->hasFile($file_name) && $path) {
            $path = config('constants.upload_paths.' . $path, 'default/path'); // Fallback in case config is not set
            $file = $request->file($file_name)->store($path, config('constants.upload_type', 'public'));
        } else {
            echo 'Provide Valid Const from web controller';
            die();
        }
        return $file;
    }
}
if (!function_exists('un_link_file')) {
    function un_link_file($image_name = "")
    {
        $result = true; // Default to success
        if (!empty($image_name)) {
            try {
                // Get base URL and default images
                $default_url = URL::to('/');
                $default_images = config('constants.default');
                $file_name = str_replace($default_url, '', $image_name);
                $default_image_list = is_array($default_images) ? str_replace($default_url, '', array_values($default_images)) : [];
                if (!in_array($file_name, $default_image_list)) {
                    Storage::disk(get_constants('upload_type'))->delete($file_name);
                }
            } catch (Exception $exception) {
                // Log the exception for debugging
                \Log::error('File Deletion Error: ' . $exception->getMessage());

                // Set result to false on failure
                $result = false;
            }
        } else {
            // If image name is empty, log and return a failure message
            \Log::warning('Empty file name provided to un_link_file function');
            $result = false;
        }

        return $result;
    }
}

if (!function_exists('get_dashboard_route_name')) {
    function get_dashboard_route_name()
    {
        $name = 'front.dashboard';
        $user_data = Auth::user();
        if ($user_data) {
            if (in_array($user_data->type, ["admin"])) {
                $name = 'admin.entity.index';
            }
            if (in_array($user_data->type, ["entity","user"])) {
                $name = 'admin.visitor-check-ins.index';
            }
        }
        return $name;
    }
}

if (!function_exists('genUniqueStr')) {
    function genUniqueStr($table, $field = null, $length = 10, $prefix = null, $isAlphaNum = false)
    {
        // Define character sets
        $numeric = range(0, 9);
        $alpha = array_merge(range('a', 'z'), range('A', 'Z'));

        // Create character pool
        $characters = $numeric;
        if ($isAlphaNum) {
            $characters = array_merge($characters, $alpha);
        }

        $maxLen = max($length - strlen($prefix), 0);
        $attempts = 0;
        $token = '';

        do {
            $token = $prefix;

            // Generate token
            for ($i = 0; $i < $maxLen; $i++) {
                $token .= $characters[array_rand($characters)];
            }

            $attempts++;
            // Add a limit to prevent infinite loop (e.g., 10 attempts)
            if ($attempts > 10) {
                throw new Exception("Unable to generate a unique string after multiple attempts.");
            }

        } while (isTokenExist($token, $table, $field));

        return $token;
    }
}

if (!function_exists('isTokenExist')) {
    function isTokenExist($token, $table, $field)
    {
        return !empty($token) && DB::table($table)->where($field, $token)->exists();
    }
}

if (!function_exists('flash_session')) {
    function flash_session($type = '', $value = "")
    {
        session()->flash('message', ['type' => $type, 'text' => $value]);
    }
}

if (!function_exists('get_error_html')) {
    function get_error_html($error)
    {
        $content = '';
        if ($error->any() !== null && $error->any()) {
            foreach ($error->all() as $value) {
                flash_session('error', $value);
            }
        }
        return $content;
    }
}

if (!function_exists('is_active_module')) {
    function is_active_module($routes)
    {
        return in_array(Route::currentRouteName(), $routes) ? 'mm-active' : '';
    }
}

if (!function_exists('echo_extra_for_site_setting')) {
    function echo_extra_for_site_setting($extra = "")
    {
        $attributes = '';

        // Decode the extra attributes if JSON is valid
        $extraData = json_decode($extra, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($extraData)) {
            foreach ($extraData as $key => $value) {
                // Escape attribute name and value to prevent XSS
                $attributes .= htmlspecialchars($key) . '="' . htmlspecialchars($value) . '" ';
            }
        }

        return $attributes;
    }
}

if (!function_exists('get_fancy_box_html')) {
    function get_fancy_box_html($imagePath, $class = "avatar-sm")
    {
        if (!$imagePath) {
            return '';
        }

        return '<a data-fancybox="" href="' . $imagePath . '">
                <img class="img-thumbnail ' . $class . '" src="' . $imagePath . '" alt="Profile Image">
            </a>';
    }
}

if (!function_exists('get_generate_switch')) {
    function get_generate_switch($status, $id, $route)
    {
        $checked = $status == 'active' ? 'checked' : '';
        return '<div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                    <input class="form-check-input status-switch" type="checkbox" id="SwitchCheckSizemd_' . $id . '" data-id="' . $id . '" data-url="' . $route . '" ' . $checked . '>
                </div>';
    }
}

if (!function_exists('generate_actions_buttons')) {
    function generate_actions_buttons($params = [], $extra = [], $target = false)
    {
        $operation = '';
        $targetAttr = $target ? 'target="_blank"' : '';

        // Standard action buttons (view, edit, delete)
        $actions = [
            'edit' => [
                'icon' => 'mdi mdi-pencil',
                'class' => 'btn btn-sm btn-outline-success waves-effect waves-light btnEdit',
                'title' => 'Edit'
            ],
            'view' => [
                'icon' => 'mdi mdi-eye',
                'class' => 'btn btn-sm btn-outline-primary waves-effect waves-light btnView',
                'title' => 'View'
            ],
            'delete' => [
                'icon' => 'mdi mdi-trash-can',
                'class' => 'btn btn-sm btn-outline-danger waves-effect waves-light btnDelete',
                'title' => 'Delete',
                'data-confirm' => 'Are you sure you want to delete this item?',
            ],
            'checkout' => [
                'icon' => 'mdi mdi-logout',
                'class' => 'btn btn-sm btn-outline-danger waves-effect waves-light btnCheckout',
                'title' => 'Checkout',
                'data-confirm' => 'Are you sure you want to proceed with checkout?',
            ]
        ];

        foreach ($actions as $action => $details) {
            if (isset($params['url'][$action])) {
                $operation .= sprintf(
                    '<a title="' . $details['title'] . '" href="' . $params['url'][$action] . '" data-id="%s" class="font-size-14 me-2 ' . $details['class'] . '">
                                    <i class="' . $details['icon'] . '"></i>
                                 </a>',
                    $details['title'],
                    $targetAttr,
                    $params['url'][$action],
                    $params['id'],
                    $details['class'],
                    $details['icon']
                );
            }
        }

        return $operation;
    }
}

if (!function_exists('get_badge_html')) {
    function get_badge_html($status)
    {
        if ($status == 'active') {
            $badge = '<span class="badge bg-success">' . ucfirst($status) . '</span>';
        } else if ($status == 'inactive') {
            $badge = '<span class="badge bg-danger">' . ucfirst($status) . '</span>';
        } else {
            $badge = '<span class="badge bg-primary">' . ucfirst($status) . '</span>';
        }
        return $badge;
    }
}

if (!function_exists('register_user_email')) {
    function register_user_email($email)
    {
        // Check if environment is local
        if (app()->environment('local')) {
            // Static OTP for local environment (development)
            $otp = 123456;
        } else {
            // Random OTP for production/live environment
            $otp = rand(111111, 999999);
        }

        // Update or create user email log entry
        $user = UserEmailLog::updateOrCreate(
            ['email' => $email],
            ['email' => $email]
        );

        if ($user) {
            // Update OTP and timestamp
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            // Send OTP email
            Mail::to($user->email)->send(new User_Password_Send_OTP_Mail($otp));

            return true;
        }
        return false;
    }
}

if (!function_exists('general_date_time')) {
    function general_date_time($date_time)
    {
        return date('M d, Y h:i A', strtotime($date_time));
    }
}

if (!function_exists('generate_template_pdf')) {
    function generate_template_pdf($type, $data)
    {
        try {
            if (empty($data['name'])) {
                throw new Exception('Name is required.');
            }

            if (!in_array($type, [1, 2, 3, 4])) {
                throw new Exception('Invalid template type.');
            }

            switch ($type) {
                case 1:
                    $template = 'admin.budge_template.template_1';
                    break;
                case 2:
                    $template = 'admin.budge_template.template_2';
                    break;
                case 3:
                    $template = 'admin.budge_template.template_3';
                    break;
                default:
                    $template = 'admin.budge_template.template_4';
                    break;
            }

            $fileName = generateUsername($data['name']) . '-' . time() . '.pdf';
            $filePath = public_path('/uploads/visitor_checkin/' . $fileName);

            if (!file_exists(public_path('/uploads/visitor_checkin/'))) {
                mkdir(public_path('/uploads/visitor_checkin/'), 0777, true);
            }

            // Generate PDF
            $pdf = PDF::loadView($template, $data);
            $pdf->set_paper([0, 0, 127, 76], 'portrait');

            // Try saving the PDF
            if (!$pdf->save($filePath)) {
                throw new Exception('Failed to save the PDF file.');
            }

            $url = url('/uploads/visitor_checkin/') . '/' . $fileName;

            return $url;

        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while generating the PDF. Please try again later.'
            ], 500);
        }
    }
}

if (!function_exists('badge_template_date_format')) {
    function badge_template_date_format($date_time)
    {
        return date('F d, Y', strtotime($date_time));
    }
}

if (!function_exists('site_prefix')) {
    function site_prefix()
    {
        $prefix = config('general_settings.SITE_NAME');
        $slug = strtolower(str_replace(" ", "-", $prefix));
        return $slug;
    }
}

function getAllLanguage()
{
    return Language::where('status', 'active')->get();
}

function generate_entity_general_setting() {
    $settings = [
        [
            'user_id' => '1',
            'label' => 'Site Name',
            'unique_name' => 'SITE_NAME',
            'input_type' => 'text',
            'value' => 'name',
            'options' => null,
            'class' => 'form-control',
            'extra' => json_encode(['required' => 'required']),
            'hint' => 'Please enter site name',
            'type' => 'general',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'user_id' => '1',
            'label' => 'Site Logo',
            'unique_name' => 'SITE_LOGO',
            'input_type' => 'file',
            'value' => '',
            'options' => null,
            'class' => 'form-control',
            'extra' => json_encode(['accept' => "image/*"]),
            'hint' => 'Site logo main',
            'type' => 'general',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'user_id' => '1',
            'label' => 'Small Site Logo',
            'unique_name' => 'SMALL_SITE_LOGO',
            'input_type' => 'file',
            'value' => '',
            'options' => null,
            'class' => 'form-control',
            'extra' => json_encode(['accept' => "image/*"]),
            'hint' => 'Site small logo main',
            'type' => 'general',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'user_id' => '1',
            'label' => 'Fav Icon',
            'unique_name' => 'FAVICON',
            'input_type' => 'file',
            'value' => '',
            'options' => null,
            'class' => 'form-control',
            'extra' => json_encode(['accept' => "image/*"]),
            'hint' => 'Fav icon for site',
            'type' => 'general',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'user_id' => '1',
            'label' => 'App Logo',
            'unique_name' => 'APP_LOGO',
            'input_type' => 'file',
            'value' => '',
            'options' => null,
            'class' => 'form-control',
            'extra' => json_encode(['accept' => "image/*"]),
            'hint' => 'App logo',
            'type' => 'general',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'user_id' => '1',
            'label' => 'Badge Logo',
            'unique_name' => 'BADGE_LOGO',
            'input_type' => 'file',
            'value' => '',
            'options' => null,
            'class' => 'form-control',
            'extra' => json_encode(['accept' => "image/*"]),
            'hint' => 'Bade logo',
            'type' => 'general',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'user_id' => '1',
            'label' => 'Support Email',
            'unique_name' => 'SUPPORT_EMAIL',
            'input_type' => 'email',
            'value' => 'admin@gmail.com',
            'options' => null,
            'class' => 'form-control',
            'extra' => json_encode(['maxlength' => "255", 'required' => 'required']),
            'hint' => 'Please enter email address for support',
            'type' => 'general',
            'created_at' => now(),
            'updated_at' => now(),
        ],
//            [
//                'label' => 'Support Mobile',
//                'unique_name' => 'SUPPORT_MOBILE',
//                'input_type' => 'text',
//                'value' => '+1 1000998877',
//                'options' => null,
//                'class' => 'form-control',
//                'extra' => json_encode(['maxlength' => "20", 'required' => 'required']),
//                'hint' => 'Please enter mobile for support',
//                'type' => 'general',
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
//            [
//                'label' => 'Android Link',
//                'unique_name' => 'ANDROID_LINK',
//                'input_type' => 'text',
//                'value' => '',
//                'options' => null,
//                'class' => 'form-control',
//                'extra' => null,
//                'hint' => 'Please enter android link',
//                'type' => 'general',
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
//            [
//                'label' => 'Ios Link',
//                'unique_name' => 'IOS_LINK',
//                'input_type' => 'text',
//                'value' => '',
//                'options' => null,
//                'class' => 'form-control',
//                'extra' => null,
//                'hint' => 'Please enter ios link',
//                'type' => 'general',
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
//            [
//                'label' => 'Android Version',
//                'unique_name' => 'ANDROID_VERSION',
//                'input_type' => 'text',
//                'value' => '1',
//                'options' => null,
//                'class' => 'form-control',
//                'extra' => json_encode(['required' => 'required']),
//                'hint' => 'Please enter android current version',
//                'type' => 'version',
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
//            [
//                'label' => 'Android Force Update',
//                'unique_name' => 'ANDROID_FORCE_UPDATE',
//                'input_type' => 'select',
//                'value' => '0',
//                'options' => json_encode([
//                    ['name' => 'Yes', 'value' => 'Yes'],
//                    ['name' => 'No', 'value' => 'No'],
//                ]),
//                'class' => 'form-select',
//                'extra' => null,
//                'hint' => 'Is android update forced?',
//                'type' => 'version',
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
//            [
//                'label' => 'iOS Version',
//                'unique_name' => 'IOS_VERSION',
//                'input_type' => 'text',
//                'value' => '1',
//                'options' => null,
//                'class' => 'form-control',
//                'extra' => json_encode(['required' => 'required']),
//                'hint' => 'Please enter iOS current version',
//                'type' => 'version',
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
//            [
//                'label' => 'iOS Force Update',
//                'unique_name' => 'IOS_FORCE_UPDATE',
//                'input_type' => 'select',
//                'value' => '0',
//                'options' => json_encode([
//                    ['name' => 'Yes', 'value' => 'Yes'],
//                    ['name' => 'No', 'value' => 'No'],
//                ]),
//                'class' => 'form-select',
//                'extra' => null,
//                'hint' => 'Is iOS update forced?',
//                'type' => 'version',
//                'created_at' => now(),
//                'updated_at' => now(),
//            ],
    ];
}
