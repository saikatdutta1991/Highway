@extends('admin.layouts.master')
@section('settings_active', 'active')
@section('settings_google_active', 'active')
@section('title', 'Google Settings')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>GOOGLE SETTINGS</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        GOOGLE AUTH SETTINGS
                        <small>Set your google project client id in order to enable google authentication. <a href="https://console.developers.google.com" target="_blank">Google Developer Console</a></small>
                    </h2>
                </div>
                <div class="body">
                    <form id="google-auth-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>User Google Android Auth Client ID</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: 9ld5f7uahh1.apps.googleusercontent.com" name="user_android_google_login_client_id" value="{{$userAndroidGoogleAuthClientId}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>User Google IOS Auth Client ID</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: 9ld5f7uahh1.apps.googleusercontent.com" name="user_ios_google_login_client_id" value="{{$userIosGoogleAuthClientId}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Driver Google Android Auth Client ID</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: 9ld5f7uahh1.apps.googleusercontent.com" name="driver_android_google_login_client_id" value="{{$driverAndroidGoogleAuthClientId}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Driver Google IOS Auth Client ID</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: 9ld5f7uahh1.apps.googleusercontent.com" name="driver_ios_google_login_client_id" value="{{$driverIosGoogleAuthClientId}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="google-auth-save-btn" class="btn bg-pink waves-effect">
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
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        GOOGLE MAPS SETTINGS
                        <small>Set your google project map api keys. <a href="https://console.developers.google.com" target="_blank">Google Developer Console</a></small>
                    </h2>
                </div>
                <div class="body">
                    <form id="google-map-api-key-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>Google Map Api Key</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: 9ld5f7uahh1.apps.googleusercontent.com" name="google_maps_api_key" value="{{$googleMapApiKey}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>Google Map Api Key For User Trip Booking Track</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: 9ld5f7uahh1.apps.googleusercontent.com" name="google_maps_api_key_booking_track" value="{{$google_maps_api_key_booking_track}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="google-map-api-key-save-btn" class="btn bg-pink waves-effect">
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


        var google_map_api_key_save_url = "{{route('admin.settings.google.mapkey.save')}}";
        $("#google-map-api-key-form").on('submit', function(event){
            event.preventDefault();
    
            var data = $(this).serializeArray();
            console.log(data)
    
            $.post(google_map_api_key_save_url, data, function(response){
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
    
        var google_save_url = "{{route('admin.settings.google.save')}}";
        $("#google-auth-form").on('submit', function(event){
            event.preventDefault();
    
            var data = $(this).serializeArray();
            console.log(data)
    
            $.post(google_save_url, data, function(response){
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