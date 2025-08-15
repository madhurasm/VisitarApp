@extends('layouts.admin.app')

@section('head_css')
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            @include('layouts.admin.breadcrumb')

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php
                            use App\Models\EntitySite;
                            use App\Models\User;
                            use Illuminate\Support\Facades\Auth;
                            $siteIds = [];
                            if (Auth::user()->type == 'user') {
                                $entity = User::where('id', Auth::user()->entity_id)->first();
                                if ($entity->site_id) {
                                    $siteIds = explode(',', $entity->site_id);
                                }
                            } elseif (Auth::user()->type == 'entity') {
                                if (Auth::user()->site_id) {
                                    $siteIds = explode(',', Auth::user()->site_id);
                                }
                            }
                            $site_records = !empty($siteIds) ? EntitySite::select('id', 'name', 'location')->whereIn('id', $siteIds)->latest()->get() : collect();
                            ?>
                            @if((Auth::user()->type == 'user' || Auth::user()->type == 'entity') && isset($site_records))
                                <div class="row mb-3 align-items-end">
                                    <!-- Filter on the Left Side -->
                                    <div class="col-md-10">
                                        <label class="col-form-label">Filter Entity Sites</label>
                                        <select class="form-control site_filter" id="site_filter" name="site_filter[]" multiple>
                                            @foreach($site_records as $site)
                                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <button class="btn btn-primary w-100" id="apply_filter">Apply</button>
                                    </div>
                                </div>
                            @endif
                            <h4 class="card-title">All Check-Ins</h4>
                            <table id="users-table" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Site Name</th>
                                    <th>Receptionist Name</th>
                                    <th>Host Name</th>
                                    <th>Visitor Profile</th>
                                    <th>Visitor Name</th>
                                    <th>Visitor Email</th>
                                    <th>Visitor Mobile</th>
                                    <th>Check-In Time</th>
                                    <th>Check-Out Time</th>
                                    <th>Visitor Request Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_script')
    <script>
        $(document).ready(function() {
            $('#site_filter').select2({
                placeholder: "All Sites",
                allowClear: true
            });
        });

        $(function() {
            oTable = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.visitor-check-ins.index') }}',
                },
                language: {
                    processing: `<div class="spinner-border text-danger" role="status">
                    <span class="sr-only">Loading...</span>
                 </div>`
                },
                order: [],
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'site_name' },
                    { data: 'receptionist' },
                    { data: 'host_name' },
                    { data: 'profile_image', orderable: false, searchable: false },
                    { data: 'name' },
                    { data: 'email' },
                    { data: 'mobile' },
                    { data: 'check_in' },
                    { data: 'check_out' },
                    { data: 'visit_request_status' },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });

            $('#apply_filter').on('click', function (){
                var site_filter = $('#site_filter').val();
                oTable.ajax.url("{{ route('admin.visitor-check-ins.index') }}?site_filter=" + site_filter).load();
            });
        });
    </script>
@endsection
