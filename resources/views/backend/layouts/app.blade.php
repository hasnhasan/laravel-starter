<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/img/favicon.png')}}">

    <!-- vendor css -->
    <link href="{{asset('assets/lib/@fortawesome/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/lib/ionicons/css/ionicons.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/lib/jqvmap/jqvmap.min.css')}}" rel="stylesheet">

    <!-- DashForge CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/dashforge.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/dashforge.dashboard.css')}}">
</head>
<body>

<aside class="aside aside-fixed">
    <div class="aside-header">
        <a href="index.html" class="aside-logo">dash<span>forge</span></a>
        <a href="" class="aside-menu-link">
            <i data-feather="menu"></i>
            <i data-feather="x"></i>
        </a>
    </div>
    <div class="aside-body">
        @include('backend.partials.user-menu')
        @include('backend.partials.menu')
    </div>
</aside>

<div class="content ht-100v pd-0">
    @include('backend.partials.top-bar')

    <div class="content-body">
        <div class="container pd-x-0">
            @yield('content')
        </div><!-- container -->
    </div>
</div>

<script src="{{asset('assets/lib/jquery/jquery.min.js')}}"></script>
<script src="{{asset('assets/lib/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/lib/feather-icons/feather.min.js')}}"></script>
<script src="{{asset('assets/lib/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>

<script src="{{asset('assets/js/dashforge.js')}}"></script>
<script src="{{asset('assets/js/dashforge.aside.js')}}"></script>

<!-- append theme customizer -->
<script src="{{asset('assets/lib/js-cookie/js.cookie.js')}}"></script>
<script src="{{asset('assets/js/dashforge.settings.js')}}"></script>
</body>
</html>
