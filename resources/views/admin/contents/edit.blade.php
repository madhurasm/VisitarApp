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
                            <form id="main_form" class="" action="{{ route('admin.contents.update',$unique_id) }}" method="POST" enctype="multipart/form-data" >
                                @csrf
                                @method('PATCH')
                                <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist" id="tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#en" role="tab" aria-selected="false">
                                            <span>English (en)</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#es" role="tab" aria-selected="false">
                                            <span>Spanish (es)</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content py-3">
                                @if(isset($contents) && $contents->count() > 0)
                                    @foreach($contents as $key => $value)
                                        <div class="tab-pane {{($loop->first) ? 'active' : ''}}" id="{{$value->lang}}" role="tabpanel">
                                            @if(Auth::user()->type == 'admin')
                                            <div class="row mb-4">
                                                <label for="example-text-input" class="col-sm-3 col-form-label"><span class="text-danger">*</span>{{__('Title')}}</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="title_{{$value->lang}}" id="title_{{$value->lang}}" placeholder="Enter Title" class="form-control content_title" value="{{($value->title) ?? ''}}">
                                                </div>
                                            </div>
                                            @endif
                                            <div class="row mb-4">
                                                <label for="example-text-input" class="col-sm-3 col-form-label"><span class="text-danger">*</span>{{__('Content')}}</label>
                                                <div class="col-sm-9" id="desc">
                                                    <textarea class="content_{{$value->lang}}" name="content_{{$value->lang}}" placeholder="Enter Content" id="content_{{$value->lang}}" cols="30" rows="10">{!! $value->content !!}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                </div>
{{--                                <div class="row mb-4">--}}
{{--                                    <label for="title" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Title</label>--}}
{{--                                    <div class="col-sm-9">--}}
{{--                                        <input class="form-control" type="text" id="title" name="title" value="{{@$data->title}}" placeholder="Please enter title">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="row mb-4">--}}
{{--                                    <label for="details" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Details</label>--}}
{{--                                    <div class="col-sm-9">--}}
{{--                                        <textarea id="content" name="details">{{@$data->content}}</textarea>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
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
    <!--tinymce js-->
    <script src="{{url('assets/admin/libs/tinymce/tinymce.min.js')}}"></script>

    <!-- init js -->
{{--    <script src="{{url('assets/admin/js/pages/form-editor.init.js')}}"></script>--}}
    <script>

        tinymce.init({
            plugins: "link",
            toolbar: [
                { name: 'history', items: [ 'undo', 'redo' ] },
                { name: 'styles', items: [ 'styles' ] },
                { name: 'formatting', items: [ 'bold', 'italic' ] },
                { name: 'alignment', items: [ 'alignleft', 'aligncenter', 'alignright', 'alignjustify' ] },
                { name: 'indentation', items: [ 'outdent', 'indent' ] },
                { name: 'link', items: [ 'link','codesample' ] },
                { name: 'insert', items: ['codesample'] } // Added codesample to the toolbar
            ],
            codesample_languages: [
                {text: 'HTML/XML', value: 'markup'},
                {text: 'JavaScript', value: 'javascript'},
                {text: 'CSS', value: 'css'},
                {text: 'PHP', value: 'php'},
                {text: 'Ruby', value: 'ruby'},
                {text: 'Python', value: 'python'},
                {text: 'Java', value: 'java'},
                {text: 'C', value: 'c'},
                {text: 'C#', value: 'csharp'},
                {text: 'C++', value: 'cpp'}
            ],
            selector: 'textarea',
            height: 500,
            // theme: 'modern',
        });

        $(document).ready(function () {
            $("#main_form").validate({
                rules: {
                    @foreach(getAllLanguage() as $key=>$value)
                    @if(Auth::user()->type == 'admin')
                    title_{{$value->code}}: {required: true},
                    @endif
                    content_{{$value->code}}: {required: true},
                    @endforeach
                },
                messages: {
                    @foreach(getAllLanguage() as $key=>$value)
                    @if(Auth::user()->type == 'admin')
                    title_{{$value->code}}: { required: "{{__('admin.errors.title.required')}}" },
                    @endif
                    content_{{$value->code}}: { required: "{{__('admin.errors.details.required')}}" },
                    @endforeach
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
                    if (element.attr("name") == "content_en" || element.attr("name") == "content_es") {
                        error.insertAfter($('#desc'));
                    } else {
                        error.insertAfter(element);
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
