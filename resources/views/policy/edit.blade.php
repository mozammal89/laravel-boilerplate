@extends('layouts.master')

@php
    $page_title = __('Update Policy');
    $menu_items = [__('Policy'), $page_title];
@endphp

@section('title', $page_title)

@section('style')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.snow.css" rel="stylesheet"/>
    <style>
        .ql-editor {
            min-height: 250px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6">
                <form class="card" action="{{route('admin.policy.update', ['key'=>$policy->key], false)}}" method="post" enctype="multipart/form-data" onsubmit="disableSubmitBtn()">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="title" class="form-label">{{__('Title')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" id="title" placeholder="eg. Webxpay" name="title" value="{{$policy->title}}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="id_value" class="form-label">{{__('Document Text')}} <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="value" id="id_value" rows="15" hidden="" required>{{$policy->value}}</textarea>
                                    <div id="snow-container">
                                        <div class="editor">{!! $policy->value !!}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button class="btn btn-primary" type="submit">{{__('Update Settings')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.js"></script>
    <script>
        let quill_editor = null;

        $(document).ready(function () {
            quill_editor = new Quill('#snow-container .editor', {
                theme: 'snow'
            });

            quill_editor.on('text-change', function (delta, oldDelta, source) {
                $("#id_value").html(quill_editor.root.innerHTML);
            })
        })
    </script>
@endsection
