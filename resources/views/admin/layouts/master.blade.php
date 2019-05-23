<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="theme-color" content="#e91e63">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <title>@yield('title') | {{$website_title}}</title>
        <!-- Favicon-->
        <link rel="icon" href="{{$website_fav_icon_url}}" type="image/x-icon">

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


        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
        <!-- Bootstrap Core Css -->
        <link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
        <!-- Waves Effect Css -->
        <link href="{{url('admin_assets/admin_bsb')}}/plugins/node-waves/waves.css" rel="stylesheet" />
        <!-- Animation Css -->
        <link href="{{url('admin_assets/admin_bsb')}}/plugins/animate-css/animate.css" rel="stylesheet" />
        <!-- Morris Chart Css-->
        <link href="{{url('admin_assets/admin_bsb')}}/plugins/morrisjs/morris.css" rel="stylesheet" />
        <!-- Sweetalert Css -->
        <link href="{{url('admin_assets/admin_bsb')}}/plugins/sweetalert/sweetalert.css" rel="stylesheet" />
        <!-- Bootstrap Select Css -->
        <link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
        <!-- Custom Css -->
        <link href="{{url('admin_assets/admin_bsb')}}/css/style.css" rel="stylesheet">
        <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
        <!-- <link href="{{url('admin_assets/admin_bsb')}}/css/themes/all-themes.css" rel="stylesheet" /> -->
        <link href="{{url('admin_assets/admin_bsb')}}/css/themes/theme-pink.min.css" rel="stylesheet" />
        @yield('top-header')
    </head>
    <body class="theme-pink">
        <!-- Page Loader -->
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
        <!-- #END# Page Loader -->
        <!-- Overlay For Sidebars -->
        <div class="overlay"></div>
        <!-- #END# Overlay For Sidebars -->
        <!-- Search Bar -->
        <div class="search-bar">
            <div class="search-icon">
                <i class="material-icons">search</i>
            </div>
            <input type="text" placeholder="START TYPING...">
            <div class="close-search">
                <i class="material-icons">close</i>
            </div>
        </div>
        <!-- #END# Search Bar -->
        @include('admin.layouts.top_navbar')
        @include('admin.layouts.left_right_sidebar')
        <section class="content">
            @yield('content')
        </section>
        <!-- Jquery Core Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap Core Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap/js/bootstrap.js"></script>
        <!-- Select Plugin Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/js/bootstrap-select.js"></script>
        <!-- Slimscroll Plugin Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
        <!-- Waves Effect Plugin Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/node-waves/waves.js"></script>
        <!-- Jquery CountTo Plugin Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-countto/jquery.countTo.js"></script>
        <!-- Morris Plugin Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/raphael/raphael.min.js"></script>
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/morrisjs/morris.js"></script>
        <!-- ChartJs -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/chartjs/Chart.bundle.js"></script>
        <!-- Jquery Validation Plugin Css -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-validation/jquery.validate.js"></script>
        <!-- Sparkline Chart Plugin Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-sparkline/jquery.sparkline.js"></script>
        <!-- Custom Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/js/admin.js"></script>
        <script src="{{url('admin_assets/admin_bsb')}}/js/pages/index.js"></script>
        <!-- Demo Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/js/demo.js"></script>
        <script src="{{url('admin_assets/admin_bsb')}}/js/pages/ui/tooltips-popovers.js"></script>
        <!-- Bootstrap Notify Plugin Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-notify/bootstrap-notify.js"></script>
        <script src="{{url('admin_assets/admin_bsb')}}/js/pages/ui/notifications.js"></script>
        <!-- SweetAlert Plugin Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/sweetalert/sweetalert.min.js"></script>
        <!-- showNotification('bg-black', 'testing', 'top', 'right', 'animated flipInX', 'animated flipOutX'); -->
        <script>
            function hideLoader()
            {
                setTimeout(() => {
                    $('.bird-wrapper').fadeOut();
                }, 500);
            }
            
            $(document).ready(()=>{
                hideLoader();
            })
        </script>
        @yield('bottom')
    </body>
</html>