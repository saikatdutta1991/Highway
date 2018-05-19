@extends('admin.layouts.master')
@section('referral_active', 'active')
@section('settings_referral_active', 'active')
@section('title', 'Referral Settings')
@section('top-header')
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>REFERRAL SETTINGS</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        ENABLE/DISABLE
                        <small>Here you can enable or disblae referral module from backend. But remember app side is not dynamic so need to remove from app side.</small>
                    </h2>
                </div>
                <div class="body" id="driver-div">
                    <div class="row clearfix">
                        <div class="col-sm-6">
                            <input name="referral_status" type="radio" id="referral_status_disabled" class="with-gap radio-col-pink" value="disable" @if(!$isReferralEnabled) checked @endif/>
                            <label for="referral_status_disabled">DISABLED</label>
                        </div>
                        <div class="col-sm-6">
                            <input name="referral_status" type="radio" id="referral_status_enabled" class="with-gap radio-col-pink" value="enable" @if($isReferralEnabled) checked @endif/>
                            <label for="referral_status_enabled">ENABLED</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        REFERRAL BONUS AMOUNT SETTINGS
                        <small>Here you can set referrer and referred bonus amount. When user register with referral code.</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="referral-bonus-amount-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Referrer Bonus Amount</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="One who is referring his referral code to others.">help_outline</i>
                                    <div class="form-line">
                                        <input type="number" step="1" pattern="\d*" required class="form-control" placeholder="Ex: 200" name="referrer_bonus_amount" value="{{$referrerBonusAmount}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Referred Bonus Amount</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="One who is being referred and inserting referral code while regisering.">help_outline</i>
                                    <div class="form-line">
                                        <input type="number" step="1" pattern="\d*" required class="form-control" placeholder="Ex: 200" name="referred_bonus_amount" value="{{$referredBonusAmount}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="referral-bonus-amount-save-btn" class="btn bg-pink waves-effect">
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


        $("#referral-bonus-amount-form").on('submit', function(event){
            event.preventDefault();

            let data = $(this).serializeArray();
            let url = "{{route('admin.referral_save_bonus')}}"

            $.post(url, data, function(response){
                
                if(response.success) {
                    showNotification('bg-black', 'Referral bonus amount saved', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                } 
            })
            .fail(function(response) {
                showNotification('bg-black', 'Unknown server error. Failed to approve driver', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            });

        })



    
        $('input[name="referral_status"]').on('change', function(){

            let enabled = $(this).val()
            let data = {
                _token : '{{csrf_token()}}',
                enable : enabled
            }
            let url = "{{route('admin.referral_save_enable')}}"
            $.post(url, data, function(response){
                console.log(enabled)
                if(enabled === 'enable') {
                    showNotification('bg-black', 'Referral module enabled', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                } else {
                    showNotification('bg-black', 'Referral module disabled', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                }
            })
            .fail(function(response) {
                showNotification('bg-black', 'Unknown server error. Failed to approve driver', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            });
        

        })
    
    
    })
</script>
@endsection