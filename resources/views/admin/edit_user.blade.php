@extends('admin.layouts.master')
@section('title', 'Users')
@section('users_active', 'active')
@section('top-header')
<style>
    .profile-photo 
    {
    width: 200px;
    height: 200px;
    /* border-radius: 50%; */
    border: 2px solid #fb483a;
    }
    .my-custom-class-for-label 
    {
    display:inline-block;
    text-align:center;
    border: 1px solid #eb3a44;
    border-radius: 5px;
    background: #fee1d7;
    text-align: center;
    line-height: 20px;
    font-weight: bold;
    font-size: 14px;
    color: green;
    }
    #aniimated-thumbnials > div > a > img 
    {
    margin-bottom : 0px;
    height: 200px;
    width: 100%;
    }
    #aniimated-thumbnials > div
    {
    text-align : center;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>EDIT USER</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-pink hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">send</i>
                </div>
                <div class="content">
                    <div class="text">TOTAL REQUESTS</div>
                    <div class="number count-to" data-from="0" data-to="{{$totalCompletedRequests}}" data-speed="1000" data-fresh-interval="20">1</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">cancel</i>
                </div>
                <div class="content">
                    <div class="text">CANCELED</div>
                    <div class="number count-to" data-from="0" data-to="{{$totalCanceledRequests}}" data-speed="1000" data-fresh-interval="20">4</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-orange hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">stars</i>
                </div>
                <div class="content">
                    <div class="text">RATING</div>
                    <div class="number">{{$user->rating}}</div>
                </div>
            </div>
        </div>
    </div>
    <!-- With Material Design Colors -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        <i class="material-icons" style="vertical-align:sub">account_circle</i>
                        UESR PROFILE
                    </h2>
                    <small>Here you can view, edit, update user details</small>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li data-user-id="{{$user->id}}" id="profile-update-menu-item"><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons">save</i> Save profile</a></li>
                                <li id="password-reset-menu-item"><a href="javascript:void(0);" class="waves-effect waves-block"><i class="material-icons">https</i> Reset password</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div class="alert bg-pink" style="display:none" id="profile-update-alert"></div>
                    <div class="row clearfix">
                        <form id="user-profile-form">
                            {!! csrf_field() !!}
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">perm_identity</i>
                                    </span>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Frist Name" name="first_name" value="{{$user->fname}}" onkeyup="this.value=this.value.charAt(0).toUpperCase() + this.value.slice(1)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Last Name"  name="last_name" value="{{$user->lname}}" onkeyup="this.value=this.value.charAt(0).toUpperCase() + this.value.slice(1)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">email</i>
                                    </span>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Email" name="email" value="{{$user->email}}">
                                    </div>
                                    <span class="input-group-addon">
                                    @if($user->is_email_verified)
                                    <i class="material-icons col-green"  data-toggle="tooltip" data-placement="left" title="Email verified">done_all</i>
                                    @else 
                                    <i class="material-icons col-grey"  data-toggle="tooltip" data-placement="left" title="Email not verified">done_all</i>
                                    @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">phone</i>
                                    </span>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="mobile_number" value="{{$user->country_code}}-{{$user->mobile_number}}"placeholder="Mobile Number" data-toggle="tooltip" data-placement="left" title="+[countrycode][-][mobileno] eg: +91-9093034785">
                                    </div>
                                    <span class="input-group-addon">
                                    @if($user->is_mobile_number_verified)
                                    <i class="material-icons col-green"  data-toggle="tooltip" data-placement="left" title="Mobile number verified">done_all</i>
                                    @else 
                                    <i class="material-icons col-grey"  data-toggle="tooltip" data-placement="left" title="Mobile number not verified">done_all</i>
                                    @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">accessibility</i>
                                    </span>
                                    <div class="form-line">
                                        <select class="form-control show-tick" name="gender">
                                            <option value = "male" @if($user->gender == 'male') selected @endif>Male</option>
                                            <option value = "female" @if($user->gender == 'female') selected @endif>Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <small>See all city rides <a href="{{route('admin.rides.city')."?user_id={$user->id}&name={$user->fullname()}"}}">Click here</a></small>
                </div>
            </div>
        </div>
    </div>
   
</div>
</div>
@endsection
@section('bottom')
<script>
    var userId = {{$user->id}};
    var csrf_token = "{{csrf_token()}}";
    $(document).ready(function(){

        /**
         * password reset
         */
        $("#password-reset-menu-item").on('click', function(){
    
            swal({
                title: "Are you really want to reset password?",
                text: "New password will be sent to user via email and sms",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, reset",
                cancelButtonText: "No, cancel please",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    
                    let userResetPasswordApi = "{{route('admin.user.password.reset', ['user_id' => '*'])}}";
                    var url = userResetPasswordApi.replace('*', userId);
    
                    $.post(url, {_token:csrf_token}, function(response){
                        console.log(response)
                        setTimeout(function(){
                            swal("User password reset", "<<New password also sent via email and sms>>", "success");
                        }, 1000)
                                    
                    }).fail(function(response) {
                        showNotification('bg-black', 'Unknown server error', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    });
            
                    swal.close();
    
    
                } 
    
            });
    
    
        });


        /**
         * profile update menu item
         */       
        $("#profile-update-menu-item").on('click', function(){
    
            var userId = $(this).data('user-id');

            let updateUserApi = "{{route('admin.user.update', ['user_id' => '*'])}}";
            var url = updateUserApi.replace('*', userId);

            var data = $("#user-profile-form").serializeArray();
    
            console.log(url, data)
    
            $("#profile-update-alert").slideUp()
    
            $.post(url, data, function(response){
                console.log(response)
                if(response.success) {
                    $("#profile-update-alert").slideUp()
                    showNotification('bg-black', 'User profile updated successfully.', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    return;
                } 
    
                //failed 
                $("#profile-update-alert").text(response.data[Object.keys(response.data)[0]]).slideDown()
    
            })
            .fail(function(response) {
                showNotification('bg-black', 'Unknown server error. Failed to update user', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            });
    
        });


    });
</script>
@endsection