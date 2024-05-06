@extends('layouts.master')

@php
    $page_title = __('Payment Settings');
    $menu_items = [__('Settings'), $page_title];
@endphp

@section('title', $page_title)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6">
                <form class="card" action="{{route('admin.settings.payment.update', [], false)}}" method="post" enctype="multipart/form-data" onsubmit="disableSubmitBtn()">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="bank_mid" class="form-label">{{__('Bank MID')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" id="bank_mid" placeholder="eg. Webxpay" name="bank_mid" value="{{$payment_settings->bank_mid}}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="merchant_number" class="form-label">{{__('Merchant Number')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" placeholder="eg. Webxpay" id="merchant_number" name="merchant_number" value="{{$payment_settings->merchant_number}}" required>
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
