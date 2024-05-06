@extends('layouts.master')

@php
    $page_title = __('SMS Settings');
    $menu_items = [__('Settings'), $page_title];
@endphp

@section('title', $page_title)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6">
                <form class="card" action="{{route('admin.settings.sms.update', [], false)}}" method="post" enctype="multipart/form-data" onsubmit="disableSubmitBtn()">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="username" class="form-label">{{__('Username')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" id="bank_mid" placeholder="eg. Webxpay" name="username" value="{{$sms_settings->username}}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="password" class="form-label">{{__('Password')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" placeholder="eg. ************" id="password" name="password" value="{{$sms_settings->password}}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="masking_name" class="form-label">{{__('Masking Name')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" placeholder="eg. WEBXPAY" id="masking_name" name="masking_name" value="{{$sms_settings->masking_name}}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check checkbox checkbox-dark mb-0">
                                    <input class="form-check-input" id="checkbox-primary-active" type="checkbox" name="status" value="1" {{$sms_settings->status ? 'checked': ''}}>
                                    <label class="form-check-label text-dark" for="checkbox-primary-active">{{__('Is Active')}}</label>
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
