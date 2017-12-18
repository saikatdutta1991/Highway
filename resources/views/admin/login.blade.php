<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Admin Panel | {{$website_title}}</title>
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
        body
        {
            background: url('{{url('admin_assets')}}/background.jpg');
            background-position: center;
            background-repeat: no-repeat;
          /*   background-size: cover; */
        }
    </style>

</head>

<body class="login-page">
    <div class="login-box">
        <div class="logo">
            <a href="javascript:void(0);">{{$website_title}}</b></a>
            <small>Admin Panel {{$website_title}}</small>
        </div>
        <div class="card">
            <div class="body">
                <form id="login-form" method="POST">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <div class="msg">Sign in to start your session</div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="email" placeholder="Email" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-8 p-t-5" style="visibility:hidden">
                            <input type="checkbox" name="rememberme" id="rememberme" class="filled-in chk-col-pink">
                            <label for="rememberme">Remember Me</label>
                        </div>
                        <div class="col-xs-4">
                            <button class="btn btn-block bg-pink waves-effect" type="submit">SIGN IN</button>
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
                            <span class="font-bold col-red font-15" id="login-msg"></span>
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


            $("#login-form").on('submit', function(event){

                showLoader();
                hideError();

                event.preventDefault();

                var data = $(this).serializeArray();


                $.post('{{url('admin/login')}}', data, function(response){

                    console.log(response)

                    hideLoader();


                    if(!response.success) {
                        showError('*'+response.text);
                    } else if(response.success){
                        showSuccesMessage('Logged in!! Wait while redirecting to dashboard');
                        setTimeout(function(){
                            window.location.reload();
                        }, 2000);
                        
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