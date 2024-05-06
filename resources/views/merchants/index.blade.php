@extends('layouts.master')

@php
    $page_title = __('View Merchants');
    $menu_items = [__('Merchants'),$page_title];
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
                                    <th>{{__('QR')}}</th>
                                    <th>{{__('Title')}}</th>
                                    <th>{{__('Number')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($merchant_list ?? [] as $merchant)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <a class="f-w-600" href="javascript:void(0);" onclick="printQR('{{route('admin.merchants.qr.print', ['id'=>$merchant->id], false)}}')">
                                                Print QR
                                            </a>
                                            |
                                            <a class="f-w-600 text-success" href="{{route('admin.merchants.qr.download', ['id'=>$merchant->id], false)}}" target="_blank">
                                                Download QR
                                            </a>
                                        </td>
                                        <td>
                                            <img class="img-fluid table-avtar" src="{{$merchant->merchant_logo}}" alt="">{{$merchant->merchant_title}}
                                        </td>
                                        <td>{{$merchant->merchant_number}}</td>
                                        <td>
                                            <a href="{{route('admin.merchants.toggle.status', ['id'=>$merchant->id], false)}}">
                                                @if($merchant->status)
                                                    <span class="badge badge-light-success">{{__('Active')}}</span>
                                                @else
                                                    <span class="badge badge-light-danger">{{__('Inactive')}}</span>
                                                @endif
                                            </a>
                                        </td>
                                        <td>
                                            <ul class="action">
                                                @can('Can Edit Merchants')
                                                    <li class="edit"><a href="{{route('admin.merchants.edit', ['id'=>$merchant->id], false)}}"><i class="icon-pencil-alt"></i></a></li>
                                                @endcan
                                                @can('Can Delete Merchants')
                                                    <li class="delete"><a href="javascript:void(0);" onclick="deleteConfirmation('{{route('admin.merchants.destroy', ['id'=>$merchant->id], false)}}')"><i class="icon-trash"></i></a></li>
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

        function printQR(qr_url) {
            window.open(qr_url, "_blank", "width=500,height=700");
        }
    </script>
@endsection
