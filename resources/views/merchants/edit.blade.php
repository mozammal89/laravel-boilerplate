@extends('layouts.master')

@php
    $page_title = __('Edit Merchants');
    $menu_items = [__('Merchants'),$page_title];
@endphp

@section('title', $page_title)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6">
                <div class="card height-equal">
                    <div class="card-body custom-input">
                        <form class="row g-3" action="{{route('admin.merchants.update', ['id'=>$merchant_instance->id], false)}}" method="post" enctype="multipart/form-data" onsubmit="disableSubmitBtn()">
                            @csrf
                            <div class="col-12 edit-profile">
                                <div class="profile-title">
                                    <img class="m-r-10" alt="" src="{{$merchant_instance->merchant_logo}}" style="border: 2px solid #000000; height: 70px;">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="merchant_title">{{__('Merchant Title (Doing Business As)')}} <span class="text-danger">*</span></label>
                                <input class="form-control" id="merchant_title" name="merchant_title" type="text" placeholder="eg. Webxpay" value="{{$merchant_instance->merchant_title}}" aria-label="Name" required="">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="merchant_number">{{__('Merchant Number')}} <span class="text-danger">*</span></label>
                                <input class="form-control" id="merchant_number" name="merchant_number" type="text" placeholder="eg. 12345678" value="{{$merchant_instance->merchant_number}}" aria-label="Name" required="">
                            </div>
                            <div class="col-12">
                                <div class="mb-0">
                                    <label class="form-label" for="merchant_logo">{{__('Merchant Logo')}}</label>
                                    <input class="form-control" id="merchant_logo" type="file" name="merchant_logo" accept=".png">
                                    <small class="text-dark">{{__('Allowed extensions: png | Max size: 500Kb')}}</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check checkbox checkbox-dark mb-0">
                                    <input class="form-check-input" id="checkbox-primary-active" type="checkbox" name="is_active" value="1" {{$merchant_instance->status ? 'checked': ''}}>
                                    <label class="form-check-label text-dark" for="checkbox-primary-active">{{__('Is Active')}}</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary" type="submit">{{__('Submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
