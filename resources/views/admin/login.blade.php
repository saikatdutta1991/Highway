<!DOCTYPE html>
<html >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Highway Trip Admin LOGIN</title>
    <link rel="stylesheet" href="{{url('admin_assets')}}/css/style.css">
    <style type="text/css">
        body
        {
            background: url('{{url('admin_assets')}}/background.jpg');
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        .btn
        {
            background: #ec511f;
        }

        #logo-title
        {
            color: white;
            font-weight: 700;
            text-shadow: 0px 0px 1px white;
            font-size: 20px;
            font-family: sans-serif;

        }

        #toggleProfile
        {
            border: 3px solid white;
        }


        .input 
        {
            padding: 5px;
        }

        .input:focus + .label, input:valid + .label{
            color: rgba(0, 0, 0, 0.52);
        }

    </style>
</head>

<body>
  <!--Google Font - Work Sans-->
<link href='https://fonts.googleapis.com/css?family=Work+Sans:400,300,700' rel='stylesheet' type='text/css'>

<div class="container">
    
    <div style="text-align: center;margin-bottom: 10px;">
        <label id="logo-title">HIGHWAY TRIP ADMIN PANEL</label>
    </div>
    
  <div class="profile">
    <button class="profile__avatar" id="toggleProfile">
     <img src="{{url('admin_assets')}}/login_round_logo.jpg" alt="Avatar" /> 
    </button>
    <div class="profile__form">
    <form id="login-form" method="post" action="{{url('admin/login')}}">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
      <div class="profile__fields">
        <div class="field">
          <input type="text" id="fieldUser" class="input" name="email"/>
          <label for="fieldUser" class="label">Email</label>
        </div>
        <div class="field">
          <input type="password" id="fieldPassword" class="input" name="password"/>
          <label for="fieldPassword" class="label">Password</label>
        </div>

       
        <div class="profile__footer" style="text-align: center">
          <button class="btn" type="submit"><img id="loader" src="{{url('admin_assets')}}/gear.gif" style="width: 16px;display: none;"> Login</button><br>
          <label id="error-level" style="font-size:10px;color:red;display:none">*<span>Invalid email</span></label>
        </div>
      </div>
    </form>

     </div>
  </div>
</div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script  src="{{url('admin_assets')}}/js/index.js"></script>

    <script type="text/javascript">

        function showLoader()
        {
            $("#loader").fadeIn();
        }

        function hideLoader()
        {
            $("#loader").fadeOut();
        }
    
        function hideError()
        {
            $("#error-level").fadeOut();
        }

        function showError(text, color='red')
        {
            $("#error-level").find('span').text(text)
            $("#error-level").css('color', color).fadeIn()
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
                        showError(response.text);
                    } else if(response.success){
                        showError('Loggin! redirecting to dashboard', 'green');

                        setTimeout(function(){
                            window.location.reload();
                        });
                        
                    }

                   
                }).fail(function(response) {
                    hideLoader();
                    showError('Unknown server error. Contact to you server admin');
                });


            });


           

        });


    </script>


</body>
</html>