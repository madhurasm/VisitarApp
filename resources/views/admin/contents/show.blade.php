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
                            <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#en" role="tab" aria-selected="false">
                                        <span>English</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#es" role="tab" aria-selected="false">
                                        <span>Spanish</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content py-3">
                            @if(isset($contents) && $contents->count() > 0)
                                @foreach($contents as $key => $value)
                                    <div class="tab-pane {{($loop->first) ? 'active' : ''}}" id="{{$value->lang}}" role="tabpanel">
                                        <h4 class="card-title mb-4">Details</h4>
                                        <hr>
                                        <h5 class="font-size-14">Title:</h5>
                                        <p class="text-muted">{{$value->title}}</p>
                                        <hr>
                                        <h5 class="font-size-14">Slug:</h5>
                                        <p class="text-muted">{{$value->slug}}</p>
                                        <hr>
                                        <h5 class="font-size-14">Content:</h5>
                                        <p class="text-muted">{!! $value->content !!}</p>
                                        <hr>
                                    </div>
                                @endforeach
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_script')
@endsection
