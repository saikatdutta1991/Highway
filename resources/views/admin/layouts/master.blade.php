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
        <style>
            /* width */
            ::-webkit-scrollbar {
            width: 5px;
            }
            /* Track */
            ::-webkit-scrollbar-track {
            background: #f1f1f1; 
            }
            /* Handle */
            ::-webkit-scrollbar-thumb {
            background: #00baed; 
            }
            /* Handle on hover */
            ::-webkit-scrollbar-thumb:hover {
            background: #555; 
            }
            #loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
            }
            #loader {
            display: block;
            position: relative;
            left: 50%;
            top: 50%;
            width: 150px;
            height: 150px;
            margin: -75px 0 0 -75px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #3498db;
            -webkit-animation: spin 2s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
            animation: spin 2s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
            z-index: 1001;
            }
            #loader:before {
            content: "";
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #e74c3c;
            -webkit-animation: spin 3s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
            animation: spin 3s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
            }
            #loader:after {
            content: "";
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #f9c922;
            -webkit-animation: spin 1.5s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
            animation: spin 1.5s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
            }
            @-webkit-keyframes spin {
            0%   { 
            -webkit-transform: rotate(0deg);  /* Chrome, Opera 15+, Safari 3.1+ */  /* IE 9 */
            transform: rotate(0deg);  /* Firefox 16+, IE 10+, Opera */
            }
            100% {
            -webkit-transform: rotate(360deg);  /* Chrome, Opera 15+, Safari 3.1+ */  /* IE 9 */
            transform: rotate(360deg);  /* Firefox 16+, IE 10+, Opera */
            }
            }
            @keyframes spin {
            0%   { 
            -webkit-transform: rotate(0deg);  /* Chrome, Opera 15+, Safari 3.1+ */  /* IE 9 */
            transform: rotate(0deg);  /* Firefox 16+, IE 10+, Opera */
            }
            100% {
            -webkit-transform: rotate(360deg);  /* Chrome, Opera 15+, Safari 3.1+ */  /* IE 9 */
            transform: rotate(360deg);  /* Firefox 16+, IE 10+, Opera */
            }
            }
            #loader-wrapper .loader-section {
            position: fixed;
            top: 0;
            width: 51%;
            height: 100%;
            background: #222222;
            z-index: 1000;
            -webkit-transform: translateX(0);  /* Chrome, Opera 15+, Safari 3.1+ */  /* IE 9 */
            transform: translateX(0);  /* Firefox 16+, IE 10+, Opera */
            }
            #loader-wrapper .loader-section.section-left {
            left: 0;
            }
            #loader-wrapper .loader-section.section-right {
            right: 0;
            }
            /* Loaded */
            .loaded #loader-wrapper .loader-section.section-left {
            -webkit-transform: translateX(-100%);  /* Chrome, Opera 15+, Safari 3.1+ */  /* IE 9 */
            transform: translateX(-100%);  /* Firefox 16+, IE 10+, Opera */
            -webkit-transition: all 0.7s 0.3s cubic-bezier(0.645, 0.045, 0.355, 1.000);  
            transition: all 0.7s 0.3s cubic-bezier(0.645, 0.045, 0.355, 1.000);
            }
            .loaded #loader-wrapper .loader-section.section-right {
            -webkit-transform: translateX(100%);  /* Chrome, Opera 15+, Safari 3.1+ */  /* IE 9 */
            transform: translateX(100%);  /* Firefox 16+, IE 10+, Opera */
            -webkit-transition: all 0.7s 0.3s cubic-bezier(0.645, 0.045, 0.355, 1.000);  
            transition: all 0.7s 0.3s cubic-bezier(0.645, 0.045, 0.355, 1.000);
            }
            .loaded #loader {
            opacity: 0;
            -webkit-transition: all 0.3s ease-out;  
            transition: all 0.3s ease-out;
            }
            .loaded #loader-wrapper {
            visibility: hidden;
            -webkit-transform: translateY(-100%);  /* Chrome, Opera 15+, Safari 3.1+ */  /* IE 9 */
            transform: translateY(-100%);  /* Firefox 16+, IE 10+, Opera */
            -webkit-transition: all 0.3s 1s ease-out;  
            transition: all 0.3s 1s ease-out;
            }
        </style>
        @yield('top-header')
    </head>
    <body class="theme-pink">
        <!-- Page Loader -->
        <div id="loader-wrapper">
            <div id="loader"></div>
            <div class="loader-section section-right"></div>
            <div class="loader-section section-left"></div>
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
                $("#loader").fadeOut()
                setTimeout(() => {
                    $(".section-left").animate({"left":"-1000px"}, "slow")
                    $(".section-right").animate({"right":"-1000px"}, "slow")
            
                    setTimeout(() => {
                        $("#loader-wrapper").remove()
                    }, 500);
            
                }, 100);
            }
            
            $(document).ready(()=>{
                hideLoader();
            })
        </script>
        @yield('bottom')
    </body>
</html>