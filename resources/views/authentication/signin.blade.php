@extends('layouts.master-auth')
@section('title', __('Sign In'))

@section('content')
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-12 p-0">
                <div class="login-card login-dark">
                    <div>
                        <div>
                            <a class="logo" href="">
                                <img class="img-fluid for-light" src="{{ Session::get('app_cfg_data', [])['app_logo'] }}" style="height: 50px;" alt="login_page">
                                <img class="img-fluid for-dark" src="{{ Session::get('app_cfg_data', [])['app_logo'] }}" style="height: 50px;" alt="login_page">
                            </a>
                        </div>
                        <div class="login-main">
                            <form class="theme-form" action="" method="post">
                                @csrf

                                <h4>{{__('Sign in to account')}}</h4>
                                <p>{{__('Enter your email & password to login')}}</p>

                                @include('parts.message')

                                <div class="form-group">
                                    <label class="col-form-label" for="id_email">{{__('Email Address')}}</label>
                                    <input class="form-control" type="email" placeholder="test@example.com" name="email" id="id_email" value="{{old('email')}}" readonly required>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label" for="id_password">{{__('Password')}}</label>
                                    <div class="form-input position-relative">
                                        <input class="form-control" type="password" name="password" id="id_password" placeholder="*********" readonly required>
                                        <div class="show-hide text-primary" style="cursor: pointer;" onclick="togglePassword()" id="id_pass_eye"><i class="fa fa-eye"></i></div>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="checkbox p-0">
                                        <input id="checkbox1" type="checkbox" name="remember_me" value="1">
                                        <label class="text-muted" for="checkbox1">{{__('Remember me')}}</label>
                                    </div>
                                    <a class="link" href="">{{__('Forgot password?')}}</a>
                                    <div class="text-end mt-3">
                                        <button class="btn btn-primary btn-block w-100" type="submit">{{__('Sign in')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="text-center">
                            <div class="social mt-4">
                                <div class="btn-showcase">
                                    @foreach(config('app.available_locales') as $locale_name => $available_locale)
                                        <a class="btn btn-light" href="{{route('set.lang', ['locale'=>$available_locale], false)}}">
                                            <div class="lang">
                                                @include('parts.locale-item')
                                            </div>
                                        </a>
                                    @endforeach
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
        setTimeout(function () {
            $("#id_email").removeAttr('readonly');
            $("#id_password").removeAttr('readonly');
        }, 1000)

        const _password = $("#id_password");
        const _password_toggler = $("#id_pass_eye");
        let _pass_type = 'password';

        function togglePassword() {
            if (_pass_type === 'password') {
                _pass_type = 'text';
                _password_toggler.html('<i class="fa fa-eye-slash"></i>');
            } else {
                _pass_type = 'password';
                _password_toggler.html('<i class="fa fa-eye"></i>');
            }
            _password.attr('type', _pass_type)

        }
    </script>
@endsection
