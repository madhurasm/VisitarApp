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
                            <h4 class="card-title mb-4 text-center">Check-In Visitor Information</h4>
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
                                    <h5 class="font-size-15">Name:</h5>
                                    <p class="text-muted">{{$data->name}}</p>
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
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <h5 class="font-size-15">Company Name:</h5>
                                    <p class="text-muted">{{$data->company_name}}</p>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="font-size-15">Purpose of Visit:</h5>
                                    <p class="text-muted">{!! $data->purpose_of_visit ? nl2br($data->purpose_of_visit) : '-' !!}</p>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="font-size-15">Host Name:</h5>
                                    <p class="text-muted">{{$data->host_name}}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <h5 class="font-size-15">Check-In At:</h5>
                                    <p class="text-muted">{!! $data->check_in ? general_date_time($data->check_in) : '<span class="badge bg-danger">Not Available</span>' !!}</p>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="font-size-15">Check-Out At:</h5>
                                    <p class="text-muted">{!! $data->check_out ? general_date_time($data->check_out) : '<span class="badge bg-danger">Not Available</span>' !!}</p>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="font-size-15">Total Visit Time:</h5>
                                    <p class="text-muted">
                                        @if($data->check_in && $data->check_out)
                                            @php
                                                $checkIn = \Carbon\Carbon::parse($data->check_in);
                                                $checkOut = \Carbon\Carbon::parse($data->check_out);
                                                $diff = $checkIn->diff($checkOut);
                                            @endphp
                                            {{ $diff->format('%h hours %i minutes') }}
                                        @else
                                            <span class="badge bg-danger">Not Available</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <h5 class="font-size-15">Site Name:</h5>
                                    <p class="text-muted">
                                        {{$data->site ? $data->site->name : ''}}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="font-size-15">Site Location:</h5>
                                    <p class="text-muted">
                                        {{$data->site ? $data->site->location : ''}}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="font-size-15">Visitor Request Status:</h5>
                                    <p class="text-muted">
                                        @if($data->visit_request_status == 'accepted')
                                            <span class="badge bg-success">Accepted</span>
                                        @elseif($data->visit_request_status == 'rejected')
                                            <span class="badge bg-primary">Rejected</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                @if(Auth::user()->type == 'admin')
                                    <div class="col-md-4">
                                        <h5 class="font-size-15">Entity Name:</h5>
                                        <p class="text-muted">
                                            {{$data->entity->name ?? ''}}
                                        </p>
                                    </div>
                                @endif
                                    <div class="col-md-4">
                                        <h5 class="font-size-15">Receptionist Name:</h5>
                                        <p class="text-muted">
                                            {{$data->receptionist->name ?? ''}}
                                        </p>
                                    </div>
                            </div>

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
