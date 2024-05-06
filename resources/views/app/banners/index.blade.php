@extends('layouts.master')

@php
    $page_title = __('View Banners');
    $menu_items = [__('Banners'),$page_title];
@endphp

@section('title', $page_title)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Zero Configuration  Starts-->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-striped table-bordered" id="dataTable1">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{__('Image')}}</th>
                                    <th>{{__('Sorting Index')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($banner_list ?? [] as $banner)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <img class="" src="{{$banner->banner_image}}" style="height: 80px;" alt="">
                                        </td>
                                        <td>{{$banner->sorting_index}}</td>
                                        <td>
                                            <a href="{{route('admin.app.banners.toggle.status', ['id'=>$banner->id], false)}}">
                                                @if($banner->status)
                                                    <span class="badge badge-light-success">{{__('Active')}}</span>
                                                @else
                                                    <span class="badge badge-light-danger">{{__('Inactive')}}</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td>
                                            <ul class="action">
                                                @can('Can Edit App Banners')
                                                    <li class="edit"><a href="{{route('admin.app.banners.edit', ['id'=>$banner->id], false)}}"><i class="icon-pencil-alt"></i></a></li>
                                                @endcan
                                                @can('Can Delete App Banners')
                                                    <li class="delete"><a href="javascript:void(0);" onclick="deleteConfirmation('{{route('admin.app.banners.destroy', ['id'=>$banner->id], false)}}')"><i class="icon-trash"></i></a></li>
                                                @endcan
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $("#dataTable1").DataTable({
            paging: true,
            ordering: true
        });
    </script>
@endsection
