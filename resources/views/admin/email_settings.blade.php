@extends('admin.layouts.master')
@section('settings_active', 'active')
@section('settings_email_active', 'active')
@section('title', 'Email Settings')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>EMAIL SETTING</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        EMAIL DRIVER
                        <small>Here you can select you email driver. According to that you have set up your email provider credentials below.</small>
                    </h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li id="test-email-menu-item"><a href="javascript:void(0);"><i class="material-icons">email</i>Test Email</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body" id="driver-div">
                    <div class="row clearfix">
                        <div class="col-sm-3">
                            <input name="m_driver" type="radio" id="m_driver_none" class="with-gap radio-col-pink" value="" @if($setting->get('is_mail_send_activated') == 'false' || $setting->get('is_mail_send_activated') == '') checked  @endif/>
                            <label for="m_driver_none">NONE</label>
                        </div>
                        <div class="col-sm-3">
                            <input name="m_driver" type="radio" id="m_driver_smtp" class="with-gap radio-col-pink" value="smtp" @if($setting->get('mail_driver') == 'smtp' && !($setting->get('is_mail_send_activated') == 'false' || $setting->get('is_mail_send_activated') == '') ) checked @endif/>
                            <label for="m_driver_smtp">SMTP</label>
                        </div>
                        <div class="col-sm-3">
                            <input name="m_driver" type="radio" id="m_driver_mailgun" class="with-gap radio-col-pink" value="mailgun" @if($setting->get('mail_driver') == 'mailgun' && !($setting->get('is_mail_send_activated') == 'false' || $setting->get('is_mail_send_activated') == '')) checked @endif/>
                            <label for="m_driver_mailgun">MAILGUN</label>
                        </div>
                        <div class="col-sm-3">
                            <input name="m_driver" type="radio" id="m_driver_mandrill" class="with-gap radio-col-pink" value="mandrill" @if($setting->get('mail_driver') == 'mandrill' && !($setting->get('is_mail_send_activated') == 'false' || $setting->get('is_mail_send_activated') == '')) checked @endif/>
                            <label for="m_driver_mandrill">MANDRILL</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- email from and name  -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        EMAIL SENDER
                        <small>All the emails will be sent from below email and name.</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="email-sender-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>FROM EMAIL</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: support@yourdomain.com" name="email_support_from_address" value="{{$setting->get('email_support_from_address')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>FROM NAME</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: your website name" name="email_from_name" value="{{$setting->get('email_from_name')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <button type="button" id="email-sender-save-btn" class="btn btn-block bg-pink waves-effect">
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
    <!-- email from and name  -->
    <!-- smtp settings -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header" data-toggle="collapse" data-target="#smtp-div" aria-expanded="false" aria-controls="smtp-div">
                    <h2>
                        SMTP CREDENTIALS
                        <small>Here you have to add your SMTP mail provider details.</small>
                    </h2>
                </div>
                <div class="body email-collapse-body collapse @if($setting->get('mail_driver') == 'smtp' && !($setting->get('is_mail_send_activated') == 'false' || $setting->get('is_mail_send_activated') == '')) in @endif" id="smtp-div">
                    <form method="POST" id="smtp-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Host</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: smtp.domain.com" name="smtp_host" value="{{$setting->get('smtp_host')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Port</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: 587" name="smtp_port" value="{{$setting->get('smtp_port')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Username</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: username@domain.com" name="smtp_username" value="{{$setting->get('smtp_username')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Password</b>
                                    <div class="form-line">
                                        <input type="password" class="form-control" placeholder="Ex: ****" name="smtp_password" value="{{$setting->get('smtp_password')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Encryption</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: tls | ssl" name="smtp_encryption" value="{{$setting->get('smtp_encryption')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <button type="button" id="smtp-save-btn" class="btn btn-block bg-pink waves-effect">
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
    <!-- smtp settings -->
    <!-- mailgun settings -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header" data-toggle="collapse" data-target="#mailgun-div" aria-expanded="false" aria-controls="mailgun-div">
                    <h2>
                        MAILGUN CREDENTIALS
                        <small>Here you have to add your MAILGUN mail provider details.</small>
                    </h2>
                </div>
                <div class="body email-collapse-body collapse @if($setting->get('mail_driver') == 'mailgun' && !($setting->get('is_mail_send_activated') == 'false' || $setting->get('is_mail_send_activated') == '')) in @endif" id="mailgun-div">
                    <form method="POST" id="mailgun-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Host</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: smtp.mailgun.com" name="mailgun_host" value="{{$setting->get('mailgun_host')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Port</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: 587" name="mailgun_port" value="{{$setting->get('mailgun_port')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Username</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: postmaster@something.mailgun.com" name="mailgun_username" value="{{$setting->get('mailgun_username')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Password</b>
                                    <div class="form-line">
                                        <input type="password" class="form-control" placeholder="Ex: ****" name="mailgun_password" value="{{$setting->get('mailgun_password')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Encryption</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: tls | ssl" name="mailgun_encryption" value="{{$setting->get('mailgun_encryption')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Domain</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: something@mailgun.com" name="mailgun_domain" value="{{$setting->get('mailgun_domain')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Api Key</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: XXXXXXXX" name="mailgun_secret" value="{{$setting->get('mailgun_secret')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <button type="button" id="mailgun-save-btn" class="btn btn-block bg-pink waves-effect">
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
    <!-- mailgun settings -->
    <!-- mandrill settings -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header" data-toggle="collapse" data-target="#mandrill-div" aria-expanded="false" aria-controls="mandrill-div">
                    <h2>
                        MANDRILL CREDENTIALS
                        <small>Here you have to add your MANDRILL mail provider details.</small>
                    </h2>
                </div>
                <div class="body email-collapse-body collapse @if($setting->get('mail_driver') == 'mandrill' && !($setting->get('is_mail_send_activated') == 'false' || $setting->get('is_mail_send_activated') == '')) in @endif" id="mandrill-div">
                    <form method="POST" id="mandrill-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Host</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: smtp.mandrillapp.com" name="mandrill_host" value="{{$setting->get('mandrill_host')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Port</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: 587" name="mandrill_port" value="{{$setting->get('mandrill_port')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Username</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: postmaster@something.mandrill.com" name="mandrill_username" value="{{$setting->get('mandrill_username')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Api Key</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: XXXXX" name="mandrill_secret" value="{{$setting->get('mandrill_secret')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Encryption</b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: tls | ssl" name="mandrill_encryption" value="{{$setting->get('mandrill_encryption')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <button type="button" id="mandrill-save-btn" class="btn btn-block bg-pink waves-effect">
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
    <!-- mandrill settings -->
</div>
</div>
<div class="modal fade" id="test-email-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">SEND EMAIL TEST</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <span class="input-group-addon">
                            <i class="material-icons">email</i>
                            </span>
                            <div class="form-line">
                                <input type="text" id="test-email-input" class="form-control" placeholder="Enter email id to send">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" id="mail-send-error-div" style="display:none">
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
                <button type="button" class="btn btn-link waves-effect" id="test-mail-send-btn">SEND</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('bottom')
<script>
    /**
        smooth scroll to anchor
     */
    function scroll_to_anchor(anchor_id)
    {
        var tag = $("#"+anchor_id+"");
        var height = tag.parent().find(':first-child').outerHeight()
        console.log(height)
        $('html,body').animate({scrollTop: tag.offset().top - height * 2},'slow');
    }
    
    
    /**
        jquery amination animate.js
     */
    $.fn.extend({
        animateCss: function (animationName) {
            var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
            $(this).addClass('animated ' + animationName).one(animationEnd, function() {
                $(this).removeClass('animated ' + animationName);
            });
        }
    });
    
    function hideMessageErrorResDiv()
    {
        $("#mail-send-error-div").hide();
        $("#mail-send-error-div > .preloader").hide()
        $("#mail-send-error-div > .res-text").hide()
    }
    
    function showEmailSendLoader(message)
    {
        $("#mail-send-error-div").show();
        $("#mail-send-error-div > .preloader").show()
        $("#mail-send-error-div > .res-text").show().text(message).removeClass('col-red').addClass('col-black');
    }
    
    function showEmailSendError(message)
    {
        $("#mail-send-error-div").show();
        $("#mail-send-error-div > .preloader").hide()
        $("#mail-send-error-div > .res-text").show().text(message).addClass('col-red').removeClass('col-black');
    }
    
    
    $("#test-mail-send-btn").on('click', function(){
    
        showEmailSendLoader('Sending...')
    
        var data = {
            _token : csrf_token,
            to_email : $("#test-email-input").val(),
            body:'Email send successful',
            subject : 'Test Email'
        };
        $.post(test_emai_url, data, function(response){
            console.log('testing email send reaponse', response)
    
            if(response.success) {
                $("#test-email-modal").modal('hide')
                swal("Email sent !!", "", "success"); 
            } else if(!response.success && response.type == 'MAIL_DRIVER_NO_SET'){ 
                showEmailSendError(response.text)
            } else if(!response.success && response.type == 'MAIL_SEND_ERROR') {
                showEmailSendError(response.data.error_message)
            }
            
        }).fail(function(){
            showEmailSendError('Internal server error. Try later.')
        });
    
    })
    
    
    
    $("#test-email-menu-item").on('click', function(){
        hideMessageErrorResDiv();
        $("#test-email-input").val('')
        $("#test-email-modal").modal('show')
    })
    
    
    
    
    
    var driver = '{{$setting->get('mail_driver')}}';
    var csrf_token = '{{csrf_token()}}'
    var setting_save_url = "{{route('admin.settings.email.save')}}";
    var test_emai_url = "{{route('admin.settings.email.test')}}";
    
    //on change driver check email credentials set or not
    $("input[name='m_driver']").on('change', function(){
    
        driver = $(this).val(); 
        console.log(driver)
    
        //collapse all divs
        $(".email-collapse-body").removeClass('in')
       /*  $("#smtp-div").removeClass('in')
        $("#mailgun-div").removeClass('in')
        $("#mandrill-div").removeClass('in') */
    
    
        //check email from and name is set if not then ask set
        if(driver != '' && ($("input[name='email_support_from_address']").val() == '' || $("input[name='email_from_name']").val() == '') ) {
            showNotification('bg-black', 'Set email sender details below', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            return;
        }
    
        //if none then collapse all credentials div and call api to save driver
        if(driver == '') {
            //call api to save driver
            saveDriver(driver);
            showNotification('bg-black', 'Mail driver saved', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            return;
        } 
        //first check smtp div host value is empty or not
        //smtp host is filled then call save driver
        else if(driver == 'smtp') {
    
            if($("#smtp-div input[name='smtp_host']").val() == '') {
                //go to smtp host div
                $("#smtp-div").addClass('in')
                scroll_to_anchor('smtp-div')
                $('#smtp-div').parent().animateCss('shake');
    
                showNotification('bg-black', 'Set SMTP provider settings', 'top', 'right', 'animated flipInX', 'animated flipOutX');
    
                return;
            }
    
            //call api to save
            saveSmtp()
            $("#smtp-div").addClass('in')
            showNotification('bg-black', 'Mail driver set to SMTP', 'top', 'right', 'animated flipInX', 'animated flipOutX');
    
        } 
        else if(driver == 'mailgun') {
    
            if($("#mailgun-div input[name='mailgun_host']").val() == '') {
                //go to mailgun host div
                $("#mailgun-div").addClass('in')
                scroll_to_anchor('mailgun-div')
                $('#mailgun-div').parent().animateCss('shake');
    
                showNotification('bg-black', 'Set MAILGUN provider settings', 'top', 'right', 'animated flipInX', 'animated flipOutX');
    
                return;
            }
    
            //call api to save
            saveMailgun()
            $("#mailgun-div").addClass('in')
            showNotification('bg-black', 'Mail driver set to MAILGUN', 'top', 'right', 'animated flipInX', 'animated flipOutX');
    
        } 
        else if(driver == 'mandrill') {
    
            if($("#mandrill-div input[name='mandrill_host']").val() == '') {
                //go to mandrill host div
                $("#mandrill-div").addClass('in')
                scroll_to_anchor('mandrill-div')
                $('#mandrill-div').parent().animateCss('shake');
    
                showNotification('bg-black', 'Set MANDRILL provider settings', 'top', 'right', 'animated flipInX', 'animated flipOutX');
    
                return;
            }
    
            //call api to save
            saveMandrill();
            $("#mandrill-div").addClass('in')
            showNotification('bg-black', 'Mail driver set to MANDRILL', 'top', 'right', 'animated flipInX', 'animated flipOutX');
    
        } 
    
    
    
    })
    
    
    /**
        email sender save 
     */
    $("#email-sender-save-btn").on('click', function(){
    
        console.log('emailsender', driver)
    
        saveEmailSender();
        showNotification('bg-black', 'Mail sender settings saved', 'top', 'right', 'animated flipInX', 'animated flipOutX');
        $("input[name='m_driver']:checked").change();
    });
    
    function saveEmailSender()
    {
        var data = {
            _token : csrf_token,
            email_settings : objectifyForm($("#email-sender-form").serializeArray()),
        };
        console.log('emailsenderform save data', data)
        $.post(setting_save_url, data, function(response){
            console.log('emailsenderform save reaponse', response)
        })
    }
    
    
    
    /**
        save mandrill
     */
    $("#mandrill-save-btn").on('click', function(){
        saveMandrill();
        showNotification('bg-black', 'Mail driver set to MANDRILL', 'top', 'right', 'animated flipInX', 'animated flipOutX');
    })
    function saveMandrill()
    {
        var data = {
            _token : csrf_token,
            email_settings : objectifyForm($("#mandrill-form").serializeArray()),
        };
        console.log('mandrill save data', data)
        $.post(setting_save_url, data, function(response){
            console.log('mandrill save reaponse', response)
            saveDriver($("input[name='m_driver']:checked").val())
        })
    }
    
    
    /**
        save Smtp
     */
    $("#smtp-save-btn").on('click', function(){
        saveSmtp();
        showNotification('bg-black', 'Mail driver set to SMTP', 'top', 'right', 'animated flipInX', 'animated flipOutX');
    })
    function saveSmtp()
    {
        var data = {
            _token : csrf_token,
            email_settings : objectifyForm($("#smtp-form").serializeArray()),
        };
        console.log('smtp save data', data)
        $.post(setting_save_url, data, function(response){
            console.log('smtp save reaponse', response)
            saveDriver($("input[name='m_driver']:checked").val())
        })
    }
    
    
    /**
        save mailgun
     */
    $("#mailgun-save-btn").on('click', function(){
        saveMailgun();
        showNotification('bg-black', 'Mail driver set to MAILGUN', 'top', 'right', 'animated flipInX', 'animated flipOutX');
    })
    function saveMailgun()
    {
        var data = {
            _token : csrf_token,
            email_settings : objectifyForm($("#mailgun-form").serializeArray()),
        };
        console.log('mailgun save data', data)
        $.post(setting_save_url, data, function(response){
            console.log('mailgun save reaponse', response)
            saveDriver($("input[name='m_driver']:checked").val())
        })
    }
    
    
    //$("input[name='m_driver']:checked").val()
    /**
        save mail driver
     */
    function saveDriver(driver)
    {
        var email_settings = {'mail_driver':driver};
        $.post(setting_save_url, {_token:csrf_token, email_settings:email_settings}, function(response){
            console.log('driver save reaponse', response)
            scroll_to_anchor('driver-div')
        })
    }
    
    
    function objectifyForm(formArray, except ='_token') 
    {//serialize data function
    
        var returnArray = {};
        for (var i = 0; i < formArray.length; i++){
            if(formArray[i]['name'] == except) {
                continue;
            }
            returnArray[formArray[i]['name']] = formArray[i]['value'];
        }
        return returnArray;
    }
    
    
</script>
@endsection