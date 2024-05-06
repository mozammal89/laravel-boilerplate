@extends('layouts.master')

@php
    $page_title = __('View Transactions');
    $menu_items = [__('Transactions'),$page_title];
@endphp

@section('title', $page_title)

@section('content')
    <div class="container-fluid">
        <div class="row widget-grid">
            <div class="col-xxl-3 col-sm-6 box-col-6">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div class="widget-round success">
                                        <div class="bg-round">
                                            <svg class="svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#profile-check"></use>
                                            </svg>
                                            <svg class="half-circle svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#halfcircle"></use>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4>{{$payment_success_count}}</h4><span class="f-light">{{__('Successful Transaction')}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6 box-col-6">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div class="widget-round secondary">
                                        <div class="bg-round">
                                            <svg class="svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#tag"></use>
                                            </svg>
                                            <svg class="half-circle svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#halfcircle"></use>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4>{{$payment_failed_count}}</h4><span class="f-light">{{__('Failed Transaction')}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6 box-col-6">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div class="widget-round warning">
                                        <div class="bg-round">
                                            <svg class="svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#orders"></use>
                                            </svg>
                                            <svg class="half-circle svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#halfcircle"></use>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4>{{$service_pending_count}}</h4><span class="f-light">{{__('Pending Services')}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6 box-col-6">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div class="widget-round primary">
                                        <div class="bg-round">
                                            <svg class="svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#new-order"></use>
                                            </svg>
                                            <svg class="half-circle svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#halfcircle"></use>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4>{{$service_delivery_count}}</h4><span class="f-light">{{__('Delivered Services')}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Zero Configuration  Starts-->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-bordered table-sm" id="dataTable1">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{__('Timestamp')}}</th>
                                    <th>{{__('Service/Merchant')}}</th>
                                    <th>{{__('Amount')}}</th>
                                    <th class="text-center">{{__('Service Status')}}</th>
                                    <th class="text-center">{{__('Payment Status')}}</th>
                                    <th class="text-center">{{__('Payment Verified')}}</th>
                                    <th>{{__('Transaction Details')}}</th>
                                    <th>{{__('Customer Details')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($transaction_list ?? [] as $transaction)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            {{$transaction->updated_at}}
                                        </td>
                                        <td class="text-center">
                                            @php
                                                if($transaction->is_merchant_payment){
                                                 $payee = $transaction->merchants;
                                                } else {
                                                 $payee = $transaction->billers;
                                                }
                                            @endphp
                                            @if($transaction->is_merchant_payment)
                                                <i data-feather="shopping-cart"></i>
                                                <br>
                                                <span class="text-danger">Merchant Payment</span>
                                                <br>
                                                <span class="f-w-600">{{$payee->merchant_title ?? 'N/A'}}</span>
                                                <br><br>
                                                <span class="f-w-600">A/C:{{$payee->merchant_number ?? 'N/A'}}</span>
                                            @else
                                                <i data-feather="rss"></i>
                                                <br>
                                                <span class="text-success">Biller Payment</span>
                                                <br>
                                                <span class="f-w-600">{{$payee->biller_name ?? 'N/A'}}</span>
                                                <br><br>
                                                <span class="f-w-600">A/C:{{$transaction->service_parameters['account_number'] ?? 'N/A'}}</span>
                                            @endif
                                        </td>
                                        <td class="f-w-600">{{number_format($transaction->amount, 2)}}</td>
                                        <td class="text-center">
                                            @if(!$transaction->is_merchant_payment)
                                                @if($transaction->payment_status == "Successful" && $transaction->service_status == "Pending")
                                                    <img class="mb-1" alt="alert" src="{{asset('assets/images/service_warning.gif')}}" style="height: 20px;"/>
                                                    <br>
                                                @endif
                                                @if($transaction->service_status == "Pending")
                                                    <span class="badge badge-light-warning">{{$transaction->service_status}}</span>
                                                @elseif($transaction->service_status == "Processing")
                                                    <span class="badge badge-light-info">{{$transaction->service_status}}</span>
                                                @elseif($transaction->service_status == "Delivered")
                                                    <img class="mb-1" alt="alert" src="{{asset('assets/images/success.gif')}}" style="height: 20px;"/>
                                                    <br>
                                                    <span class="badge badge-light-success">{{$transaction->service_status}}</span>
                                                @elseif($transaction->service_status == "Failed")
                                                    <span class="badge badge-light-danger">{{$transaction->service_status}}</span>
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($transaction->payment_status == "Pending")
                                                <span class="badge badge-light-warning">{{$transaction->payment_status}}</span>
                                            @elseif($transaction->payment_status == "Successful")
                                                <img class="mb-1" alt="alert" src="{{asset('assets/images/success.gif')}}" style="height: 20px;"/>
                                                <br>
                                                <span class="badge badge-light-success">{{$transaction->payment_status}}</span>
                                            @elseif($transaction->payment_status == "Failed")
                                                <img class="mb-1" alt="alert" src="{{asset('assets/images/failed.gif')}}" style="height: 20px;"/>
                                                <br>
                                                <span class="badge badge-light-danger">{{$transaction->payment_status}}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($transaction->is_transaction_verified)
                                                <img class="mb-1" alt="alert" src="{{asset('assets/images/success.gif')}}" style="height: 20px;"/>
                                                <br>
                                                <span class="badge badge-light-success">{{__('Verified')}}</span>
                                            @else
                                                <img class="mb-1" alt="alert" src="{{asset('assets/images/failed.gif')}}" style="height: 20px;"/>
                                                <br>
                                                <span class="badge badge-light-danger">{{__('Not Verified')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $trx_response = $transaction->transaction_verification_response;
                                                $trx_reference = $trx_response ? $trx_response['transaction']['txnReference'] : "-";
                                                $trx_response_text = $trx_response ? $trx_response['transaction']['responseText'] : "-";
                                            @endphp
                                            <b>Store Reference:</b> {{$transaction->request_id}}
                                            <br>
                                            <b>Payment Reference:</b> {{$transaction->payment_reference ?? '-'}}
                                            <br>
                                            <b>Transaction Reference:</b> {{$trx_reference}}
                                            <br>
                                            <b>Response Text:</b> {{$trx_response_text}}
                                            <br>
                                            <br>
                                            <b>Card Type:</b> {{$transaction->card_type ?? '-'}}
                                            <br>
                                            <b>Card Number:</b> {{$transaction->card_number_masked ?? '-'}}
                                        </td>
                                        <td>
                                            @php
                                                $trx_user = $transaction->users;
                                            @endphp
                                            <b>First Name:</b> {{$trx_user->first_name}}
                                            <br>
                                            <b>Last Name:</b> {{$trx_user->last_name ?? '-'}}
                                            <br>
                                            <b>Email:</b> {{$trx_user->email ?? '-'}}
                                            <br>
                                            <b>Mobile:</b> {{$trx_user->mobile ?? '-'}}
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
