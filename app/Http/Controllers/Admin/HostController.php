<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Host;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class HostController extends Controller
{
    public $entity;

    public function __construct()
    {
        $this->entity = User::where(['status' => 'active', 'type' => 'entity'])->orderBy('name')->get();
    }

    public function index(Request $request)
    {
        $data['entities'] = $this->entity;

        if (request()->ajax()) {
            $users = Host::query();

            if (Auth::user()->type == 'entity') {
                $users = $users->where('user_id', Auth::id());
            }
            if ($request->has('entity_filter') && !empty($request->entity_filter)) {
                $users->where('user_id', $request->entity_filter);
            }

            $users = $users->latest('id')->get();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('entity', content: function ($row) {
                    return $row->entity ? $row->entity->name : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $params = [
                        'id' => $row->id,
                        'url' => [
//                            'edit' => route('admin.hosts.edit', $row->id),
//                            'view' => route('admin.hosts.show', $row->id),
                            'delete' => route('admin.hosts.destroy', $row->id),
                        ]
                    ];

                    // Generate action buttons using the helper function
                    return generate_actions_buttons($params);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.hosts.index', [
            'title' => "Hosts",
            'data' => $data,
            'breadcrumbs' => [
                'Hosts' => route('admin.hosts.index'),
            ]
        ]);
    }

    public function create()
    {
        $data['entities'] = $this->entity;

        return view('admin.hosts.create', [
            'title' => "Hosts",
            'data' => $data,
            'breadcrumbs' => [
                'Host' => route('admin.hosts.index'),
                'Create' => route('admin.hosts.create'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('hosts', 'email')],
        ]);
        if (Auth::user()->type == 'admin') {
            $entity_id = $request->entity;
        } else {
            $entity_id = Auth::id();
        }

        Host::create([
            'user_id' => $entity_id,
            'name' => $request->name,
            'email' => $request->email,
        ]);
        flash_session('success', 'Host created successfully.');
        return redirect()->route('admin.hosts.index');
    }

    public function show(Host $host)
    {
//        return view('admin.hosts.show',  [
//            'title' => "Receptionist",
//            'data' => $host,
//            'breadcrumbs' => [
//                'Receptionist' => route('admin.hosts.index'),
//                'View' => route('admin.hosts.show', $host->id),
//            ]
//        ]);
    }

    public function edit(Host $host)
    {
        $data['entities'] = $this->entity;
        $data['host'] = $host;

        return view('admin.hosts.edit', [
            'title' => "Host",
            'data' => $data,
            'breadcrumbs' => [
                'Receptionist' => route('admin.hosts.index'),
                'edit' => route('admin.hosts.edit', $host->id),
            ]
        ]);
    }

    public function update(Request $request, Host $host)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:hosts,email,' . $host->id,
        ]);

        $host->name = $request->name;
        $host->email = $request->email;
        $host->save();

        flash_session('success', 'Host updated successfully.');
        return redirect()->route('admin.hosts.index');
    }

    public function destroy(Host $host)
    {
        try {
            $host->delete();
            return response()->json([
                'success' => true,
                'message' => __('Host has been successfully deleted.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete host: ') . $e->getMessage(),
            ], 500);
        }
    }
}
