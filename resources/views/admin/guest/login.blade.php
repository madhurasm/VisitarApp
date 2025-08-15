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
                            <a href="{{route('admin.login')}}" class="auth-logo-dark">
                                <div class="mb-2 mt-2">
                                    <span class="">
                                        <img src="{{checkFileExist(App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_LOGO'))}}" alt="" class="" height="100">
                                    </span>
                                </div>
                            </a>
                        </div>
                        <div class="p-2">
                            <form id="login-form" class="form-horizontal" action="{{ route('admin.login.post') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username/Email</label>
                                    <input type="text" class="form-control" id="username" placeholder="Enter username/email" name="username">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group auth-pass-inputgroup">
                                        <input type="password" class="form-control" placeholder="Enter password" name="password" aria-label="Password" aria-describedby="password-addon">
                                        <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-check" name="remember">
                                    <label class="form-check-label" for="remember-check">
                                        Remember me
                                    </label>
                                </div>
                                <div class="mt-3 d-grid">
                                    <button id="login-btn" class="btn btn-primary waves-effect waves-light" type="submit">
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        Log In
                                    </button>
                                </div>
                                <div class="mt-4 text-center">
                                    <a href="{{ route('admin.password.request') }}" class="text-muted"><i class="mdi mdi-lock me-1"></i> Forgot your password?</a>
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
    $(document).ready(function () {
        $("#login-form").validate({
            rules: {
                username: {
                    required: true,
                },
                password: {
                    required: true,
                    minlength: 6
                }
            },
            messages: {
                username: {
                    required: "{{__('admin.errors.username_or_email.required')}}",
                },
                password: {
                    required: "{{__('admin.errors.password.required')}}",
                    minlength: "{{__('admin.errors.password.minlength')}}"
                }
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
                // Custom placement for password error message
                if (element.attr("name") == "password") {
                    error.insertAfter(".auth-pass-inputgroup");
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                // Show loader
                const loginBtn = $("#login-btn");
                loginBtn.prop("disabled", true); // Disable the button to prevent multiple clicks
                loginBtn.find(".spinner-border").removeClass("d-none"); // Show the spinner
                loginBtn.contents().last().replaceWith(" <i class='mdi mdi-spin me-1'></i>"); // Update button text

                form.submit();
            }
        });
    });
</script>
@endsection
