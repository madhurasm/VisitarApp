@extends('layouts.admin.app')

@section('head_css')
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include('layouts.admin.breadcrumb')

        <div class="row">
            <div class="col-xl-4">
                <div class="card overflow-hidden">
                    <div class="bg-primary bg-soft">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-primary p-3">
                                    <h5 class="text-primary">Welcome Back !</h5>
                                    <p>{{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }} Dashboard</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium">Users</p>
                                        <h4 class="mb-0">{{$user_count}}</h4>
                                    </div>
                                    <div class="flex-shrink-0 align-self-center ">
                                        <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                              <i class="bx bx-user-circle font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium">Content</p>
                                        <h4 class="mb-0">{{$content_count}}</h4>
                                    </div>
                                    <div class="flex-shrink-0 align-self-center ">
                                        <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                <i class="bx bx-detail font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
{{--                    <div class="col-md-4">--}}
{{--                        <div class="card mini-stats-wid">--}}
{{--                            <div class="card-body">--}}
{{--                                <div class="d-flex">--}}
{{--                                    <div class="flex-grow-1">--}}
{{--                                        <p class="text-muted fw-medium">Average Price</p>--}}
{{--                                        <h4 class="mb-0">$16.2</h4>--}}
{{--                                    </div>--}}
{{--                                    <div class="flex-shrink-0 align-self-center">--}}
{{--                                        <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">--}}
{{--                                            <span class="avatar-title rounded-circle bg-primary">--}}
{{--                                                <i class="bx bx-purchase-tag-alt font-size-24"></i>--}}
{{--                                            </span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_script')
@endsection
