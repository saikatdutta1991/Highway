@extends('admin.layouts.master')
@section('driver_active', 'active')
@section('payout_settings_active', 'active')
@section('title', 'Payout Settings')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>DRIVER PAYOUT SETTINGS</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        DRIVER PAYOUT SETTINGS
                        <small>Set your driver payouts settings below.</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="payout-settings-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Driver City Ride Cancellation Limit</b>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" name="driver_cancel_ride_request_limit" value="{{$driver_cancel_ride_request_limit}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>City Ride Admin Commission (%)</b>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" name="city_ride_admin_commission" value="{{$city_ride_admin_commission}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Highway Ride Admin Commission (%)</b>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" name="highway_ride_admin_commission" value="{{$highway_ride_admin_commission}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Driver City Ride Cancellation Charge ({{$currency_symbol}})</b>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" name="driver_city_ride_cancellation_charge" value="{{$driver_city_ride_cancellation_charge}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Driver Highway Cancellation Charge ({{$currency_symbol}})</b>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" name="driver_highway_ride_cancellation_charge" value="{{$driver_highway_ride_cancellation_charge}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="general-website-save-btn" class="btn bg-pink waves-effect">
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
    
    var payoutSettingsSaveApi = "{{route('admin.payouts.settings.save')}}";

    $(document).ready(function(){
    
        $("#payout-settings-form").on('submit', function(event) {
            
            event.preventDefault();
            var data = $(this).serializeArray();
    
            $.post(payoutSettingsSaveApi, data, function(response){
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