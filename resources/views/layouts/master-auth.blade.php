<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @include('layouts.meta')

    @include('layouts.favicon')

    <title>@yield('title') - {{ Session::get('app_cfg_data', [])['app_name'] }}</title>

    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">

    @include('layouts.css')

    @yield('style')
</head>
<body>
@yield('content')

@include('layouts.script')

@include('layouts.theme')

@yield('script')
</body>
</html>
