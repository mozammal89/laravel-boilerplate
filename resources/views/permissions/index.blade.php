@extends('layouts.master')

@php
    $page_title = __('Manage Permissions');
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
                                    <th>{{__('Permissions')}}</th>
                                    @foreach($role_list as $role)
                                        <th class="text-center">{{$role->name}}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($permission_list as $permission)
                                    <tr>
                                        <td>{{$permission->name}}</td>
                                        @foreach($role_list as $role)
                                            <td class="text-center">
                                                <div class="form-check checkbox checkbox-primary mb-0">
                                                    <input class="form-check-input" id="checkbox-primary-{{$permission->id}}{{$role->id}}" type="checkbox" @if($role->hasPermissionTo($permission->name)) checked="" @endif @can('Can Change Permissions') onchange="togglePermission('{{$permission->id}}','{{$role->id}}')" @endcan>
                                                    <label class="form-check-label" for="checkbox-primary-{{$permission->id}}{{$role->id}}"></label>
                                                </div>
                                            </td>
                                        @endforeach
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
            ordering: false
        });

        @can('Can Change Permissions')
        function togglePermission(permission_id, role_id) {
            let payload = {
                'permission_id': permission_id,
                'role_id': role_id
            };
            axios.post('{{route('admin.permissions.toggle', [], false)}}', payload, axios_options).then((response) => {
                response = response.data;
                if(response.code === 200){
                    showToastNotification(response.message);
                }
            })
        }
        @endcan
    </script>
@endsection
