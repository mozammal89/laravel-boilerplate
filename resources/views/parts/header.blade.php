<!-- Page Header Start-->
<div class="page-header">
    <div class="header-wrapper row m-0">
        <div class="header-logo-wrapper col-auto p-0">
            <div class="logo-wrapper">
                <a href="{{route('admin.home')}}">
                    <img class="img-fluid" src="{{ Session::get('app_cfg_data', [])['app_logo'] }}" alt="">
                </a>
            </div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
            </div>
        </div>
        <div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
            <div class="notification-slider">
                <div class="d-flex h-100">
                    <img src="{{asset('assets/images/giftools.gif')}}" alt="gif">
                    <h6 class="mb-0 f-w-400">
                        <span class="font-primary">Don't Miss Out! </span>
                        <span class="f-light">Our new update has been released.</span>
                    </h6>
                    <i class="icon-arrow-top-right f-light"></i>
                </div>
            </div>
        </div>
        <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
            <ul class="nav-menus">
                <li class="language-nav">
                    <div class="translate_wrapper">
                        <div class="current_lang">
                            <div class="lang">
                                @include('parts.locale-flag', ['available_locale'=>app()->getLocale()])
                                <span class="lang-txt">{{app()->getLocale()}} </span>
                            </div>
                        </div>
                        <div class="more_lang">
                            @foreach(config('app.available_locales') as $locale_name => $available_locale)
                                <div class="lang" data-value="en" onclick="setAppLanguage('{{$available_locale}}')">
                                    @include('parts.locale-item')
                                </div>
                            @endforeach
                        </div>
                    </div>
                </li>
                <li class="profile-nav onhover-dropdown pe-0 py-0">
                    <a href="{{route('admin.my.profile')}}" style="text-decoration: none;">
                        <div class="media profile-media">
                            <img class="b-r-10" src="{{auth()->user()->getProfilePhoto()}}" alt="">
                            <div class="media-body">
                                <span class="text-dark">{{auth()->user()->getFullName()}}</span>
                                <p class="mb-0">{{auth()->user()->getUserRole()}}</p>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <script class="result-template" type="text/x-handlebars-template">
            <div class="ProfileCard u-cf">
                <div class="ProfileCard-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0">
                        <path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path>
                        <polygon points="12 15 17 21 7 21 12 15"></polygon>
                    </svg>
                </div>
                <div class="ProfileCard-details">
                    <div class="ProfileCard-realName"></div>
                </div>
            </div>
        </script>
        <script class="empty-template"
                type="text/x-handlebars-template">
            <div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div></script>
    </div>
</div>
<!-- Page Header Ends-->
