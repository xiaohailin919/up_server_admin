    <!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Plugins css -->
    <link href="{{ asset('plugins/bootstrap-daterangepicker/daterangepicker.css?1') }}" rel="stylesheet">
    <link href="{{ asset('plugins/bootstrap-timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/bootstrap-datetimepicker/datetimepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/sweet-alert/sweetalert2.min.css') }}" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/icons.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css?1') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset('js/modernizr.min.js') }}"></script>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- jQuery  -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/waves.js') }}"></script>
    <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
    <script src="{{ asset('js/jquery.form.min.js') }}"></script>

    <!-- init dashboard -->
    <!--<script src="assets/pages/jquery.dashboard.init.js"></script>-->

    <script src="{{ asset('plugins/moment/moment.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-daterangepicker/daterangepicker.js?2') }}"></script>
    <script src="{{ asset('plugins/bootstrap-timepicker/bootstrap-timepicker.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-datetimepicker/datetimepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/sweet-alert/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('plugins/parsleyjs/parsley.min.js') }}"></script>

    <style>
        li.notification-list:hover .dropdown-item a {
            color: #000!important;
        }
        label.version_label {
            color:#ff3111
        }
    </style>
</head>
<body>
<header id="topnav">
    <!-- 手机端导航栏 -->
    <div class="topbar-main">
        <div class="container-fluid">
            <!-- Logo container-->
            <div class="logo">
                <!-- Text Logo -->
                <a href="{{ route('home') }}" class="logo">
                    <span class="logo-small"><i class="mdi mdi-radar"></i></span>
                    <span class="logo-large"><i class="mdi mdi-radar"></i> TopOn</span>
                </a>
                <!-- Image Logo -->
                <!-- <a href="index.html" class="logo">
                    <img src="images/logo_sm.png" alt="" height="26" class="logo-small">
                    <img src="images/logo.png" alt="" height="16" class="logo-large">
                </a>-->
            </div>
            <!-- End Logo container-->

            <!-- Right Side Of Navbar -->
            <div class="menu-extras topbar-custom">
                <ul class="list-unstyled topbar-right-menu float-right mb-0">
                    <li class="menu-item">
                        <!-- Mobile menu toggle-->
                        <a class="navbar-toggle nav-link">
                            <div class="lines">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </a>
                        <!-- End mobile menu toggle-->
                    </li>

                    @guest
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                    @else
                    <li class="dropdown notification-list">
                        <a href="#" class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="false">
                            <span class="ml-1 pro-user-name">
                                {{ Auth::user()->name }}
                                <i class="mdi mdi-chevron-down"></i>
                            </span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                            <div class="dropdown-item noti-title">
                                <h6 class="text-overflow m-0">Welcome !</h6>
                            </div>

                            <a href="{{ route('password.update') }}" class="dropdown-item notify-item">
                                <i class="fi-lock"></i> <span>Update Password</span>
                            </a>

                            <a href="{{ \Illuminate\Support\Facades\URL::to('email-signature') }}" class="dropdown-item notify-item">
                                <i class="mdi mdi-email-outline"></i> <span>Email Signature</span>
                            </a>

                            <a href="{{ route('logout') }}" class="dropdown-item notify-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <i class="fi-power"></i> <span>Logout</span>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </a>
                        </div>
                    </li>
                    @endguest
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="navbar-custom">
        <div class="container-fluid">
            <div id="navigation">
                <!-- Left Side Of Navbar -->
                <ul class="navigation-menu">
                    @guest
                    @else
                        <?php $navList = \App\Services\Nav::getList(); ?>
                        @foreach ($navList as $nav)
                            <li class="has-submenu">
                                <a href="#">
                                    {!! $nav['icon'] !!}
                                    {{ $nav['name'] }}
                                </a>
                                <ul class="submenu">
                                    @foreach ($nav['list'] as $navSon)
                                        @if (!empty($navSon['list']))
                                            <li class="has-submenu">
                                                <a href="#">{{ $navSon['name'] }}</a>
                                                <ul class="submenu">
                                                    @foreach ($navSon['list'] as $navSon2)
                                                        <li>
                                                            <a href="{{ URL::to($navSon2['route']) }}">{{ $navSon2['name'] }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ URL::to($navSon['route']) }}">{{ $navSon['name'] }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                        <li class="dropdown notification-list float-right" style="height: 60px; padding: 20px 0;">
                            <a href="#" class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="false">
                                <span class="ml-1 pro-user-name">
                                    <i class="mdi mdi-account-circle"></i>
                                    {{ Auth::user()->name }}
                                    {{--<i class="mdi mdi-chevron-down"></i>--}}
                                </span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                                <div class="dropdown-item noti-title">
                                    <h6 class="text-overflow m-0">Welcome !</h6>
                                </div>

                                <a href="{{ route('password.update') }}" class="dropdown-item notify-item">
                                    <i class="fi-lock"></i> <span>Update Password</span>
                                </a>

                                <a href="{{ \Illuminate\Support\Facades\URL::to('email-signature') }}" class="dropdown-item notify-item">
                                    <i class="mdi mdi-email-outline"></i> <span>Email Signature</span>
                                </a>

                                <a href="{{ route('logout') }}" class="dropdown-item notify-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    <i class="fi-power"></i> <span>Logout</span>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </a>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </div>
</header>
<div class="wrapper">
    <div class="container-fluid">
        @yield('content')
    </div>
</div>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                {{ date('Y') }} © TopOn. - toponad.com
            </div>
        </div>
    </div>
</footer>

<script>
jQuery(document).ready(function () {
    $('.input-daterange-datepicker').daterangepicker({
        format: 'mm/dd/yyyy',
        minDate: "01/01/2018",
        maxDate: moment(),
        ranges: {
            '@lang('Today')': [moment(), moment()],
            '@lang('Yesterday')': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '@lang('Last :x Days', ['x' => 7])': [moment().subtract(6, 'days'), moment()],
            '@lang('Last :x Days', ['x' => 14])': [moment().subtract(13, 'days'), moment()],
            '@lang('Last :x Days', ['x' => 30])': [moment().subtract(29, 'days'), moment()],
            '@lang('This Month')': [moment().startOf('month'), moment().endOf('month')],
            '@lang('Last Month')': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            customRangeLabel: 'Custom'
        },
        showCustomRangeLabel: true,
        showDropdowns: false,
        alwaysShowCalendars: true,
        autoUpdateInput: false,
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-success',
        cancelClass: 'btn-light'
    }, function(start, end, label) {
        $('.input-daterange-datepicker').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
    });
    $(".select2").select2();

    $('.input-datetimepicker').datetimepicker({
        format: 'yyyy/mm/dd',
        weekStart: 1,
        todayBtn:  0,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0
    });
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

<!-- App js -->
<script src="{{ asset('js/jquery.core.js') }}"></script>
<script src="{{ asset('js/jquery.app.js') }}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
@yield('extra_js')
</body>
</html>
