@extends('admin.layouts.master')
@section('settings_active', 'active')
@section('settings_firebase_active', 'active')
@section('title', 'Firebase Settings')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>FIREBASE SETTINGS</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        FIREBASE PUSH NOTIFICATION SETTINGS
                        <small>Set your firebase project sender id and server key in order to enable push notification. <a href="https://console.firebase.google.com" target="_blank">Firebase Console</a></small>
                    </h2>
                </div>
                <div class="body">
                    <form id="firebase-push-notif-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>Firebase Project Sender ID</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: 347144635187" name="firebase_cloud_messaging_sender_id" value="{{$firebaseSenderId}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>Firebase Project Server key</b>
                                    <div class="form-line">
                                        <textarea rows="1" required class="form-control no-resize auto-growth" 
                                            placeholder="Ex: AAAAUNNuwyk:APA91bE9hKXtr2pg6zwrCygUuQn_Gnc34xg6TnXxIqbj-5jMsE1zDxIA-iBj6KcgAMbvURLGtdTrGN4pgpymL-1chrXQJBO6HioEdhxUm" 
                                            name="firebase_cloud_messaging_server_key">{{$firebaseServerKey}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="firebase-push-notif-save-btn" class="btn bg-pink waves-effect">
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
<!-- Autosize Plugin Js -->
<script src="{{url('admin_assets/admin_bsb')}}/plugins/autosize/autosize.js"></script>
<script>
    $(document).ready(function(){
        autosize($('textarea'));
    
        var firebase_save_url = '{{url("admin/settings/firebase/save")}}'
        $("#firebase-push-notif-form").on('submit', function(event){
            event.preventDefault();
    
            var data = $(this).serializeArray();
            console.log(data)
    
            $.post(firebase_save_url, data, function(response){
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