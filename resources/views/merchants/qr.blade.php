<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print QR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="text-center p-4" style="border: 3px solid #000000">
            <img src="{{$merchant_instance->merchant_logo}}" alt="app_logo" style="height: 70px;"/>
            <h1 class="m-3" style="font-size: 30px; font-weight: 900;">{{$merchant_instance->merchant_title}}</h1>
            <div class="my-4" style="padding-left: 25%; padding-right: 25%;">
                <img class="p-2" src="{{$merchant_instance->merchant_qr}}" alt="qr" style="width: 100%; border: 5px solid #000000;"/>
            </div>
            <h1 class="m-3 mb-0" style="font-weight: 800;">Scan to Pay</h1>
            <p style="font-size: 25px;">Pay using {{ Session::get('app_cfg_data', [])['app_name'] }} Super App</p>
            <img src="{{asset('assets/images/payment-footer.png')}}" alt="footer" style="width: 100%; padding-left: 15%; padding-right: 15%;"/>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
