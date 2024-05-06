@extends('layouts.master')

@php
    $page_title = __('Create Category');
    $menu_items = [__('App'),__('Category'),$page_title];
@endphp

@section('title', $page_title)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6">
                <div class="card height-equal">
                    <div class="card-body custom-input">
                        <form class="row g-3" action="{{route('admin.app.category.store', [], false)}}" method="post" enctype="multipart/form-data" onsubmit="disableSubmitBtn()">
                            @csrf
                            <div class="col-12">
                                <label class="form-label" for="group-name">{{__('Category Name')}} <span class="text-danger">*</span></label>
                                <input class="form-control" id="group-name" name="name" type="text" placeholder="eg. Dialog" value="{{old('name')}}" aria-label="Name" required="">
                            </div>
                            <div class="col-12">
                                <div class="mb-0">
                                    <label class="form-label" for="profilePhoto">{{__('Icon')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" id="profilePhoto" type="file" name="category_icon" accept=".png" required>
                                    <small class="text-dark">{{__('Allowed extensions: png | Max size: 200Kb')}}</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="group-name">{{__('Sorting Index')}} <span class="text-danger">*</span></label>
                                <input class="form-control" id="group-name" name="sorting_index" type="number" placeholder="eg. Dialog" value="{{old('sorting_index') ?? 1}}" aria-label="Name" required="">
                            </div>
                            <div class="col-12">
                                <div class="form-check checkbox checkbox-dark mb-0">
                                    <input class="form-check-input" id="checkbox-primary-active" type="checkbox" name="is_active" value="1" checked>
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
