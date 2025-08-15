<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>{{isset($title) ? ucfirst($title).' | ':''}}{{ ucfirst(App\Models\GeneralSetting::getSiteSettingValue(\Auth::id(), 'SITE_NAME')) }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{checkFileExist(App\Models\GeneralSetting::getSiteSettingValue(1, 'FAVICON'))}}">
    <!-- DataTables -->
    <link href="{{url('assets/admin/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/admin/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{url('assets/admin/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Bootstrap Css -->
    <link href="{{url('assets/admin/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{url('assets/admin/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Toastr Css -->
    <link href="{{url('assets/admin/libs/toastr/build/toastr.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/admin/libs/fancybox/jquery.fancybox.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/admin/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{url('assets/admin/css/app.css')}}" id="app-style" rel="stylesheet" type="text/css" />
    @if(\Auth::user()->type == 'admin')
    <link href="{{url('assets/admin/css/app-change.css')}}" id="app-style" rel="stylesheet" type="text/css" />
    @endif
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 35px !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .fancybox-button--zoom{
            display: none !important;
        }
    </style>
    @yield('head_css')
</head>

<body data-sidebar="dark">
    <div id="layout-wrapper">
        @include('layouts.admin.top-header')
        @include('layouts.admin.sidebar')

        <div class="main-content">
            @yield('content')
            {!! get_error_html($errors) !!}
            @include('layouts.admin.footer')
        </div>
    </div>
    <!-- JAVASCRIPT -->
    <script src="{{url('assets/admin/libs/jquery/jquery.min.js')}}"></script>
    <script src="{{url('assets/admin/libs/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{url('assets/admin/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{url('assets/admin/libs/metismenu/metisMenu.min.js')}}"></script>
    <script src="{{url('assets/admin/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{url('assets/admin/libs/node-waves/waves.min.js')}}"></script>

    <!-- Required datatable js -->
    <script src="{{url('assets/admin/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{url('assets/admin/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>

    <!-- Responsive examples -->
    <script src="{{url('assets/admin/libs/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{url('assets/admin/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>

    <!-- dashboard init -->
    <script src="{{url('assets/admin/libs/toastr/build/toastr.min.js')}}"></script>
    <script src="{{url('assets/admin/libs/fancybox/jquery.fancybox.min.js')}}"></script>
    <script src="{{url('assets/admin/libs/sweetalert2/sweetalert2.min.js')}}"></script>

    <!-- App js -->
    <script src="{{url('assets/admin/js/app.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @yield('page_script')
    <script>
        @if(session()->has('message'))
            toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };

        @if(session('message')['type'] === 'success')
        toastr.success("{{ session('message')['text'] }}");
        @elseif(session('message')['type'] === 'error')
        toastr.error("{{ session('message')['text'] }}");
        @elseif(session('message')['type'] === 'warning')
        toastr.warning("{{ session('message')['text'] }}");
        @elseif(session('message')['type'] === 'info')
        toastr.info("{{ session('message')['text'] }}");
        @endif
        @endif
    </script>
    <script>
        $(document).on('change', '.status-switch', function () {
            $.ajax({
                url: this.dataset.url,
                dataType: 'Json',
                success: function (r) {
                    let msg = r.message;
                    if (r.success) {
                        toastr.success(msg);
                    } else {
                        toastr.error(msg);
                    }
                }
            });
        });
        document.addEventListener("DOMContentLoaded", function () {
            $(document).on('click', '.btnDelete', function (e) {
                e.preventDefault();

                const deleteUrl = $(this).attr('href');

                // Use SweetAlert2 for confirmation
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#34c38f",
                    cancelButtonColor: "#f46a6a",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel",
                }).then(function (result) {
                    if (result.isConfirmed) {
                        // If confirmed, proceed with the AJAX delete request
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                            success: function (response) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.message,
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                $('.table').DataTable().ajax.reload();
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    title: "Error!",
                                    text: xhr.responseJSON.message || 'Failed to delete.',
                                    icon: "error",
                                });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btnCheckout', function (e) {
                e.preventDefault();

                const checkoutUrl = $(this).attr('href');

                // Use SweetAlert2 for confirmation
                Swal.fire({
                    title: "Proceed to Checkout?",
                    text: "Are you sure you want to proceed with checkout?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#34c38f",
                    cancelButtonColor: "#f46a6a",
                    confirmButtonText: "Yes, Checkout!",
                    cancelButtonText: "Cancel",
                }).then(function (result) {
                    if (result.isConfirmed) {
                        // If confirmed, proceed with the AJAX delete request
                        $.ajax({
                            url: checkoutUrl,
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                            success: function (response) {
                                Swal.fire({
                                    title: "Checkout",
                                    text: response.message || "Checkout completed successfully.",
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                $('.table').DataTable().ajax.reload();
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    title: "Error!",
                                    text: xhr.responseJSON.message || 'Failed to process checkout.',
                                    icon: "error",
                                });
                            }
                        });
                    }
                });
            });
        });


        $('#vertical-menu-btn').on('click', function () {
            if ($('body').hasClass('vertical-collpsed')) {
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                localStorage.setItem('sidebarCollapsed', 'false');
            }
        });

        if(localStorage.getItem('sidebarCollapsed') === 'true') {
            $('body').addClass('vertical-collpsed');
        } else {
            $('body').removeClass('vertical-collpsed');
        }
    </script>
</body>
</html>
