@extends('layouts.admin.guest.app')

@section('head_css')
@endsection

@section('content')
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Welcome Back !</h5>
                                        <p>Sign in to continue to the Admin Portal</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="auth-logo text-center">
                                <a href="{{route('admin.password.update')}}" class="auth-logo-dark">
                                    <div class="mb-2 mt-2">
                                    <span class="">
                                        <img src="{{checkFileExist(App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_LOGO'))}}" alt="" class="" height="100">
                                    </span>
                                    </div>
                                </a>
                            </div>
                            <div class="p-2">
                                <div class="alert alert-success text-center mb-4" role="alert">
                                    Enter your Email and instructions will be sent to you!
                                </div>
                                <form id="forgot-form" class="form-horizontal" action="{{ route('admin.password.email') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
                                    </div>
                                    <div class="mt-3 d-grid">
                                        <button id="forgot-btn" class="btn btn-primary waves-effect waves-light" type="submit">
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            Reset
                                        </button>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <a href="{{route('admin.login')}}" class="text-muted"><i class="mdi mdi-login me-1"></i> Sign In here</a>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <div>
                            <p>© <script>document.write(new Date().getFullYear())</script> <i class="mdi mdi-heart text-danger"></i> by {{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_script')
    <script>
        jQuery.validator.addMethod("regex", function(value, element, regexpr) {
            return regexpr.test(value);
        }, "Please enter a valid email address");

        $(document).ready(function () {
            $("#forgot-form").validate({
                rules: {
                    email: {
                        required: true,
                        email: true,
                        regex: /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/,
                    },
                },
                messages: {
                    email: {
                        required: "{{__('admin.errors.email.required')}}",
                        email: "{{__('admin.errors.email.format')}}",
                    },
                },
                errorClass: "text-danger",
                errorElement: "small",
                highlight: function (element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function (element) {
                    $(element).removeClass("is-invalid");
                },
                errorPlacement: function (error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function (form) {
                    // Show loader
                    const loginBtn = $("#forgot-btn");
                    loginBtn.prop("disabled", true); // Disable the button to prevent multiple clicks
                    loginBtn.find(".spinner-border").removeClass("d-none"); // Show the spinner
                    loginBtn.contents().last().replaceWith(" <i class='mdi mdi-spin me-1'></i>"); // Update button text

                    form.submit();
                }
            });
        });
    </script>
@endsection
