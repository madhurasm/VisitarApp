@extends('layouts.admin.guest.app')

@section('head_css')
@endsection

@section('content')
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
{{--                        <div class="bg-primary bg-soft">--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-12 text-center">--}}
{{--                                    <div class="text-primary p-4">--}}
{{--                                        <h5 class="text-primary">Welcome Back !</h5>--}}
{{--                                        <p>Sign in to continue to {{  App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }}.</p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="card-body pt-0">
                            <div class="auth-logo text-center mb-4">
                                <a href="{{route('admin.login')}}" class="auth-logo-dark">
                                    <div class="mb-2 mt-2">
                                    <span class="">
                                        <img src="{{checkFileExist(App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_LOGO'))}}" alt="" class="" height="100">
                                    </span>
                                    </div>
                                </a>
                            </div>
                            <div class="text-center mb-2">
                                <h2>Reset Password</h2>
                            </div>
                            <div class="p-2">
                                <form id="forgot-form" class="form-horizontal" action="{{ route('admin.password.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="reset_token" value="{{$token}}">
                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" class="form-control" placeholder="Enter password" id="password" name="password" aria-label="Password">
                                            <button class="btn btn-light toggle-password" type="button" data-target="password">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <div class="input-group auth-cpass-inputgroup">
                                            <input type="password" class="form-control" placeholder="Enter confirm password" id="confirm-password" name="confirmed" aria-label="Password">
                                            <button class="btn btn-light toggle-password" type="button" data-target="confirm-password">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-3 d-grid">
                                        <button id="forgot-btn" class="btn btn-primary waves-effect waves-light" type="submit">
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            Reset Password
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
                            <p>© <script>document.write(new Date().getFullYear())</script> <i class="mdi mdi-heart text-danger"></i> by {{  App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_script')

    <script>
        $.validator.addMethod("strongPassword", function (value, element) {
            return this.optional(element) ||
                /[A-Z]/.test(value) && // At least one uppercase letter
                /[a-z]/.test(value) && // At least one lowercase letter
                /[0-9]/.test(value) && // At least one digit
                /[@$!%*#?&]/.test(value) && // At least one special character
                value.length >= 8; // Minimum length of 8 characters
        }, "Your password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.");

        $(document).ready(function () {

            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function () {
                    let targetId = this.getAttribute('data-target');
                    let passwordField = document.getElementById(targetId);
                    let icon = this.querySelector('i');

                    if (passwordField.type === "password") {
                        passwordField.type = "text";
                        icon.classList.replace("mdi-eye-outline", "mdi-eye-off-outline");
                    } else {
                        passwordField.type = "password";
                        icon.classList.replace("mdi-eye-off-outline", "mdi-eye-outline");
                    }
                });
            });

            $("#forgot-form").validate({
                rules: {
                    password: {
                        required: true,
                        minlength: 8,
                        strongPassword: true
                    },
                    confirmed: {
                        required: true,
                        equalTo: "#password"
                    },
                },
                messages: {
                    password: {
                        required: "{{__('admin.errors.password.required')}}",
                        minlength: "{{__('admin.errors.password.minlength')}}"
                    },
                    confirmed: {
                        required: "{{__('admin.errors.confirm_password.required')}}",
                        equalTo: "{{__('admin.errors.confirm_password.equal_to')}}"
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
                    // Custom placement for password error message
                    if (element.attr("name") == "password") {
                        error.insertAfter(".auth-pass-inputgroup");
                    } else {
                        error.insertAfter(".auth-cpass-inputgroup");
                    }
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
