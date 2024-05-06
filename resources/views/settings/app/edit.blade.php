@extends('layouts.master')

@php
    $page_title = __('App Settings');
    $menu_items = [__('Settings'), $page_title];
@endphp

@section('title', $page_title)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6">
                <form class="card" action="{{route('admin.settings.app.update', [], false)}}" method="post" enctype="multipart/form-data" onsubmit="disableSubmitBtn()">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 edit-profile">
                                <div class="profile-title">
                                    <div class="media">
                                        <img class="m-r-10" alt="" src="{{$app_settings->app_icon}}" style="border: 2px solid #000000; height: 70px;">
                                        <img class="m-r-10" alt="" src="{{$app_settings->app_logo}}" style="border: 2px solid #000000; height: 70px;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">{{__('App Name')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" placeholder="eg. Webxpay" name="app_name" value="{{$app_settings->app_name}}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="id_app_logo">{{__('App Logo')}}</label>
                                    <input class="form-control" id="id_app_logo" type="file" name="app_logo" accept=".png">
                                    <small class="text-dark">{{__('Allowed extensions: png | Max size: 500Kb')}}</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="id_app_icon">{{__('App Icon')}}</label>
                                    <input class="form-control" id="id_app_icon" type="file" name="app_icon" accept=".png">
                                    <small class="text-dark">{{__('Allowed extensions: png | Max size: 500Kb')}}</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Primary Color')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="color" name="primary_color" value="{{$app_settings->primary_color}}" aria-label="primary_color" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Secondary Color')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="color" name="secondary_color" value="{{$app_settings->secondary_color}}" aria-label="secondary_color" required>
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
