<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EntityCreationMail;
use App\Mail\EntityPasswordResetMail;
use App\Models\Content;
use App\Models\EntitySite;
use App\Models\GeneralSetting;
use App\Models\Host;
use App\Models\User;
use App\Models\VisitorCheckIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class EntityController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $users = User::where(['type' => 'entity'])->latest('id')->get();
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('profile_image', function ($row) {
                    return get_fancy_box_html($row->profile_image);
                })
                ->addColumn('mobile', function ($row) {
                    return $row->country_code . ' ' . $row->mobile;
                })
                ->addColumn('status', content: function ($row) {
                    return get_generate_switch($row->status, $row->id, route('admin.entity.update-status', $row->id));

                })
                ->addColumn('action', function ($row) {
                    $params = [
                        'id' => $row->id,
                        'url' => [
                            'edit' => route('admin.entity.edit', $row->id),
//                            'view' => route('admin.entity.show', $row->id),
                            'delete' => route('admin.entity.destroy', $row->id),
                        ]
                    ];

                    // Generate action buttons using the helper function
                    return generate_actions_buttons($params);
                })
                ->rawColumns(['profile_image', 'status', 'action'])
                ->make(true);
        }

        return view('admin.entity.index', [
            'title' => "Entity",
            'breadcrumbs' => [
                'Entity' => route('admin.entity.index'),
            ]
        ]);
    }

    public function create()
    {
        return view('admin.entity.create', [
            'title' => "Create New Entity Profile",
            'breadcrumbs' => [
                'Entity' => route('admin.entity.index'),
                'Create' => route('admin.entity.create'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'max:50', Rule::unique('users', 'username')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')->whereNull('deleted_at')],
            'email' => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at')],
        ]);
        $randomPassword = genUniqueStr('users', 'password', 8, '', true);

        $data = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $randomPassword,
            'type' => 'entity',
        ]);

        $user_id = $data->id;

        GeneralSetting::generate_entity_general_setting($user_id);
        Content::generate_entity_content($user_id);

        try {
            Mail::to($data->email)->send(new EntityCreationMail($data, $randomPassword));
        } catch (\Exception $e) {
            flash_session('error', $e->getMessage());
        }
        flash_session('success', 'Entity created successfully.');
        return redirect()->route('admin.entity.index');
    }

//    public function show(Request $request)
//    {
//        $user = $request->user();
//        return view('admin.entity.show', [
//            'title' => "Entity",
//            'data' => $user,
//            'breadcrumbs' => [
//                'Entity' => route('admin.entity.index'),
//                'View' => route('admin.entity.show', $user->id),
//            ]
//        ]);
//    }

    public function updateStatus($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->update(['status' => ($user->status == "inactive") ? "active" : "inactive"]);
            $user->save();
            return response()->json(['success' => true, 'message' => 'Entity status updated successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Entity not found.']);
    }

    public function edit($id)
    {
        $user = User::find($id);

        return view('admin.entity.edit', [
            'title' => "Update Entity Profile",
            'data' => $user,
            'breadcrumbs' => [
                'Entity' => route('admin.entity.index'),
                'edit' => route('admin.entity.edit', $user->id),
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $request->validate([
            'username' => ['required', 'max:50', Rule::unique('users', 'username')->ignore($user->id)->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')->ignore($user->id)->whereNull('deleted_at')],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)->whereNull('deleted_at')],
        ]);

        $user->username = $request->username;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        flash_session('success', 'Entity updated successfully.');
        return redirect()->route('admin.entity.index');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        try {
            if ($user->site_id) {
                $siteIds = $user->site_id ? explode(',', $user->site_id) : [];
                $siteIds = array_diff($siteIds, [$id]);
                EntitySite::whereIn('id', $siteIds)->delete();
            }
            GeneralSetting::where('user_id', $user->id)->delete();
            Content::where('user_id', $user->id)->delete();
            EntitySite::where('entity_id', $user->id)->delete();
            Host::where('user_id', $user->id)->delete();
            User::where('entity_id', $user->id)->where('type', 'user')->delete();
            VisitorCheckIn::where('user_id', $user->id)->delete();

            $user->delete();
            return response()->json([
                'success' => true,
                'message' => __('Entity has been successfully deleted.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete Entity: ') . $e->getMessage(),
            ], 500);
        }
    }

    public function resetPassword($id)
    {
        $user = User::find($id);
        if ($user) {
            $randomPassword = genUniqueStr('users', 'password', 8, '', true);

            $user->update([
                'password' => $randomPassword,
            ]);

            try {
                Mail::to($user->email)->send(new EntityPasswordResetMail($user, $randomPassword));
            } catch (\Exception $e) {
                flash_session('error', $e->getMessage());
            }
            flash_session('success', 'Entity password reset successfully.');
        } else {
            flash_session('success', 'Entity not found');
        }

        return redirect()->route('admin.entity.index');
    }
}
