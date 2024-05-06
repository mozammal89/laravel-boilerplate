@if ($message = Session::get('success'))
    <div class="alert alert-light-success" role="alert">
        <div class="txt-success">{{ $message }}</div>
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="alert alert-light-danger" role="alert">
        <div class="txt-danger">{{ $message }}</div>
    </div>
@endif

@if (count($errors) > 0)
    <div class="alert alert-light-danger" role="alert">
        <div class="txt-danger">
            <ul class="list-group">
                @foreach ($errors->all() as $error)
                    <li><i class="icofont icofont-arrow-right"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
