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
                            <h4 class="card-title mb-4 text-center">Receptionist Information</h4>
                            <div class="card-title">
                                <div class="kt-widget__media text-center w-100">
                                    <a data-fancybox="gallery" href="{{$data->profile_image}}" style="margin-right: 15px;">
                                        <img class="img_75" src="{{$data->profile_image}}" alt="image" width="100" height="100" style="border-radius:10px">
                                    </a>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <h5 class="font-size-15">First Name:</h5>
                                    <p class="text-muted">{{$data->first_name}}</p>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="font-size-15">Last Name:</h5>
                                    <p class="text-muted">{{$data->last_name}}</p>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="font-size-15">Email:</h5>
                                    <p class="text-muted">
                                        @if($data->email)
                                            <a href="mailto:{{ $data->email }}">{{ $data->email }}</a>
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <h5 class="font-size-15">Mobile:</h5>
                                    <p class="text-muted">
                                        @if($data->country_code)
                                            <a href="tel:{{ $data->country_code . $data->mobile }}">
                                                {{ $data->country_code . ' ' . $data->mobile }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                                @if(Auth::user()->type == 'admin')
                                    <div class="col-md-4">
                                        <h5 class="font-size-15">Entity Name:</h5>
                                        <p class="text-muted">
                                            {{$data->entity->name ?? ''}}
                                        </p>
                                    </div>
                                @endif
{{--                                <div class="col-md-4">--}}
{{--                                    <h5 class="font-size-15">Location:</h5>--}}
{{--                                    <p class="text-muted">{{$data->location}}</p>--}}
{{--                                </div>--}}
                                <div class="col-md-4">
                                    <h5 class="font-size-15">Status:</h5>
                                    <p class="text-muted">{!! get_badge_html($data->status) !!}</p>
                                </div>
                            </div>
                            <hr>
{{--                            <div class="row">--}}
{{--                                @if(Auth::user()->type == 'admin')--}}
{{--                                <div class="col-md-4">--}}
{{--                                    <h5 class="font-size-15">Entity Name:</h5>--}}
{{--                                    <p class="text-muted">--}}
{{--                                        {{$data->entity->name ?? ''}}--}}
{{--                                    </p>--}}
{{--                                </div>--}}
{{--                                @endif--}}
{{--                                <div class="col-md-4">--}}
{{--                                    <h5 class="font-size-15">Site Name:</h5>--}}
{{--                                    <p class="text-muted">--}}
{{--                                        {{$data->site->name ?? ''}}--}}
{{--                                    </p>--}}
{{--                                </div>--}}
{{--                                <div class="col-md-4">--}}
{{--                                    <h5 class="font-size-15">Site Location:</h5>--}}
{{--                                    <p class="text-muted">--}}
{{--                                        {{$data->site->location ?? ''}}--}}
{{--                                    </p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                        </div>
                    </div>
                </div>
{{--                <div class="col-8">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-body">--}}
{{--                            <h4 class="card-title mb-4">Details</h4>--}}
{{--                            <hr>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </div>
    </div>
@endsection

@section('page_script')
@endsection
