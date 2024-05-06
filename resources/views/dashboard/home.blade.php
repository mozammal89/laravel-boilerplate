@extends('layouts.master')

@php
    $page_title = __('Home');
    $menu_items = [__('Dashboard')];
@endphp

@section('title', $page_title)

@section('content')
    <div class="container-fluid">
        <div class="row widget-grid">
            <div class="col-xxl-4 col-sm-6 box-col-6">
                <div class="card profile-box">
                    <div class="card-body">
                        <div class="media media-wrapper justify-content-between">
                            <div class="media-body">
                                <div class="greeting-user">
                                    <h4 class="f-w-600">Welcome to {{ Session::get('app_cfg_data', [])['app_name'] }}</h4>
                                    <p>Here whats happing in your account today</p>
                                    <div class="whatsnew-btn"><a class="btn btn-outline-white">Whats New !</a></div>
                                </div>
                            </div>
                            <div>
                                <div class="clockbox">
                                    <svg id="clock" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600">
                                        <g id="face">
                                            <circle class="circle" cx="300" cy="300" r="253.9"></circle>
                                            <path class="hour-marks" d="M300.5 94V61M506 300.5h32M300.5 506v33M94 300.5H60M411.3 107.8l7.9-13.8M493 190.2l13-7.4M492.1 411.4l16.5 9.5M411 492.3l8.9 15.3M189 492.3l-9.2 15.9M107.7 411L93 419.5M107.5 189.3l-17.1-9.9M188.1 108.2l-9-15.6"></path>
                                            <circle class="mid-circle" cx="300" cy="300" r="16.2"></circle>
                                        </g>
                                        <g id="hour" style="transform: rotate(137.717deg);">
                                            <path class="hour-hand" d="M300.5 298V142"></path>
                                            <circle class="sizing-box" cx="300" cy="300" r="253.9"></circle>
                                        </g>
                                        <g id="minute" style="transform: rotate(214.2deg);">
                                            <path class="minute-hand" d="M300.5 298V67"></path>
                                            <circle class="sizing-box" cx="300" cy="300" r="253.9"></circle>
                                        </g>
                                        <g id="second" style="transform: rotate(252deg);">
                                            <path class="second-hand" d="M300.5 350V55"></path>
                                            <circle class="sizing-box" cx="300" cy="300" r="253.9"></circle>
                                        </g>
                                    </svg>
                                </div>
                                <div class="badge f-10 p-0" id="txt"></div>
                            </div>
                        </div>
                        <div class="cartoon">
                            <img class="img-fluid" src="{{asset('assets/images/dashboard/cartoon.svg')}}" alt="vector women with leptop">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-auto col-xl-3 col-sm-6 box-col-6">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div class="widget-round secondary">
                                        <div class="bg-round">
                                            <svg class="svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#stroke-user"></use>
                                            </svg>
                                            <svg class="half-circle svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#halfcircle"></use>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4>{{$user_count}}</h4><span class="f-light">{{__('Users')}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="card widget-1">
                                <div class="card-body">
                                    <div class="widget-content">
                                        <div class="widget-round primary">
                                            <div class="bg-round">
                                                <svg class="svg-fill">
                                                    <use href="{{asset('assets/svg/icon-sprite.svg')}}#stroke-widget"></use>
                                                </svg>
                                                <svg class="half-circle svg-fill">
                                                    <use href="{{asset('assets/svg/icon-sprite.svg')}}#halfcircle"></use>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h4>{{$biller_groups}}</h4><span class="f-light">{{__('Biller Groups')}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-auto col-xl-3 col-sm-6 box-col-6">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div class="widget-round warning">
                                        <div class="bg-round">
                                            <svg class="svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#rate"></use>
                                            </svg>
                                            <svg class="half-circle svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#halfcircle"></use>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4>{{$biller_count}}</h4><span class="f-light">{{__('Biller')}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="card widget-1">
                                <div class="card-body">
                                    <div class="widget-content">
                                        <div class="widget-round success">
                                            <div class="bg-round">
                                                <svg class="svg-fill">
                                                    <use href="{{asset('assets/svg/icon-sprite.svg')}}#fill-ecommerce"></use>
                                                </svg>
                                                <svg class="half-circle svg-fill">
                                                    <use href="{{asset('assets/svg/icon-sprite.svg')}}#halfcircle"></use>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h4>{{$merchant_count}}</h4><span class="f-light">{{__('Merchants')}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-auto col-xl-3 col-sm-6 box-col-6">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div class="widget-round secondary">
                                        <div class="bg-round">
                                            <svg class="svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#stroke-button"></use>
                                            </svg>
                                            <svg class="half-circle svg-fill">
                                                <use href="{{asset('assets/svg/icon-sprite.svg')}}#halfcircle"></use>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4>{{$transaction_count}}</h4><span class="f-light">{{__('Transactions')}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="card widget-1">
                                <div class="card-body">
                                    <div class="widget-content">
                                        <div class="widget-round primary">
                                            <div class="bg-round">
                                                <svg class="svg-fill">
                                                    <use href="{{asset('assets/svg/icon-sprite.svg')}}#stroke-social"></use>
                                                </svg>
                                                <svg class="half-circle svg-fill">
                                                    <use href="{{asset('assets/svg/icon-sprite.svg')}}#halfcircle"></use>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h4>0</h4><span class="f-light">{{__('App Downloads')}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        startTime();

        // time
        function startTime() {
            var today = new Date();
            var h = today.getHours();
            var m = today.getMinutes();
            // var s = today.getSeconds();
            var ampm = h >= 12 ? "PM" : "AM";
            h = h % 12;
            h = h ? h : 12;
            m = checkTime(m);
            // s = checkTime(s);
            document.getElementById("txt").innerHTML = h + ":" + m + " " + ampm;
            var t = setTimeout(startTime, 500);
        }

        function checkTime(i) {
            if (i < 10) {
                i = "0" + i;
            } // add zero in front of numbers < 10
            return i;
        }
    </script>
@endsection
