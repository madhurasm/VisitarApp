<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitorCheckIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class VisitorCheckInController extends Controller
{

    public function index(Request $request)
    {
        if (request()->ajax()) {
            $users = VisitorCheckIn::query();

            if (Auth::user()->type == 'entity') {
                $users = $users->where('user_id', Auth::id());
            }

            if (Auth::user()->type == 'user') {
                $users = $users->where('receptionist_id', Auth::id());
            }

            if (request()->input('site_filter', []) && !empty($request->site_filter)) {
                $siteIds = explode(',', $request->site_filter);
                $users->whereIn('site_id', $siteIds);
            }

            $users = $users->latest('id')->get();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('site_name', content: function ($row) {
                    return $row->site ? $row->site->name : 'N/A';
                })
                ->addColumn('entity', content: function ($row) {
                    return $row->entity ? $row->entity->name : 'N/A';
                })
                ->addColumn('receptionist', content: function ($row) {
                    return $row->receptionist ? $row->receptionist->name : 'N/A';
                })
                ->addColumn('profile_image', function ($row) {
                    return get_fancy_box_html($row->profile_image);
                })
                ->addColumn('mobile', function ($row) {
                    return $row->country_code . ' ' . $row->mobile;
                })
                ->addColumn('check_in', function ($row) {
                    return general_date_time($row->check_in);
                })
                ->addColumn('check_out', function ($row) {
                    return $row->check_out ? general_date_time($row->check_out) : '<span class="badge bg-danger">Not Available</span>';
                })
                ->addColumn('visit_request_status', function ($row) {
                    if ($row->visit_request_status == 'accepted') {
                        $status = '<span class="badge bg-success">Accepted</span>';
                    } elseif ($row->visit_request_status == 'rejected') {
                        $status = '<span class="badge bg-primary">Rejected</span>';
                    } else {
                        $status = '<span class="badge bg-secondary">Pending</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $params = [
                        'id' => $row->id,
                        'url' => [
//                            'edit' => route('admin.visitor_check_ins.edit', $row->id),
                            'view' => route('admin.visitor-check-ins.show', $row->id),
//                            'delete' => route('admin.visitor-check-ins.destroy', $row->id),
                            'checkout' => route('admin.visitor-check-ins.checkout', ['id' => $row->id]),
                        ]
                    ];
                    if (!is_null($row->check_out)) {
                        unset($params['url']['checkout']);
                    }

                    // Generate action buttons using the helper function
                    return generate_actions_buttons($params);
                })
                ->rawColumns(['profile_image', 'entity', 'receptionist', 'check_out', 'visit_request_status', 'action'])
                ->make(true);
        }

        return view('admin.visitor_check_ins.index', [
            'title' => "Visitor Check-Ins",
            'breadcrumbs' => [
                'Visitor Check-Ins' => route('admin.visitor-check-ins.index'),
            ]
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(VisitorCheckIn $VisitorCheckIn)
    {
        return view('admin.visitor_check_ins.show', [
            'title' => "Visitor Check-Ins",
            'data' => $VisitorCheckIn,
            'breadcrumbs' => [
                'Visitor Check-Ins' => route('admin.visitor-check-ins.index'),
                'View' => route('admin.visitor-check-ins.show', $VisitorCheckIn->id),
            ]
        ]);
    }

    public function edit(VisitorCheckIn $VisitorCheckIn)
    {
        //
    }

    public function update(Request $request, VisitorCheckIn $VisitorCheckIn)
    {
        //
    }

    public function destroy(VisitorCheckIn $VisitorCheckIn)
    {
        try {
            $VisitorCheckIn->delete();
            return response()->json([
                'success' => true,
                'message' => __('Visitor Check In has been successfully deleted.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete visitor Check In: ') . $e->getMessage(),
            ], 500);
        }
    }

    public function checkout(Request $request)
    {
        $VisitorCheckIn = VisitorCheckIn::find($request->id);

        try {
            $VisitorCheckIn->update([
                'check_out' => now(),
            ]);
            return response()->json([
                'success' => true,
                'message' => __('Visitor has been successfully checked out.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Visitor checkout failed: ') . $e->getMessage(),
            ], 500);
        }
    }


}
