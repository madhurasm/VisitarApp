@extends('layouts.admin.guest.app')

@section('head_css')
    <style>
        body {
            text-align: center;
            padding: 40px 0;
            background: #EBF0F5;
        }
        h1 {
            font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
            font-weight: 900;
            font-size: 40px;
            margin-bottom: 10px;
        }
        p {
            color: #404F5E;
            font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
            font-size: 20px;
            margin: 0;
        }
        i {
            font-size: 100px;
            line-height: 200px;
            margin-left: -15px;
        }
        .card {
            background: white;
            padding: 60px;
            border-radius: 4px;
            box-shadow: 0 2px 3px #C8D0D8;
            display: inline-block;
            margin: 0 auto;
        }
    </style>
@endsection

@section('content')
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        @if($action == 'accepted')
                            <div style="">
                                <i class="checkmark fa fa-check" style="color: #88B04B;"></i> <!-- Green check -->
                            </div>
                            <h1 style="color: #88B04B;">Accepted</h1>
                            <p>Your request has been accepted</p>
                        @elseif($action == 'rejected')
                            <div style="">
                                <i class="fa fa-times" style="color: #DC3545;"></i> <!-- Red cross -->
                            </div>
                            <h1 style="color: #DC3545;">Rejected</h1>
                            <p>Your request has been rejected.</p>
                        @else
                            <div style="">
                                <i class="checkmark fa fa-times" style="color: #6C757D;"></i> <!-- Gray cross -->
                            </div>
                            <h1 style="color: #6C757D;">Request Not Found</h1>
                            <p>Your request could not be found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

