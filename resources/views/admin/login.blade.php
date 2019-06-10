<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <title>Admin Panel | {{$website_title}}</title>
        <meta name="theme-color" content="black">
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
        <!-- Custom Css -->
        <link href="{{url('admin_assets/admin_bsb')}}/css/style.css" rel="stylesheet">
        <style>
            .website_name { font-family: Baskerville; font-style: normal; font-variant: normal; font-weight: 700; line-height: 26.4px; }
            body {
            margin: 0;
            background: #000; 
            }
            .login-page {
                background:none;
            }
            video { 
            position: fixed;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -100;
            transform: translateX(-50%) translateY(-50%);
            background: url('{{url('admin_assets/Orchestra/Mp4/Orchestra.mp4')}}') no-repeat;
            background-size: cover;
            transition: 1s opacity;
            }
            .input-group {
                border: 1px solid #ea3c64;
                border-radius: 50px;
                overflow: hidden;
                padding-left: 5px;
                padding-right: 5px;
            }

            ::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
            color: #ea3c64;
            opacity: 1; /* Firefox */
            }

            :-ms-input-placeholder { /* Internet Explorer 10-11 */
            color: #ea3c64;
            }

            ::-ms-input-placeholder { /* Microsoft Edge */
            color: #ea3c64;
            }
        </style>
    </head>
    <body class="login-page">
        <video poster="{{url('admin_assets/Orchestra/Mp4/Orchestra.mp4')}}" id="bgvid" playsinline autoplay muted loop>
            <!-- WCAG general accessibility recommendation is that media such as background video play through only once. Loop turned on for the purposes of illustration; if removed, the end of the video will fade in the same way created by pressing the "Pause" button  -->
            <!-- <source src="{{url('admin_assets/Orchestra/WEBM/Orchestra.webm')}}" type="video/webm"> -->
            <source src="{{url('admin_assets/Orchestra/Mp4/Orchestra.mp4')}}" type="video/mp4">
        </video>
        <div class="login-box">
            
            <div class="card">
                <div class="body">
                    <div class="logo">
                        <a href="javascript:void(0);"><span class="website_name" style="color:#ea3c64">{{$website_name}}</span></b></a>
                        <small style="color:#ea3c64"><span class="website_name">{{$website_name}}</span> Master Control Panel. Sign in to start your session</small>
                    </div>
                    <form id="login-form" method="POST" autocomplete="off">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <div class="input-group">
                            <span class="input-group-addon">
                            <i class="material-icons">person</i>
                            </span>
                            <div class="">
                                <input type="text" class="form-control" name="email" placeholder="Email" required readonly>
                            </div>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                            </span>
                            <div class="">
                                <input type="password" class="form-control" name="password" placeholder="Password" required readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <button class="btn btn-block bg-pink waves-effect" style="border-radius: 50px;" type="submit">SIGN IN</button>
                            </div>
                        </div>
                        <div class="row m-t-0 m-b-0" style="display:none" id="preloader-container">
                            <div class="col-xs-12 align-center" style="margin-bottom:0;margin-top:0">
                                <div class="preloader pl-size-xs">
                                    <div class="spinner-layer pl-red">
                                        <div class="circle-clipper left">
                                            <div class="circle"></div>
                                        </div>
                                        <div class="circle-clipper right">
                                            <div class="circle"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row m-t-5 m-b-5 align-center" style="display:none" id="error-div">
                            <div class="col-xs-12" style="margin-bottom:0;margin-top:0">
                                <span class="font-bold col-red font-12" id="login-msg"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Jquery Core Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap Core Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap/js/bootstrap.js"></script>
        <!-- Waves Effect Plugin Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/plugins/node-waves/waves.js"></script>
        <!-- Validation Plugin Js -->
        <!-- <script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-validation/jquery.validate.js"></script> -->
        <!-- Custom Js -->
        <script src="{{url('admin_assets/admin_bsb')}}/js/admin.js"></script>
        <script type="text/javascript">
            var vid = document.getElementById("bgvid");
            
            function vidFade() {
            vid.classList.add("stopfade");
            }
            
            vid.addEventListener('ended', function()
            {
            // only functional if "loop" is removed 
            vid.pause();
            // to capture IE10
            vidFade();
            }); 
            
            
            
            function showLoader()
            {
                $("#preloader-container").fadeIn();
            }
            
            function hideLoader()
            {
                $("#preloader-container").fadeOut();
            }
            
            function hideError()
            {
                $("#error-div").fadeOut();
            }
            
            function showError(text)
            {
                $("#error-div").fadeIn();
                $("#login-msg").removeClass('col-green').addClass('col-red').text(text);
            }     
            
            
            function showSuccesMessage(message)
            {
                $("#error-div").fadeIn();
                $("#login-msg").removeClass('col-red').addClass('col-green').text(message);
            }
            
            
            
            $(document).ready(function(){


                setTimeout(function(){
                    $("input[name=email]").attr('readonly', false).focus();
                    $("input[name=password]").attr('readonly', false);
                },500);
            
            
                $("#login-form").on('submit', function(event){
            
                    showLoader();
                    hideError();
            
                    event.preventDefault();
            
                    var data = $(this).serializeArray();
            
            
                    $.post('{{route("admin-login")}}', data, function(response){
            
                        console.log(response)
            
                        hideLoader();
            
            
                        if(!response.success) {
                            showError('*'+response.text);
                        } else if(response.success){
                            showSuccesMessage('Logged in!! Wait while redirecting');
                            setTimeout(function(){
                                window.location.href=response.data.intended_url
                            }, 100);
                            
                        }
            
                       
                    }).fail(function(response) {
                        hideLoader();
                        showError('*Unknown server error. Contact to you server admin');
                    });
            
            
                });
            
            
               
            
            });
            
            
        </script>
    </body>
</html>