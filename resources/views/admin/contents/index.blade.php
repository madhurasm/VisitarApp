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
{{--                            @if(Auth::user()->type == 'entity')--}}
{{--                                @if(count(Auth::user()->contents) == 0)--}}
{{--                                    <div class="text-end">--}}
{{--                                        <a href="{{route('admin.contents.create')}}" class="btn btn-primary waves-effect btn-label waves-light"><i class="bx bx-plus label-icon"></i> Add Waiver Policy</a>--}}
{{--                                    </div>--}}
{{--                                @endif--}}
{{--                            @endif--}}
                            <h4 class="card-title">All Contents</h4>
                            <table id="contents-table" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Title</th>
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
            $('#contents-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.contents.index') }}',
                },
                language: {
                    processing: `<div class="spinner-border text-danger" role="status">
                                <span class="sr-only">Loading...</span>
                                </div>`
                },
                order: [],
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title' },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endsection
