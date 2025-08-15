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
                            @if(Auth::user()->type == 'admin')
                                <div class="row mb-3 align-items-end">
                                    <!-- Filter on the Left Side -->
                                    <div class="col-md-3">
                                        <label class="col-form-label">Filter Entity Sites</label>
                                        <select class="form-control entity_filter" id="entity_filter" name="entity_filter">
                                            <option value="">All</option>
                                            @foreach($data['entities'] as $entity)
                                                <option value="{{ $entity->id }}">{{ $entity->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <button class="btn btn-primary w-100" id="apply_filter">Apply</button>
                                    </div>

                                    <!-- Add Button on the Right Side -->
                                    <div class="col-md-7 text-end">
                                        <a href="{{ route('admin.entity-sites.create') }}" class="btn btn-primary waves-effect btn-label waves-light">
                                            <i class="bx bx-plus label-icon"></i> Add
                                        </a>
                                    </div>
                                </div>
                            @endif
                            <h4 class="card-title">All Sites</h4>
                            <table id="users-table" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th>#ID</th>
                                    @if(Auth::user()->type == 'admin')
                                        <th>Entity Name</th>
                                    @endif
                                    <th>Site Name</th>
                                    <th>Site Location</th>
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
            $('.entity_filter').select2();
        });
    </script>
    <script>
        $(function() {
            oTable = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.entity-sites.index') }}',
                },
                language: {
                    processing: `<div class="spinner-border text-danger" role="status">
                                <span class="sr-only">Loading...</span>
                                </div>`
                },
                order: [],
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    @if(Auth::user()->type == 'admin') { data: 'entity' }, @endif
                    { data: 'name' },
                    { data: 'location' },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });

            $('#apply_filter').on('click', function (){
                var entity_filter = $('#entity_filter').val();
                oTable.ajax.url("{{ route('admin.entity-sites.index') }}?entity_filter=" + entity_filter).load();
            });
        });


    </script>
@endsection
