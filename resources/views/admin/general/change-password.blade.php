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
                            <form id="main_form" action="{{ route('admin.change-password.update') }}" method="POST">
                                @csrf
                                <div class="row mb-4">
                                    <label for="opassword" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Old Password</label>
                                    <div class="input-group auth-pass-inputgroup">
                                        <input type="password" class="form-control" id="opassword" name="opassword" placeholder="Please enter old password.">
                                        <button class="btn btn-light toggle-password" type="button" data-target="opassword">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="npassword" class="col-sm-3 col-form-label"><span class="text-danger">*</span>New Password</label>
                                    <div class="input-group auth-npass-inputgroup">
                                        <input type="password" class="form-control" id="npassword" name="npassword" placeholder="Please enter new password.">
                                        <button class="btn btn-light toggle-password" type="button" data-target="npassword">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="cpassword" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Confirm Password</label>
                                    <div class="input-group auth-cpass-inputgroup">
                                        <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Please enter confirm password.">
                                        <button class="btn btn-light toggle-password" type="button" data-target="cpassword">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-9">
                                        <div>
                                            <button id="submit-btn" class="btn btn-primary waves-effect waves-light" type="submit">
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
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

        $("#npassword-addon").on("click",function(){0<$(this).siblings("input").length&&("password"==$(this).siblings("input").attr("type")?$(this).siblings("input").attr("type","input"):$(this).siblings("input").attr("type","password"))});
        $("#cpassword-addon").on("click",function(){0<$(this).siblings("input").length&&("password"==$(this).siblings("input").attr("type")?$(this).siblings("input").attr("type","input"):$(this).siblings("input").attr("type","password"))});

        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function () {
                let targetId = this.getAttribute('data-target');
                let passwordField = document.getElementById(targetId);
                let icon = this.querySelector('i');

                if (passwordField) {
                    if (passwordField.type === "password") {
                        passwordField.type = "text";
                        icon.classList.replace("mdi-eye-outline", "mdi-eye-off-outline");
                    } else {
                        passwordField.type = "password";
                        icon.classList.replace("mdi-eye-off-outline", "mdi-eye-outline");
                    }
                }
            });
        });

        $(document).ready(function () {
            $("#main_form").validate({
                rules: {
                    opassword: { required: true},
                    npassword: { required: true, minlength: 8, strongPassword: true },
                    cpassword: { required: true, equalTo: "#npassword",},
                },
                messages: {
                    opassword: {
                        required: "{{__('admin.errors.opassword.required')}}",
                    },
                    npassword: {
                        required: "{{__('admin.errors.npassword.required')}}",
                        minlength: "{{__('admin.errors.npassword.minlength')}}",
                    },
                    cpassword: {
                        required: "{{__('admin.errors.cpassword.required')}}",
                        equalTo: "{{__('admin.errors.cpassword.equal_to')}}",
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
                    if (element.attr("name") == "opassword") {
                        error.insertAfter(".auth-pass-inputgroup");
                    } else if (element.attr("name") == "npassword") {
                        error.insertAfter(".auth-npass-inputgroup");
                    } else {
                        error.insertAfter(".auth-cpass-inputgroup");
                    }
                },
                submitHandler: function (form) {
                    // Show loader
                    const submitBtn = $("#submit-btn");
                    submitBtn.prop("disabled", true); // Disable the button to prevent multiple clicks
                    submitBtn.find(".spinner-border").removeClass("d-none"); // Show the spinner
                    submitBtn.contents().last().replaceWith(" <i class='mdi mdi-spin me-1'></i>"); // Update button text

                    form.submit();
                }
            });
        });
    </script>
@endsection
