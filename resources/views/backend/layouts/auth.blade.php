<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/img/favicon.png')}}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- vendor css -->
    <link href="{{asset('assets/lib/@fortawesome/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/lib/ionicons/css/ionicons.min.css')}}" rel="stylesheet">

    <!-- DashForge CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/dashforge.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/dashforge.auth.css')}}">
</head>
<body>

<header class="navbar navbar-header navbar-header-fixed">
    <div class="navbar-brand">
        <a href="{{url('')}}" class="df-logo">dash<span>forge</span></a>
    </div><!-- navbar-brand -->
</header><!-- navbar -->

<div class="content content-fixed content-auth">
    @yield('content')
</div><!-- content -->

<footer class="footer">
    <div>
        <span>&copy; {{now()->format('Y')}} DashForge v1.0.0. </span>
        <span>Created by <a href="{{url('')}}">ThemePixels</a></span>
    </div>
    <div>
        <nav class="nav">
            <a href="https://themeforest.net/licenses/standard" class="nav-link">Licenses</a>
            <a href="http://themepixels.me/dashforge/change-log.html" class="nav-link">Change Log</a>
            <a href="https://discordapp.com/invite/RYqkVuw" class="nav-link">Get Help</a>
        </nav>
    </div>
</footer>

<script src="{{asset('assets/lib/jquery/jquery.min.js')}}"></script>
<script src="{{asset('assets/lib/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/lib/feather-icons/feather.min.js')}}"></script>
<script src="{{asset('assets/lib/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>

<script src="{{asset('assets/js/dashforge.js')}}"></script>

<!-- append theme customizer -->
<script src="{{asset('assets/lib/js-cookie/js.cookie.js')}}"></script>
<script src="{{asset('assets/js/dashforge.settings.js')}}"></script>

</body>
</html>
