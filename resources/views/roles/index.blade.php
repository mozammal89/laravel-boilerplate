@extends('layouts.master')

@php
    $page_title = __('Roles');
    $menu_items = [__('Roles & Permissions'),$page_title];
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
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Admin Login')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Users')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($roles_list as $role)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$role->name}}</td>
                                        <td>
                                            @if($role->is_admin_login_allowed)
                                                <span class="badge badge-light-success">{{__('Allowed')}}</span>
                                            @else
                                                <span class="badge badge-light-danger">{{__('Blocked')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($role->is_active)
                                                <span class="badge badge-light-success">{{__('Active')}}</span>
                                            @else
                                                <span class="badge badge-light-danger">{{__('Inactive')}}</span>
                                            @endif
                                        </td>
                                        <td>{{$role->users_count}}</td>
                                        <td>
                                            <ul class="action">
                                                @can('Can Edit Roles')
                                                    <li class="edit"><a href="{{route('admin.roles.edit', ['id'=>$role->id], false)}}"><i class="icon-pencil-alt"></i></a></li>
                                                @endcan
                                                @can('Can Delete Roles')
                                                    <li class="delete"><a href="javascript:void(0);" onclick="deleteRole('{{$role->users_count}}', '{{route('admin.roles.destroy', ['id'=>$role->id], false)}}')"><i class="icon-trash"></i></a></li>
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

        @can('Can Delete Roles')
        function deleteRole(user_count, delete_url) {
            if (user_count > 0) {
                Swal.fire({
                    title: '{{__('Delete Restricted')}}',
                    text: '{{__('The number of users under the role has to be 0 (Zero) to be able delete the role.')}}',
                    icon: "warning",
                    confirmButtonText: '{{__('Okay')}}'
                });
            } else {
                deleteConfirmation(delete_url);
            }
        }
        @endcan
    </script>
@endsection
