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
                                        <p>{{ App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_NAME') }}.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="auth-logo text-center">
                                <a href="{{route('admin.login')}}" class="auth-logo-dark">
                                    <div class="mb-2 mt-2">
                                    <span class="bg-light">
                                        <img src="{{checkFileExist(App\Models\GeneralSetting::getSiteSettingValue(1, 'SITE_LOGO'))}}" alt="" class="" height="100">
                                    </span>
                                    </div>
                                </a>
                            </div>
                            <div class="p-2">
                                <form id="action-form" class="form-horizontal" action="{{ route('admin.accept_or_reject_notification_verify') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="visitor_token" value="{{$token}}">
                                    <div class="mt-3 d-grid">
                                        <button class="action-btn btn btn-primary waves-effect waves-light" type="submit" name="action" value="accepted">
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            Accept Request
                                        </button>
                                    </div>
                                    <div class="mt-3 d-grid">
                                        <button class="action-btn btn btn-primary waves-effect waves-light" type="submit" name="action" value="rejected">
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            Reject Request
                                        </button>
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
            $("#action-form").validate({
                errorClass: "text-danger",
                errorElement: "small",
                highlight: function (element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function (element) {
                    $(element).removeClass("is-invalid");
                },
                submitHandler: function (form) {
                    // Show loader
                    const bothBtn = $(".action-btn");
                    bothBtn.addClass('disabled');

                    const actionBtn = $(form).find("button[type=submit]:focus");
                    actionBtn.prop("disabled", true); // Disable the button to prevent multiple clicks
                    actionBtn.find(".spinner-border").removeClass("d-none"); // Show the spinner
                    actionBtn.contents().last().replaceWith(" <i class='mdi mdi-spin me-1'></i>"); // Update button text

                    form.submit();
                }
            });
        });
    </script>
@endsection
