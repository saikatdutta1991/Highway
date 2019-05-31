@extends('admin.layouts.master')
@section('settings_active', 'active')
@section('settings_facebook_active', 'active')
@section('title', 'Facebook Settings')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>FACEBOOK SETTINGS</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        FACEBOOK AUTH SETTINGS
                        <small>Set your facebook project client id and secret key in order to enable facebook authentication. <a href="https://developers.facebook.com/" target="_blank">Facebook Developer</a></small>
                    </h2>
                </div>
                <div class="body">
                    <form id="facebook-auth-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>User Facebook App Client ID</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: 1906395459374643" name="user_facebook_client_id" value="{{$userFacebookClientId}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>User Facebook App Secret Key</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: a8b96a7a91fb1953bf5625f4ac4239kdj" name="user_facebook_secret_key" value="{{$userFacebookSecretKey}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Driver Facebook App Client ID</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: 1906395459374643" name="driver_facebook_client_id" value="{{$driverFacebookClientId}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Driver Facebook App Secret Key</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: a8b96a7a91fb1953bf5625f4ac4239kdj" name="driver_facebook_secret_key" value="{{$driverFacebookSecretKey}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="facebook-auth-save-btn" class="btn bg-pink waves-effect">
                                <i class="material-icons">save</i>
                                <span>SAVE</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('bottom')
<script>
    $(document).ready(function(){
    
        var facebook_save_url = "{{route('admin.settings.email.test')}}";
        $("#facebook-auth-form").on('submit', function(event){
            event.preventDefault();
    
            var data = $(this).serializeArray();
            console.log(data)
    
            $.post(facebook_save_url, data, function(response){
                console.log(response)
    
                if(response.success){
                    showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    return;
                }
    
                showNotification('bg-black', 'Internal server error. Try again !!', 'top', 'right', 'animated flipInX', 'animated flipOutX');
    
            }).fail(function(){
                showNotification('bg-black', 'Internal server error. Try again !!', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            })
    
        })
    
    
    
    })
</script>
@endsection