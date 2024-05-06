<!-- Page Sidebar Start-->
<div class="sidebar-wrapper" sidebar-layout="stroke-svg">
    <div>
        <div class="logo-wrapper">
            <a href="{{ route('admin.home') }}">
                <img class="img-fluid for-light" src="{{ Session::get('app_cfg_data', [])['app_logo'] }}" alt=""
                    style="height: 35px;">
                <img class="img-fluid for-dark" src="{{ Session::get('app_cfg_data', [])['app_logo'] }}" alt=""
                    style="height: 35px;">
            </a>
            <div class="back-btn"><i class="fa fa-angle-left"></i></div>
        </div>
        <div class="logo-icon-wrapper">
            <a href="{{ route('admin.home') }}">
                <img class="img-fluid" src="{{ Session::get('app_cfg_data', [])['app_icon'] }}" alt="">
            </a>
        </div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow">
                <i data-feather="arrow-left"></i>
            </div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn">
                        <div class="mobile-back text-end"><span>Back</span>
                            <i class="fa fa-angle-right ps-2" aria-hidden="true"></i>
                        </div>
                    </li>
                    <li class="pin-title sidebar-main-title"></li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.home', [], false) }}">
                            <i data-feather="home"></i>
                            <span>{{ __('Dashboard') }}</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                    </li>
                    @canany(['Can View Transactions'])
                        <li class="sidebar-main-title">
                            <div>
                                <h6>{{ __('Transactions') }}</h6>
                            </div>
                        </li>
                        @can('Can View Transactions')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title link-nav"
                                    href="{{ route('admin.transactions.index', [], false) }}">
                                    <i data-feather="pie-chart"></i>
                                    <span>{{ __('View Transactions') }}</span>
                                    <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                                </a>
                            </li>
                        @endcanany
                    @endcanany
                    @canany([
                        'Can View Roles',
                        'Can Create Roles',
                        'Can Edit Roles',
                        'Can Delete Roles',
                        'Can View
                        Permissions',
                        'Can Change Permissions',
                        'Can View Users',
                        'Can Create Users',
                        'Can Edit Users',
                        'Can
                        Delete Users',
                        ])
                        <li class="sidebar-main-title">
                            <div>
                                <h6>{{ __('Users & Perms') }}</h6>
                            </div>
                        </li>
                        @canany(['Can View Users', 'Can Edit Users', 'Can Delete Users'])
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title link-nav"
                                    href="{{ route('admin.users.index', [], false) }}">
                                    <i data-feather="users"></i>
                                    <span>{{ __('Users') }}</span>
                                    <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                                </a>
                            </li>
                        @endcanany
                        @canany([
                            'Can View Roles',
                            'Can Create Roles',
                            'Can Edit Roles',
                            'Can Delete Roles',
                            'Can View
                            Permissions',
                            'Can Change Permissions',
                            ])
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title" href="javascript:void(0);">
                                    <i data-feather="shield"></i>
                                    <span>{{ __('Roles & Permissions') }}</span>
                                    <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                                </a>
                                <ul class="sidebar-submenu" style="display: none;">
                                    @can('Can Create Roles')
                                        <li><a href="{{ route('admin.roles.create', [], false) }}">{{ __('Create Role') }}</a>
                                        </li>
                                    @endcan
                                    @canany(['Can View Roles', 'Can Create Roles', 'Can Edit Roles', 'Can Delete Roles'])
                                        <li><a href="{{ route('admin.roles.index', [], false) }}">{{ __('Roles') }}</a></li>
                                    @endcanany
                                    @canany(['Can View Permissions', 'Can Change Permissions'])
                                        <li><a
                                                href="{{ route('admin.permissions.index', [], false) }}">{{ __('Permissions') }}</a>
                                        </li>
                                    @endcanany
                                </ul>
                            </li>
                        @endcanany
                    @endcanany
                    @canany(['Can Change Settings', 'Can Change Payment Settings'])
                        <li class="sidebar-main-title">
                            <div>
                                <h6>{{ __('Settings') }}</h6>
                            </div>
                        </li>
                        @can('Can Change Settings')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title link-nav"
                                    href="{{ route('admin.settings.app', [], false) }}">
                                    <i data-feather="settings"></i>
                                    <span>{{ __('App Settings') }}</span>
                                    <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                                </a>
                            </li>
                        @endcan
                        @can('Can Change Payment Settings')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title link-nav"
                                    href="{{ route('admin.settings.payment', [], false) }}">
                                    <i data-feather="sliders"></i>
                                    <span>{{ __('Payment Settings') }}</span>
                                    <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                                </a>
                            </li>
                        @endcan
                        @can('Can Change SMS Settings')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title link-nav"
                                    href="{{ route('admin.settings.sms', [], false) }}">
                                    <i data-feather="message-square"></i>
                                    <span>{{ __('SMS Settings') }}</span>
                                    <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                                </a>
                            </li>
                        @endcan
                        @can('Can Change API Key')
                            <li class="sidebar-list">
                                <a class="sidebar-link sidebar-title link-nav"
                                    href="{{ route('admin.settings.api', [], false) }}">
                                    <i data-feather="lock"></i>
                                    <span>{{ __('API Key') }}</span>
                                    <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                                </a>
                            </li>
                        @endcan
                    @endcanany
                    @canany(['Can Change Policy'])
                        <li class="sidebar-main-title">
                            <div>
                                <h6>{{ __('Policy') }}</h6>
                            </div>
                        </li>
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title link-nav"
                                href="{{ route('admin.policy.edit', ['key' => 'terms-and-conditions'], false) }}">
                                <i data-feather="tag"></i>
                                <span>{{ __('Terms & Conditions') }}</span>
                                <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                            </a>
                        </li>
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title link-nav"
                                href="{{ route('admin.policy.edit', ['key' => 'privacy-policy'], false) }}">
                                <i data-feather="tag"></i>
                                <span>{{ __('Privacy Policy') }}</span>
                                <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                            </a>
                        </li>
                    @endcanany
                    <li class="sidebar-main-title">
                        <div>
                            <h6>{{ __('Miscellaneous') }}</h6>
                        </div>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav"
                            href="{{ route('admin.my.profile', [], false) }}">
                            <i data-feather="github"></i>
                            <span>{{ __('My Profile') }}</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('auth.signout', [], false) }}">
                            <i data-feather="log-out"></i>
                            <span>{{ __('Logout') }}</span>
                            <div class="according-menu"><i class="fa fa-angle-right"></i></div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>
<!-- Page Sidebar Ends-->
