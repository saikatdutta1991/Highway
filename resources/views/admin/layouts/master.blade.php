<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
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
        <!-- Custom Css -->
        <link href="{{url('admin_assets/admin_bsb')}}/css/style.css" rel="stylesheet">
        <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
        <!-- <link href="{{url('admin_assets/admin_bsb')}}/css/themes/all-themes.css" rel="stylesheet" /> -->
        <link href="{{url('admin_assets/admin_bsb')}}/css/themes/theme-pink.min.css" rel="stylesheet" />
        @yield('top-header')
    </head>
    <body class="theme-pink">
        <!-- Page Loader -->
        <div class="page-loader-wrapper">
            <div class="loader">
                <div class="preloader">
                    <div class="spinner-layer pl-red">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
                <p>Please wait...</p>
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
        @yield('bottom')
    </body>
</html>