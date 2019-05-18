@extends('admin.layouts.master')
@section('support_active', 'active')
@section('support_settings', 'active')
@section('title', 'Support Settings')
@section('top-header')
<!-- Bootstrap Tagsinput Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
<style>
    .bootstrap-tagsinput {
        padding-left:0;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>SUPPORT SETTINGS</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        GENERAL SETTINGS
                        <small>Here you can set general settings for support system.</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="general-settings-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <div class="form-group demo-tagsinput-area">
                                    <div class="text">Emails to Notify When New Support Ticket Creates</div>
                                    <div class="form-line" >
                                        <input required type="text" class="form-control" data-role="tagsinput" value="{{$support_ticket_notify_emails}}" name="support_ticket_notify_emails">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="general-settings-save-btn" class="btn bg-pink waves-effect">
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
<!-- Bootstrap Tags Input Plugin Js -->
<script src="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
<script>
    $(document).ready(function(){


        var generalSettingsSaveApi = '{{route('admin.support.general.save')}}';
        $("#general-settings-form").on('submit', function(event){
            event.preventDefault();
    
            var data = $(this).serializeArray();
            console.log(data)
    
            $.post(generalSettingsSaveApi, data, function(response){
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