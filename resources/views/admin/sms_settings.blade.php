@extends('admin.layouts.master')
@section('settings_active', 'active')
@section('settings_sms_active', 'active')
@section('title', 'SMS Settings')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>SMS SETTING</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        SMS PROVIDER
                        <small>Here you can select you sms provider. According to that you have set up your sms provider credentials below.</small>
                    </h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li id="test-sms-menu-item"><a href="javascript:void(0);"><i class="material-icons">sms</i>Test SMS</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <select class="form-control show-tick" name="sms_provider">
                                <option value="">-- Please select sms provider --</option>
                                <option class = "sms-provider-option" value="msg91" @if($setting->get('sms_provider') == 'msg91') selected @endif>MSG91</option>
                                <option class = "sms-provider-option" value="twilio" @if($setting->get('sms_provider') == 'twilio') selected @endif>TWILIO</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- msg91 msg_91  -->
    <div class="row clearfix">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        MSG91 CREDENTIALS
                        <small>Setup your msg91 account sms api credentials</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="msg91-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>SENDER ID</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: GOFAST(max 6 char)" name="msg91_sender_id" value="{{$setting->get('msg91_sender_id')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>AUTH KEY</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: 187213Al383QppsM5a2983kdje" name="msg91_auth_key" value="{{$setting->get('msg91_auth_key')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <button type="button" id="msg91-form-save-btn" class="btn btn-block bg-pink waves-effect">
                                <i class="material-icons">save</i>
                                <span>SAVE</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        TWILIO CREDENTIALS
                        <small>Setup your msg91 account sms api credentials</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="twilio-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>SID</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: djeyx8ej3kdm" name="twilio_sid" value="{{$setting->get('twilio_sid')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>TOKEN</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: axhen9n3ndhd" name="twilio_token" value="{{$setting->get('twilio_token')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>FROM NUMBER</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: +18607861087" name="twilio_from" value="{{$setting->get('twilio_from')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <button type="button" id="twilio-form-save-btn" class="btn btn-block bg-pink waves-effect">
                                <i class="material-icons">save</i>
                                <span>SAVE</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <!-- </div> -->
    </div>
    <!-- msg91 provider  -->
</div>
</div>
<div class="modal fade" id="test-sms-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">SEND TEST SMS</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-addon">
                            <i class="material-icons">sms</i>
                            </span>
                            <div class="form-line">
                                <input type="text" id="test-sms-input" class="form-control" placeholder="Ex: +91-984758393" data-toggle="tooltip" data-placement="left" title="+[countrycode][-][mobileno] eg: +91-9093034785">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" id="sms-send-error-div" style="display:none">
                        <div class="preloader pl-size-xs" style="float:left;margin-right: 5px;display:none">
                            <div class="spinner-layer pl-red-grey">
                                <div class="circle-clipper left">
                                    <div class="circle"></div>
                                </div>
                                <div class="circle-clipper right">
                                    <div class="circle"></div>
                                </div>
                            </div>
                        </div>
                        <h6 class="res-text" style="float:left;display:none">Sending ...</h6>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" id="test-sms-send-btn">SEND</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('bottom')
<script>

    var csrf_token = '{{csrf_token()}}'
    var sms_save_url = '{{url("admin/settings/sms/save")}}'
    var test_sms_url = '{{url("admin/settings/sms/test")}}'
    $('select[name="sms_provider"]').on('change', function(){
        var sms_provider = $('select[name="sms_provider"]').val()
        console.log(sms_provider)

        $.post(sms_save_url, {_token:csrf_token, sms_provider:sms_provider}, function(response){
            console.log(response)
            if(response.success) {
                showNotification('bg-black', 'Sms provider saved', 'top', 'right', 'animated flipInX', 'animated flipOutX');  
            }
        }).fail(function(){
            showEmailSendError('Internal server error. Try later.')
        });


    })


    $("#msg91-form-save-btn").on('click', function(){

        var data = $("#msg91-form").serializeArray();
        console.log(data)

        $.post(sms_save_url, data, function(response){
            console.log(response)
            if(response.success) {
                showNotification('bg-black', 'Msg91 settings saved', 'top', 'right', 'animated flipInX', 'animated flipOutX');  
            }
        }).fail(function(){
            showEmailSendError('Internal server error. Try later.')
        });


    })


    $("#twilio-form-save-btn").on('click', function(){

        var data = $("#twilio-form").serializeArray();
        console.log(data)

        $.post(sms_save_url, data, function(response){
            console.log(response)
            if(response.success) {
                showNotification('bg-black', 'Twilio settings saved', 'top', 'right', 'animated flipInX', 'animated flipOutX');  
            }
        }).fail(function(){
            showEmailSendError('Internal server error. Try later.')
        });


    })



    function hideMessageErrorResDiv()
    {
        $("#sms-send-error-div").hide();
        $("#sms-send-error-div > .preloader").hide()
        $("#sms-send-error-div > .res-text").hide()
    }
    
    function showEmailSendLoader(message)
    {
        $("#sms-send-error-div").show();
        $("#sms-send-error-div > .preloader").show()
        $("#sms-send-error-div > .res-text").show().text(message).removeClass('col-red').addClass('col-black');
    }
    
    function showEmailSendError(message)
    {
        $("#sms-send-error-div").show();
        $("#sms-send-error-div > .preloader").hide()
        $("#sms-send-error-div > .res-text").show().text(message).addClass('col-red').removeClass('col-black');
    }
    
    
    $("#test-sms-send-btn").on('click', function(){
    

        if(!new RegExp("^[+][0-9]+[-][0-9]+$").test($("#test-sms-input").val())) {
            showEmailSendError('Mobile number format is wrong')
            return;
        }


        showEmailSendLoader('Sending...')
    
        var data = {
            _token : csrf_token,
            to_number: $("#test-sms-input").val()
        };
        $.post(test_sms_url, data, function(response){
            console.log('testing sms send reaponse', response)
    
            if(response.success) {
                $("#test-sms-modal").modal('hide')
                swal("Sms sent !!", "", "success"); 
            } else if(!response.success && response.type == 'SMS_SEND_ERROR') {
                showEmailSendError(response.data.error_message)
            }
            
        }).fail(function(){
            showEmailSendError('Internal server error. Try later.')
        });
    
    })
    
    
    
    $("#test-sms-menu-item").on('click', function(){
        hideMessageErrorResDiv();
        $("#test-sms-input").val('')
        $("#test-sms-modal").modal('show')
    })




</script>
@endsection