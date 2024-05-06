@extends('layouts.master')

@php
    $page_title = __('API Settings');
    $menu_items = [__('Settings'), $page_title];
@endphp

@section('title', $page_title)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6">
                <form class="card" action="{{route('admin.settings.api.update', [], false)}}" method="post" enctype="multipart/form-data" onsubmit="disableSubmitBtn()">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="bank_mid" class="form-label">{{__('API Key')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" id="bank_mid" placeholder="eg. Webxpay" value="{{$api_settings->api_key}}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button class="btn btn-primary" type="submit">{{__('Regenerate API Key')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
