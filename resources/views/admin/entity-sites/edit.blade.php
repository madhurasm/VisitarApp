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
                            <form id="main_form" action="{{ route('admin.entity-sites.update',$data['site']['id']) }}" method="POST" enctype="multipart/form-data" >
                                @csrf
                                @method('PATCH')
                                @include('admin.entity-sites.form')
                                <div class="row justify-content-end">
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
        let id = "{{$data['site']['id']}}";
        $(document).ready(function () {
            $('#entity').select2({

            });
            // $('.select2-selection').css({
            //     'height': '35px',
            //     'line-height': '35px',
            //     'display': 'flex',
            //     'align-items': 'center'
            // });

            $("#main_form").validate({
                rules: {
                    entity: { required: true},
                    site_name: { required: true},
                    site_location: { required: true},
                },
                messages: {
                    entity: {
                        required: "{{__('admin.errors.entity.required')}}",
                    },
                    site_name: {
                        required: "{{__('admin.errors.site_name.required')}}",
                    },
                    site_location: {
                        required: "{{__('admin.errors.site_location.required')}}",
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
    </script>
@endsection
