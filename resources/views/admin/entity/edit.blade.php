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
                            <form id="main_form" action="{{ route('admin.entity.update',$data->id) }}" method="POST" enctype="multipart/form-data" >
                                @csrf
                                @method('PATCH')
                                @include('admin.entity.form')
                                <div class="row justify-content-end">
                                    <div class="col-sm-6">
                                        <div>
                                            <button id="submit-btn" class="btn btn-primary waves-effect waves-light" type="submit">
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                Submit
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div>
                                            <a href="{{ route('admin.entity.reset-password',$data->id) }}" id="reset-password-btn" class="btn btn-primary waves-effect waves-light"><span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> Reset Password</a>
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
        jQuery.validator.addMethod("regex", function(value, element, regexpr) {
            return regexpr.test(value);
        }, "Please enter a valid email address");

        let id = "{{$data->id}}";
        $(document).ready(function () {
            $("#main_form").validate({
                rules: {
                    name: { required: true},
                    email: {
                        required: true,
                        email: true,
                        regex: /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/,
                        remote: {
                            type: 'get',
                            url: "{{route('admin.user-availability-checker')}}",
                            data: {
                                id: id,
                                'type': "email",
                                'val': function() {
                                    return $('#email').val();
                                }
                            },
                        },
                    },
                },
                messages: {
                    email: {
                        required: "{{__('admin.errors.email.required')}}",
                        remote: "{{__('admin.errors.email.remote')}}",
                    },
                    name: {
                        required: "{{__('admin.errors.name.required')}}",
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
                    const submitBtn = $("#submit-btn");
                    submitBtn.prop("disabled", true); // Disable the button to prevent multiple clicks
                    submitBtn.find(".spinner-border").removeClass("d-none"); // Show the spinner
                    submitBtn.contents().last().replaceWith(" <i class='mdi mdi-spin me-1'></i>"); // Update button text

                    form.submit();
                }
            });
        });

        $('#reset-password-btn').on('click', function (){
            const resetBtn = $("#reset-password-btn");
            resetBtn.prop("disabled", true); // Disable the button to prevent multiple clicks
            resetBtn.find(".spinner-border").removeClass("d-none"); // Show the spinner
            resetBtn.contents().last().replaceWith(" <i class='mdi mdi-spin me-1'></i>"); // Update button text
        });
    </script>
@endsection
