<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EntityCreationMail;
use App\Models\Content;
use App\Models\EntitySite;
use App\Models\GeneralSetting;
use App\Models\Host;
use App\Models\User;
use App\Models\VisitorCheckIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class EntitySiteController extends Controller
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
            $users = EntitySite::query();

            if (Auth::user()->type == 'entity') {
                $users = $users->where('entity_id', Auth::id());
            }

            if ($request->has('entity_filter') && !empty($request->entity_filter)) {
                $users->where('entity_id', $request->entity_filter);
            }

            $users = $users->latest('id')->get();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name ? Str::limit($row->name, 20) : '';
                })
                ->addColumn('location', function ($row) {
                    return $row->location ? Str::limit($row->location, 20) : '';
                })
                ->addColumn('entity', content: function ($row) {
                    return $row->entity ? $row->entity->name : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $params = [
                        'id' => $row->id,
                        'url' => [
                            'edit' => route('admin.entity-sites.edit', $row->id),
//                            'view' => route('admin.entity-sites.show', $row->id),
                            'delete' => route('admin.entity-sites.destroy', $row->id),
                        ]
                    ];
                    if (Auth::user()->type == 'entity') {
                        unset($params['url']['delete']);
                    }
                    // Generate action buttons using the helper function
                    return generate_actions_buttons($params);
                })
                ->rawColumns(['name', 'location', 'entity', 'action'])
                ->make(true);
        }

        return view('admin.entity-sites.index', [
            'title' => "Sites",
            'data' => $data,
            'breadcrumbs' => [
                'Sites' => route('admin.entity-sites.index'),
            ]
        ]);
    }

    public function create()
    {
        $data['entities'] = $this->entity;

        return view('admin.entity-sites.create', [
            'title' => "Site",
            'data' => $data,
            'breadcrumbs' => [
                'Site' => route('admin.entity-sites.index'),
                'Create' => route('admin.entity-sites.create'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'entity' => ['required'],
            'site_name' => ['required', 'string', 'max:255'],
            'site_location' => ['required', 'string', 'max:255'],
        ]);

        $site = EntitySite::create([
            'entity_id' => $request->entity,
            'name' => $request->site_name,
            'location' => $request->site_location,
        ]);

        $user = User::where('type', 'entity')->where('id', $request->entity)->first();
        if ($user) {
            $existingSites = $user->site_id ? explode(',', $user->site_id) : [];
            $existingSites[] = $site->id;
            $user->site_id = implode(',', array_unique($existingSites));
            $user->save();
        }

        flash_session('success', 'Site created successfully.');
        return redirect()->route('admin.entity-sites.index');
    }

//    public function show(Request $request)
//    {
//        $user = $request->user();
//        return view('admin.entity-sites.show', [
//            'title' => "Entity",
//            'data' => $user,
//            'breadcrumbs' => [
//                'Entity' => route('admin.entity-sites.index'),
//                'View' => route('admin.entity-sites.show', $user->id),
//            ]
//        ]);
//    }

    public function edit($id)
    {
        $site = EntitySite::find($id);

        $data['entities'] = $this->entity;
        $data['site'] = $site;

        return view('admin.entity-sites.edit', [
            'title' => "Site",
            'data' => $data,
            'breadcrumbs' => [
                'Site' => route('admin.entity-sites.index'),
                'edit' => route('admin.entity-sites.edit', $site->id),
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_location' => ['required', 'string', 'max:255'],
        ]);

        $data = EntitySite::find($id);
        if (Auth::user()->type == 'admin') {
            $oldEntityId = $data->entity_id;
            $newEntityId = $request->entity;
            $data->entity_id = $newEntityId;

            // Update site_id for old entity (remove site from the list)
            $oldUser = User::where('type', 'entity')->where('id', $oldEntityId)->first();
            if ($oldUser) {
                $oldSites = explode(',', $oldUser->site_id);
                $oldSites = array_diff($oldSites, [$id]); // Remove current site ID
                $oldUser->site_id = implode(',', $oldSites);
                $oldUser->save();
            }

            // Update site_id for new entity (add site to the list)
            $newUser = User::where('type', 'entity')->where('id', $newEntityId)->first();
            if ($newUser) {
                $newSites = $newUser->site_id ? explode(',', $newUser->site_id) : [];
                $newSites[] = $id;
                $newUser->site_id = implode(',', array_unique($newSites)); // Ensure uniqueness
                $newUser->save();
            }
        }
        $data->name = $request->site_name;
        $data->location = $request->site_location;
        $data->save();

        flash_session('success', 'Site updated successfully.');
        return redirect()->route('admin.entity-sites.index');
    }

    public function destroy($id)
    {
        $data = EntitySite::find($id);
        try {
            $user = User::where('type', 'entity')->where('id', $data->entity_id)->first();
            if ($user) {
                $siteIds = $user->site_id ? explode(',', $user->site_id) : [];
                $siteIds = array_diff($siteIds, [$id]);
                $user->site_id = implode(',', $siteIds);
                $user->save();
            }
            VisitorCheckIn::where('site_id',$data->id)->delete();

            $data->delete();

            return response()->json([
                'success' => true,
                'message' => __('Site has been successfully deleted.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete site: ') . $e->getMessage(),
            ], 500);
        }
    }
}
