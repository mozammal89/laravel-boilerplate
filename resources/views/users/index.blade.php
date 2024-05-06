@extends('layouts.master')

@php
    $page_title = __('Users');
    $menu_items = [$page_title];
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
                                    <th>{{__('Role')}}</th>
                                    <th>{{__('Mobile')}}</th>
                                    <th>{{__('Email')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($user_list as $user)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <img class="img-fluid table-avtar" src="{{$user->profile_photo}}" alt="">{{$user->getFullName()}}
                                        </td>
                                        <td>
                                            <a class="f-w-600" href="javascript:void(0);" onclick="showRoleChangeModal('{{$user->getUserRoleID()}}', '{{$user->id}}')">
                                                {{$user->getUserRole()}}
                                            </a>
                                        </td>
                                        <td>
                                            @if($user->is_mobile_verified)
                                                <i class="fa fa-check-circle text-success"></i>
                                            @else
                                                <i class="fa fa-times-circle text-danger"></i>
                                            @endif
                                            {{$user->mobile}}
                                        </td>
                                        <td>
                                            @if($user->is_email_verified)
                                                <i class="fa fa-check-circle text-success"></i>
                                            @else
                                                <i class="fa fa-times-circle text-danger"></i>
                                            @endif
                                            {{$user->email ?? "Not available"}}
                                        </td>
                                        <td>
                                            <a href="{{route('admin.users.status.toggle', ['id'=>$user->id], false)}}">
                                                @if($user->is_active)
                                                    <span class="badge badge-light-success">{{__('Active')}}</span>
                                                @else
                                                    <span class="badge badge-light-danger">{{__('Inactive')}}</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td>
                                            <ul class="action">
                                                @can('Can Edit Users')
                                                    <li class="edit"><a href="{{route('admin.users.edit', ['id'=>$user->id], false)}}"><i class="icon-pencil-alt"></i></a></li>
                                                @endcan
                                                @can('Can Delete Users')
                                                    <li class="delete"><a href="javascript:void(0);" onclick="deleteConfirmation('{{route('admin.users.destroy', ['id'=>$user->id], false)}}')"><i class="icon-trash"></i></a></li>
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

    @can('Can Edit Users')
        <div class="modal fade" id="userRoleSelectionModal" tabindex="-1" role="dialog" aria-labelledby="userRoleSelectionModalTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="userRoleSelectionModalTitle">{{__('Change User Role')}}</h5>
                            <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label" for="id_user_role">{{__('User Role')}}</label>
                                <select class="form-control btn-square select2-custom" id="id_user_role" name="user_role" required>
                                    <option value="" selected>--Select--</option>
                                    @foreach($user_roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{__('Close')}}</button>
                            <button class="btn btn-primary" type="submit">{{__('Save changes')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@section('script')
    <script>
        $("#dataTable1").DataTable({
            paging: true,
            ordering: true
        });

        @can('Can Edit Users')
        function showRoleChangeModal(current_role, user_id) {
            let form_url = '{{route('admin.users.change.role', ['id'=>'###000###'], false)}}'.replace('###000###', user_id);
            $("#userRoleSelectionModal form").attr('action', form_url);
            $("#id_user_role").val(current_role).trigger('change');
            $("#userRoleSelectionModal").modal('show');
        }
        @endcan
    </script>
@endsection
