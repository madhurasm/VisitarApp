<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use App\Models\User;
use App\Models\PersonalAccessToken;
use App\Http\Controllers\Controller;
use App\Models\VisitorCheckIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $users = User::query();
            $users = $users->where(['type' => 'user']);
            if (Auth::user()->type == 'entity') {
                $users = $users->where('entity_id', Auth::id());
            }

            $users = $users->latest('id')->get();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('profile_image', function ($row) {
                    return get_fancy_box_html($row->profile_image);
                })
                ->addColumn('entity', content: function ($row) {
                    return $row->entity ? $row->entity->name : 'N/A';
                })
                ->addColumn('mobile', function ($row) {
                    return $row->country_code . ' ' . $row->mobile;
                })
                ->addColumn('status', content: function ($row) {
                    return get_generate_switch($row->status, $row->id, route('admin.users.update-status', $row->id));

                })
                ->addColumn('action', function ($row) {
                    $params = [
                        'id' => $row->id,
                        'url' => [
//                            'edit' => route('admin.users.edit', $row->id),
                            'view' => route('admin.users.show', $row->id),
                            'delete' => route('admin.users.destroy', $row->id),
                        ]
                    ];

                    // Generate action buttons using the helper function
                    return generate_actions_buttons($params);
                })
                ->rawColumns(['profile_image', 'entity', 'status', 'action'])
                ->make(true);
        }

        return view('admin.users.index', [
            'title' => "Receptionists",
            'breadcrumbs' => [
                'Receptionists' => route('admin.users.index'),
            ]
        ]);
    }

    public function create()
    {
        return view('admin.users.create', [
            'title' => "Receptionist",
            'breadcrumbs' => [
                'Receptionist' => route('admin.users.index'),
                'Create' => route('admin.users.create'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at')],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        flash_session('success', 'Receptionist created successfully.');
        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        return view('admin.users.show', [
            'title' => "Receptionist",
            'data' => $user,
            'breadcrumbs' => [
                'Receptionist' => route('admin.users.index'),
                'View' => route('admin.users.show', $user->id),
            ]
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->update(['status' => ($user->status == "inactive") ? "active" : "inactive"]);
            $user->save();
            if ($user->status == "inactive") {
                PersonalAccessToken::where('tokenable_id', $user->id)->where('tokenable_type', 'App\Models\User')->delete();
            }
            return response()->json(['success' => true, 'message' => 'Receptionist status updated successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Receptionist not found.']);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'title' => "Receptionist",
            'data' => $user,
            'breadcrumbs' => [
                'Receptionist' => route('admin.users.index'),
                'edit' => route('admin.users.edit', $user->id),
            ]
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        flash_session('success', 'Receptionist updated successfully.');
        return redirect()->route('admin.users.index');
    }

    public function destroy(User $user)
    {
        try {
            PersonalAccessToken::where('tokenable_id', $user->id)->where('tokenable_type', 'App\Models\User')->delete();
            VisitorCheckIn::where('receptionist_id', $user->id)->delete();

            $user->delete();
            return response()->json([
                'success' => true,
                'message' => __('Receptionist has been successfully deleted.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete receptionist: ') . $e->getMessage(),
            ], 500);
        }
    }
}
