@extends('layouts.master')

@php
    $page_title = __('Category');
    $menu_items = [__('App'),$page_title];
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
                                    <th>{{__('Icon')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Sorting')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($app_categories as $category)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <img src="{{$category->icon}}" alt="{{$category->name}}" style="height: 40px"/>
                                        </td>
                                        <td>{{$category->name}}</td>
                                        <td>{{$category->sorting_index}}</td>
                                        <td>
                                            <a href="{{route('admin.app.category.toggle.status', ['id'=>$category->id], false)}}">
                                                @if($category->status)
                                                    <span class="badge badge-light-success">{{__('Active')}}</span>
                                                @else
                                                    <span class="badge badge-light-danger">{{__('Inactive')}}</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td>
                                            <ul class="action">
                                                @can('Can Edit App Category')
                                                    <li class="edit"><a href="{{route('admin.app.category.edit', ['id'=>$category->id], false)}}"><i class="icon-pencil-alt"></i></a></li>
                                                @endcan
                                                @can('Can Delete App Category')
                                                    <li class="delete"><a href="javascript:void(0);" onclick="deleteConfirmation('{{route('admin.app.category.destroy', ['id'=>$category->id], false)}}')"><i class="icon-trash"></i></a></li>
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
