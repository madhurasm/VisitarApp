<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Credential;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Models\VersionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class GeneralController extends Controller
{
    public function dashboard()
    {
        $name = get_dashboard_route_name();
        return redirect()->route($name);
    }

    public function getProfile(Request $request)
    {
        return view('admin.general.profile', [
            'title' => __('admin.profile'),
            'user' => $request->user(),
            'breadcrumbs' => [
                __('admin.profile') => route('admin.profile'),
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'profile_image' => ['file', 'image'],
            'name' => ['required', 'max:255'],
            'username' => ['required', 'max:255', Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')],
            'email' => ['required', 'max:255', Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')],
        ]);

        // Initialize profile image with the original one
        $profile_image = $user->getRawOriginal('profile_image');

        // Check if a new profile image is uploaded
        if ($request->hasFile('profile_image')) {
            $upload = upload_file('profile_image', 'profile_images');
            if ($upload) {
                un_link_file($profile_image);
                $profile_image = $upload;
            } else {
                flash_session('error', 'Profile image upload failed');
                return redirect()->back();
            }
        }
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'profile_image' => $profile_image,
        ]);

        flash_session('success', 'Profile Updated successfully');
        return redirect()->back();
    }

    public function showChangePasswordForm(Request $request)
    {
        return view('admin.general.change-password', [
            'title' => __('admin.change_password'),
            'user' => $request->user(),
            'breadcrumbs' => [
                __('admin.change_password') => route('admin.change-password'),
            ]
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'opassword' => ['required'],
            'npassword' => ['required', 'min:6'],
        ]);
        if (Hash::check($request->opassword, $user->getAuthPassword())) {
            $is_update = $user->update(['password' => $request->npassword]);
            if ($is_update) {
                flash_session('success', __('admin.change_password_updated'));
            } else {
                flash_session('error', __('admin.change_password_updated'));
            }
        } else {
            flash_session('error', __('admin.change_password_not_match'));
        }
        return redirect()->back();
    }

    public function getSetting()
    {
        $fields = GeneralSetting::query();
        if (Auth::user()->type == 'admin') {
            $fields = $fields->whereNotIn('unique_name', ['APP_LOGO', 'BADGE_LOGO']);
        }
        if (Auth::user()->type == 'entity') {
            $fields = $fields->whereNotIn('unique_name', ['SITE_LOGO', 'SMALL_SITE_LOGO', 'FAVICON']);
        }
        $fields = $fields->where(['status' => 'active', 'type' => 'general'])->where('user_id', Auth::id())
            ->orderBy('order_number', 'DESC')
            ->get();

        $header_title = Auth::user()->type == 'admin' ? __('admin.site_settings') : __('admin.entity_site_settings');

        return view('admin.general.setting', [
            'title' => $header_title,
            'fields' => $fields,
            'breadcrumbs' => [
                $header_title => route('admin.site-settings'),
            ]
        ]);
    }

    public function getVersionSetting()
    {
        $fields = GeneralSetting::where(['status' => 'active', 'type' => 'version'])
            ->orderBy('order_number', 'DESC')
            ->get();

        return view('admin.general.version-setting', [
            'title' => __('admin.version_settings'),
            'fields' => $fields,
            'breadcrumbs' => [
                __('admin.version_settings') => route('admin.version-settings'),
            ]
        ]);
    }

    public function VersionList()
    {
        if (request()->ajax()) {
            $users = VersionHistory::latest()->get();
            return DataTables::of($users)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function settingUpdate(Request $request)
    {
        $all_req = $request->except('_token');
        $settings = GeneralSetting::whereIn('id', array_keys($all_req))->get()->keyBy('id');
        foreach ($all_req as $key => $value) {
            if (!isset($settings[$key])) {
                continue;
            }
            $setting = $settings[$key];
            if ($request->hasFile($key)) {
                $request->validate([
                    $key => 'file|mimes:jpg,jpeg,png,pdf|max:2048', // Customize this validation as needed
                ]);
                $filePath = upload_file($key, 'admin_upload');

                if ($filePath) {
                    $image = $setting->getRawOriginal('value');
                    if ($image != 'default/no_user_image.png') {
                        un_link_file($image);
                    }
                    $setting->update(['value' => $filePath]);
                }
            } else {
                $setting->update(['value' => $value]);
            }
        }
//        $g_setting = GeneralSetting::whereIn('unique_name', ['Android_Version', 'Android_Force_Update', 'IOS_Version', 'IOS_Force_Update'])->get();
//        if (isset($g_setting)) {
//            $android_version = $g_setting[0]['value'];
//            $android_force_update = $g_setting[1]['value'];
//            $ios_version = $g_setting[2]['value'];
//            $ios_force_update = $g_setting[3]['value'];
//
//            VersionHistory::updateOrCreate(
//                ['version' => $android_version, 'type' => 'android'],
//                ['version' => $android_version, 'type' => 'android', 'is_force' => $android_force_update,]
//            );
//
//            VersionHistory::updateOrCreate(
//                ['version' => $ios_version, 'type' => 'ios'],
//                ['version' => $ios_version, 'type' => 'ios', 'is_force' => $ios_force_update,]
//            );
//        }
        flash_session('success', __('admin.site_setting_updated'));
//        if ($request['1']) {
//            $slug = strtolower(str_replace(" ", "-", $request['1']));
//            return redirect()->to(url($slug.'/site-settings'));
//        } else {
//            return redirect()->back();
//        }

        return redirect()->back();

    }

    public function getCredentials()
    {
        $fields = Credential::get();;
        return view('admin.general.credentials', [
            'title' => __('admin.credentials'),
            'fields' => $fields,
            'breadcrumbs' => [
                __('admin.credentials') => route('admin.credentials'),
            ]
        ]);
    }

    public function credentialsUpdate(Request $request)
    {
        $credentials = $request->input('credential', []);
        $submittedIds = array_filter($credentials['id']);
        Credential::whereNotIn('id', $submittedIds)->delete();

        foreach ($credentials['key'] as $index => $key) {
            if (!empty($key) && !empty($credentials['value'][$index])) {
                Credential::updateOrCreate(
                    ['id' => $credentials['id'][$index] ?? null],
                    ['key' => $key, 'value' => $credentials['value'][$index]]
                );
            }
        }
        flash_session('success', __('admin.credentials_updated'));
        return redirect()->back();
    }

    public function availabilityChecker(Request $request)
    {
        $count = 0;
        $type = $request->type;
        $val = $request->val;
        $user_id = Auth::id() ?? 0;
        if ($type == "username" || $type == "email") {
            $count = User::where($type, $val)->where('id', '!=', $user_id)->count();
        }
        return $count ? "false" : "true";
    }

    public function availabilityCheckerUser(Request $request)
    {
        $count = 0;
        $type = $request->type;
        $val = $request->val;
        $user_id = $request->id ?? 0;
        if ($type == "username" || $type == "email") {
            $count = User::where($type, $val)->where('id', '!=', $user_id)->count();
        }
        return $count ? "false" : "true";
    }

    public function logout()
    {
        $name = get_dashboard_route_name();
        Auth::logout();
        return redirect()->route($name);
    }
}
