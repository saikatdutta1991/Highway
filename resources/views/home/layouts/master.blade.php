<!doctype html>
<html lang="en">
    <head>
        <title>@yield('title') | {{$website_title}}</title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="{{$seo_description}}">
        <meta name="keywords" content="{{$seo_keywords}}">
        <link rel="icon" href="{{$website_fav_icon_url}}" type="image/x-icon">
        <style>
            #website_name_navbar { font-family: Baskerville; font-size: 29px; font-style: normal; font-variant: normal; font-weight: 700; line-height: 26.4px; }
        </style>
        <style>
            circle, .half, #bird .crest, #bird .face, .quarter, #bird .cheek, #bird .upperLip, #bird .lowerLip, #bird .eye {
            border-radius: 50%;
            background-repeat: no-repeat;
            overflow: hidden;
            }
            .half, #bird .crest, #bird .face {
            background-size: 50% 100%;
            }
            .quarter, #bird .cheek, #bird .upperLip, #bird .lowerLip {
            background-size: 50% 50%;
            }
            #bird {
            width: 20em;
            height: 20em;
            overflow: hidden;
            position: relative;
            transition: opacity 1s;
            }
            #bird > div {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
            }
            #bird .crest {
            width: 100%;
            height: 100%;
            background-image: linear-gradient(to right, #A12A15, #A12A15);
            animation: crest 2000ms infinite linear;
            }
            @keyframes crest {
            0% {
            transform: rotate(0deg);
            }
            25%, 50% {
            transform: rotate(180deg);
            }
            75%, 100% {
            transform: rotate(360deg);
            }
            }
            #bird .face {
            width: 65%;
            height: 65%;
            background-image: linear-gradient(to right, #FFF2FF, #FFF2FF);
            animation: face 2000ms infinite linear;
            }
            @keyframes face {
            0% {
            transform: rotate(0deg);
            }
            25%, 50% {
            transform: rotate(-180deg);
            }
            75%, 100% {
            transform: rotate(-360deg);
            }
            }
            #bird .cheek {
            width: 65%;
            height: 65%;
            background-image: linear-gradient(to right, #E7E7E7, #E7E7E7);
            animation: cheek 2000ms infinite linear;
            }
            @keyframes cheek {
            0% {
            transform: rotate(-90deg);
            }
            25%, 50% {
            transform: rotate(-180deg);
            }
            75%, 100% {
            transform: rotate(-450deg);
            }
            }
            #bird .upperLip {
            width: 65%;
            height: 65%;
            background-image: linear-gradient(to right, #F7CE42, #F7CE42);
            animation: upperLip 2000ms infinite linear;
            }
            @keyframes upperLip {
            0% {
            transform: rotate(90deg);
            }
            25%, 50% {
            transform: rotate(0deg);
            }
            75%, 100% {
            transform: rotate(90deg);
            }
            }
            #bird .lowerLip {
            width: 35%;
            height: 35%;
            background-image: linear-gradient(to right, #F7A500, #F7A500);
            animation: lowerLip 2000ms infinite linear;
            }
            @keyframes lowerLip {
            0% {
            transform: rotate(180deg);
            }
            25%, 50% {
            transform: rotate(270deg);
            }
            75%, 100% {
            transform: rotate(180deg);
            }
            }
            #bird .eye {
            width: 15%;
            height: 15%;
            background-color: #18233E;
            transform: translate(-60%, -60%);
            animation: eye 2000ms infinite linear;
            }
            @keyframes eye {
            0% {
            transform: translate(-60%, -60%);
            }
            25%, 50% {
            transform: translate(60%, -60%);
            }
            75%, 100% {
            transform: translate(-60%, -60%);
            }
            }
            .bird-wrapper {
            background-image: linear-gradient( 135deg, rgb(60, 8, 118) 0%, rgb(250, 0, 118) 100%);
            width: 100vw;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            z-index: 9999;    align-items: center;
            justify-content: center;
            flex-direction: column;
            }
        </style>

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
        <div class="bird-wrapper">
            <div id="bird">
                <div class="lowerLip"></div>
                <div class="crest"></div>
                <div class="face"></div>
                <div class="cheek"></div>
                <div class="eye"></div>
                <div class="upperLip"></div>
            </div>
        </div>
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
        <script>
            $(document).ready(()=>{
                setTimeout(() => {
                    $('.bird-wrapper').fadeOut();
                }, 500);
            })
        </script>
        @yield('bottom-script')
    </body>
</html>