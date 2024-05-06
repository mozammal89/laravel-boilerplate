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
                        <form class="row g-3" action="{{route('admin.app.banners.update', ['id'=>$banner_instance->id], false)}}" method="post" enctype="multipart/form-data" onsubmit="disableSubmitBtn()">
                            @csrf
                            <div class="col-12">
                                <img src="{{$banner_instance->banner_image}}" style="height: 100px; border: 2px solid #000000; padding: 2px;">
                            </div>
                            <div class="col-12">
                                <div class="mb-0">
                                    <label class="form-label" for="banner_image">{{__('Banner Image')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" id="banner_image" type="file" name="banner_image" accept=".png">
                                    <small class="text-dark">{{__('Allowed extensions: png | Max size: 500Kb')}}</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="group-name">{{__('Sorting Index')}} <span class="text-danger">*</span></label>
                                <input class="form-control" id="group-name" name="sorting_index" type="number" placeholder="eg. Dialog" value="{{$banner_instance->sorting_index}}" aria-label="Name" required="">
                            </div>
                            <div class="col-12">
                                <div class="form-check checkbox checkbox-dark mb-0">
                                    <input class="form-check-input" id="checkbox-primary-active" type="checkbox" name="is_active" value="1" {{$banner_instance->status ? 'checked' : ''}}>
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
