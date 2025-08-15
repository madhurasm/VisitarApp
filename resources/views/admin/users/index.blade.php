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
{{--                            <div class="text-end">--}}
{{--                                <a href="{{route('admin.users.create')}}" class="btn btn-primary waves-effect btn-label waves-light"><i class="bx bx-plus label-icon"></i> Add</a>--}}
{{--                            </div>--}}
                            <h4 class="card-title">All Receptionists</h4>
                            <table id="users-table" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Profile</th>
                                    @if(Auth::user()->type == 'admin')
                                        <th>Entity Name</th>
                                    @endif
                                    <th>First Name</th>
                                    <th>Last Name</th>
{{--                                    <th>Username</th>--}}
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
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
        $(function() {
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.users.index') }}',
                },
                language: {
                    processing: `<div class="spinner-border text-danger" role="status">
                                <span class="sr-only">Loading...</span>
                                </div>`
                },
                order: [],
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'profile_image', orderable: false, searchable: false },
                        @if(Auth::user()->type == 'admin') { data: 'entity' }, @endif
                    { data: 'first_name' },
                    { data: 'last_name' },
                    // { data: 'username' },
                    { data: 'email' },
                    { data: 'mobile' },
                    { data: 'status', orderable: false, searchable: false },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endsection
