@extends('layouts.master')

@php
    $page_title = __('Create Role');
    $menu_items = [__('Roles & Permissions'),$page_title];
@endphp

@section('title', $page_title)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6">
                <div class="card height-equal">
                    <div class="card-body custom-input">
                        <form class="row g-3" action="{{route('admin.roles.update', ['id'=>$role->id], false)}}" method="post" onsubmit="disableSubmitBtn()">
                            @csrf
                            <div class="col-12">
                                <label class="form-label" for="role-name">{{__('Role Name')}} <span class="text-danger">*</span></label>
                                <input class="form-control" id="role-name" name="role_name" type="text" placeholder="eg. Admin" value="{{$role->name}}" aria-label="Role Name" required="">
                            </div>
                            <div class="col-12">
                                <div class="form-check checkbox checkbox-dark mb-0">
                                    <input class="form-check-input" id="checkbox-primary-admin-login" type="checkbox" name="is_admin_login_allowed" value="1" @if($role->is_admin_login_allowed) checked @endif>
                                    <label class="form-check-label text-dark" for="checkbox-primary-admin-login">{{__('Is Admin Login Allowed')}}</label>
                                </div>
                                <div class="form-check checkbox checkbox-dark mb-0">
                                    <input class="form-check-input" id="checkbox-primary-active" type="checkbox" name="is_active" value="1" @if($role->is_active) checked @endif>
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
