@extends('layouts.master')

@php
    if(route('admin.my.profile',[], false) == '/' . request()->path()){
        $page_title = __('My Profile');
        $update_url = '';
    } else {
        $page_title = __('Edit Profile');
        $update_url = route('admin.users.update', ['id'=>$user->id ?? ''], false);
    }
    $menu_items = [__('Users'), $page_title];
@endphp

@section('title', $page_title)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-7">
                <form class="card" action="{{$update_url}}" method="post" enctype="multipart/form-data" onsubmit="disableSubmitBtn()">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 edit-profile">
                                <div class="profile-title">
                                    <div class="media">
                                        <img class="img-70 rounded-circle" alt="" src="{{$user->getProfilePhoto()}}">
                                        <div class="media-body">
                                            <h5 class="mb-1">{{$user->getFullName()}}</h5>
                                            <p>{{$user->getUserRole()}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{__('First Name')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" placeholder="eg. Rafat" name="first_name" value="{{$user->first_name}}" required>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Last Name')}}</label>
                                    <input class="form-control" type="text" placeholder="eg. Hossain" name="last_name" value="{{$user->last_name}}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="mobile">{{__('Mobile Number')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" placeholder="Mobile Number" id="mobile" value="{{$user->mobile}}" disabled readonly>
                                    @if(!$user->is_mobile_verified)
                                        <small class="text-danger">{{__('Mobile number is not verified yet!')}}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Email address')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="email" placeholder="Email" value="{{$user->email}}" disabled readonly>
                                    @if(!$user->is_email_verified)
                                        <small class="text-danger">{{__('Email address is not verified yet!')}}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Address')}}</label>
                                    <input class="form-control" type="text" placeholder="Home Address" name="address" value="{{$user->address}}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{__('City')}}</label>
                                    <input class="form-control" type="text" placeholder="City" name="city" value="{{$user->city}}">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">{{__('Postal Code')}}</label>
                                    <input class="form-control" type="number" placeholder="ZIP Code" name="post_code" value="{{$user->post_code}}">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label class="form-label" for="id_country">{{__('Country')}}</label>
                                    <select class="form-control btn-square select2-custom" id="id_country" name="country">
                                        <option value="">--Select--</option>
                                        @foreach($country_list as $country)
                                            <option value="{{$country->name}}">{{$country->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="profilePhoto">{{__('Profile Photo')}}</label>
                                    <input class="form-control" id="profilePhoto" type="file" name="profile_photo" accept=".jpg,.jpeg,.png">
                                    <small class="text-dark">{{__('Allowed extensions: jpeg,png,jpg | Max size: 5MB')}}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button class="btn btn-primary" type="submit">{{__('Update Profile')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let mobile_number = document.querySelector("#mobile");
        $(document).ready(function () {
            $("#id_country").val('{{$user->country}}').trigger('change');

            window.intlTelInput(mobile_number, {
                utilsScript: util_script,
                initialCountry: "auto",
                separateDialCode: true,
                showSelectedDialCode: true,
                geoIpLookup: function (success, failure) {
                    $.get("https://ipinfo.io", function () {
                    }, "jsonp").always(function (resp) {
                        countryCode = (resp && resp.country) ? resp.country : "";
                        success(countryCode);
                    });
                },
            });
            let iti = window.intlTelInputGlobals.getInstance(mobile_number);
            iti.setNumber('+{{$user->mobile}}');
        });
    </script>
@endsection
