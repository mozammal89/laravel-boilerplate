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

    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/custom-style.css')}}">
</head>
<body>
@include('parts.loader')
@include('layouts.scroll-to-top')
<!-- page-wrapper Start-->
<div class="page-wrapper compact-wrapper" id="pageWrapper">
    @include('parts.header')
    <!-- Page Body Start-->
    <div class="page-body-wrapper horizontal-menu">
        @include('parts.menu')
        <div class="page-body">
            @include('parts.breadcrumbs', ['title'=>$page_title ?? null, 'menu_items'=>$menu_items ?? null])
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        @include('parts.message')
                    </div>
                </div>
            </div>
            @yield('content')
        </div>
    </div>
</div>

@include('layouts.script')

<script>
    const util_script = '{{asset('assets/intl-tel/utils.js')}}';

    let axios_options = {
        headers: {
            "Content-Type": "application/json",
            "X-CSRFToken": '@csrf'
        }
    };

    $(document).ready(function () {
        $('.select2-custom').each(function (index, element) {
            $(this).select2();
        });
    })

    function setAppLanguage(locale) {
        window.location.href = '{{route('set.lang', ['locale'=>'replace_with_locale'], false)}}'.replaceAll('replace_with_locale', locale);
    }

    function disableSubmitBtn() {
        $('button[type=submit]').attr('disabled', 'disabled');
    }

    function deleteConfirmation(delete_url, title = '{{__('Are you sure?')}}', text = '{{__('You will not be able to revert this!')}}') {
        Swal.fire({
            title: title,
            text: text,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FC4438",
            cancelButtonColor: "#54BA4A",
            confirmButtonText: '{{__('Yes, delete it!')}}',
            cancelButtonText: '{{__('Cancel')}}'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = delete_url;
            }
        });
    }

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    function showToastNotification(message) {
        toastr["success"](message);
    }
</script>

@include('layouts.theme')

@yield('script')
</body>
</html>
