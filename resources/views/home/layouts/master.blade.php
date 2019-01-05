<!doctype html>
<html lang="en">
    <head>
        <title>@yield('title') | {{$website_title}}</title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="keywords" content="">
        <link rel="icon" href="{{$website_fav_icon_url}}" type="image/x-icon">
        <!-- Font -->
        <link rel="dns-prefetch" href="//fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500" rel="stylesheet">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{asset('web/home/')}}/css/bootstrap.min.css">
        <!-- Themify Icons -->
        <link rel="stylesheet" href="{{asset('web/home/')}}/css/themify-icons.css">
        <!-- Owl carousel -->
        <link rel="stylesheet" href="{{asset('web/home/')}}/css/owl.carousel.min.css">
        <!-- Main css -->
        <link href="{{asset('web/home/')}}/css/style.css" rel="stylesheet">
        <!-- Chrome, Firefox OS and Opera -->
        <meta name="theme-color" content="#8d5b92">
        <!-- Windows Phone -->
        <meta name="msapplication-navbutton-color" content="#8d5b92">
        <!-- iOS Safari -->
        <meta name="apple-mobile-web-app-status-bar-style" content="#8d5b92">
        @yield('top-header')
    </head>
    <body data-spy="scroll" data-target="#navbar" data-offset="30">
        <!-- Nav Menu -->
        @include('home.layouts.navbar')
        @yield('content')
        <!-- // end .section -->
        @include('home.layouts.footer')
        <!-- jQuery and Bootstrap -->
        <script src="{{asset('web/home/')}}/js/jquery-3.2.1.min.js"></script>
        <script src="{{asset('web/home/')}}/js/bootstrap.bundle.min.js"></script>
        <!-- Plugins JS -->
        <script src="{{asset('web/home/')}}/js/owl.carousel.min.js"></script>
        <!-- Custom JS -->
        <script src="{{asset('web/home/')}}/js/script.js"></script>
    </body>
</html>