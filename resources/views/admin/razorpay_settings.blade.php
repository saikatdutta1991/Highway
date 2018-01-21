@extends('admin.layouts.master')
@section('settings_active', 'active')
@section('settings_razorpay_active', 'active')
@section('title', 'Razorpay Settings')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>RAZORPAY SETTINGS</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        RAZORPAY API SETTINGS
                        <small>Set your Razorpay merchant id, key and secret. <a href="https://dashboard.razorpay.com" target="_blank">Razorpay Dashboard</a></small>
                    </h2>
                </div>
                <div class="body">
                    <form id="razorpay-auth-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Razorpay Merchant ID</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="" name="razorpay_merchant_id" value="{{$RAZORPAY_MERCHANT_ID}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Razorpay Api Key ID</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: " name="razorpay_api_key" value="{{$RAZORPAY_API_KEY}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>Razorpay Api Key Secret</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: " name="razorpay_api_secret" value="{{$RAZORPAY_API_SECRET}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="razorpay-auth-save-btn" class="btn bg-pink waves-effect">
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
    
        var razorpay_save_url = '{{url("admin/settings/razorpay/save")}}'
        $("#razorpay-auth-form").on('submit', function(event){
            event.preventDefault();
    
            var data = $(this).serializeArray();
            console.log(data)
    
            $.post(razorpay_save_url, data, function(response){
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