@extends('admin.layouts.master')
@section('settings_active', 'active')
@section('settings_general_active', 'active')
@section('title', 'General Settings')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>GENERAL SETTINGS</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        WEBSITE SETTINGS
                        <small>Set your website settings below.</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="general-website-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Website Name
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Website Name" data-content="Used to show website name in pages and emails etc.">help_outline</i>
                                    </b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="" name="website_name" value="{{$setting->get('website_name')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Website Title
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Website Title" data-content="Used to show website in browser tab title bar including admin panel">help_outline</i>
                                    </b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="" name="website_title" value="{{$setting->get('website_title')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Company Name
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Company Name" data-content="Used to show company name at webpage footer ex: company pvt. ltd.. Sometimes website name and company might be different.">help_outline</i>
                                    </b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="" name="website_company_name" value="{{$setting->get('website_company_name')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Copyright
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Company Copyright" data-content="Used to show website copyright year">help_outline</i>
                                    </b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="" name="website_copyright" value="{{$setting->get('website_copyright')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Currency
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Website Currency" data-content="This currency will be used to make all transctions. It is not changeable because multicurrency is not supported now. Multiple currency makes problem always. Admin is not supposed to change currency after installation.">help_outline</i>
                                    </b>
                                    <div class="form-line">
                                        <select class="form-control" name="currency">
                                        <option value="INR-₹" @if($setting->get('currency_code') == 'INR') selected @endif>INR-₹</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Admin Default Timezone
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Admin Panel Timezone" data-content="Used to show any kind of record timestamp in admin panel converted to this timezone.">help_outline</i>
                                    </b>
                                    <div class="form-line">
                                        <select class="form-control" data-live-search="true" name="default_timezone">
                                        @foreach($timezones as $timezone)
                                        <option value="{{$timezone}}" @if($setting->get('default_timezone') == $timezone) selected="true" @endif>{{$timezone}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Driver/User Default Timezone
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Driver/user timezone" data-content="You are not supposed to change user or driver timezone after installation. Driver and user both should have same timezone and same country.">help_outline</i>
                                    </b>
                                    <div class="form-line">
                                        <select class="form-control" data-live-search="true" disabled>
                                        @foreach($timezones as $timezone)
                                        <option value="{{$timezone}}" @if($setting->get('default_user_driver_timezone') == $timezone) selected="true" @endif>{{$timezone}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Contact Number
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Website Contact Number" data-content="Will be used to show website contact number in website pages and emails.">help_outline</i>
                                    </b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="" name="website_contact_number" value="{{$setting->get('website_contact_number')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Contact Email
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Website Contact Email" data-content="Will be used to show website contact email in website pages and emails.">help_outline</i>
                                    </b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="" name="website_contact_email" value="{{$setting->get('website_contact_email')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>Company Address
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Company Address" data-content="Will be used to show company addreess in other pages and emails sent from this server.">help_outline</i>
                                    </b>
                                    <div class="form-line">
                                        <textarea rows="1" required class="form-control no-resize auto-growth" 
                                            name="website_address">{{$setting->get('website_address')}}</textarea>
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
    <!-- website logo and fav icon  -->
    <div class="row clearfix">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        WEBSITE LOGO
                        <small>Upload Your website logo here.</small>
                    </h2>
                </div>
                <div class="body">
                    <div class="row clearfix">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                            <img id="logo-image" src="{{$setting->websiteLogoUrl()}}" class="img-responsive" style="display: inline-block;">
                        </div>
                    </div>
                    <form class="form-horizontal" id="upload-logo-form" method="POST" enctype="multipart/form-data">
                        <div class="row clearfix">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="input-group input-group-md">
                                    <span class="input-group-addon">
                                    <i class="material-icons col-pink" id="custom-logo-selcet-btn" style="cursor:pointer" data-toggle="tooltip" data-placement="left" title="Click icon and select photo">add_a_photo</i>
                                    </span>
                                    <div class="form-line">
                                        <input id="uploadLogo" type="file" style="display:none"/>
                                        <input type="text" id="custom-logo-selcet-text" class="form-control" placeholder="Click icon and select photo" disabled>
                                    </div>
                                    <span class="input-group-addon">
                                    <i class="material-icons col-pink" id="custom-logo-upload-btn" style="cursor:pointer" data-toggle="tooltip" data-placement="left" title="Upload photo">file_upload</i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="" id="logo-upload-progressbar-div">
                        <div class="progress">
                            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                        <small>Photo upload progress status: <span id="logo-upload-progress-status">0%</span></small>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        WEBSITE FAVICON
                        <small>Upload your website fav icon here. Size preferred 32X32.</small>
                    </h2>
                </div>
                <div class="body">
                    <div class="row clearfix">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                            <img id="favicon-image" src="{{$setting->websiteFavIconUrl()}}" class="img-responsive" style="display: inline-block;">
                        </div>
                    </div>
                    <form class="form-horizontal" id="upload-favicon-form" method="POST" enctype="multipart/form-data">
                        <div class="row clearfix">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="input-group input-group-md">
                                    <span class="input-group-addon">
                                    <i class="material-icons col-pink" id="custom-favicon-selcet-btn" style="cursor:pointer" data-toggle="tooltip" data-placement="left" title="Click icon and select photo">add_a_photo</i>
                                    </span>
                                    <div class="form-line">
                                        <input id="uploadFavicon" type="file" style="display:none"/>
                                        <input type="text" id="custom-favicon-selcet-text" class="form-control" placeholder="Click icon and select photo" disabled>
                                    </div>
                                    <span class="input-group-addon">
                                    <i class="material-icons col-pink" id="custom-favicon-upload-btn" style="cursor:pointer" data-toggle="tooltip" data-placement="left" title="Upload photo">file_upload</i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="" id="favicon-upload-progressbar-div">
                        <div class="progress">
                            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                        <small>Photo upload progress status: <span id="favicon-upload-progress-status">0%</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- website logo and fav icon  -->
</div>
</div>
@endsection
@section('bottom')
<!-- Autosize Plugin Js -->
<script src="{{url('admin_assets/admin_bsb')}}/plugins/autosize/autosize.js"></script>
<script>
    $(document).ready(function(){
        autosize($('textarea'));



        /**
            upload favicon photo
         */
        var xhr = null;
        $("#custom-favicon-upload-btn").on('click', function(){
    
            
            if(!$("#uploadFavicon")[0].files.length) {
                showNotification('bg-black', 'Select photo first', 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
                return;
            }

            $("#favicon-upload-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
            $("#favicon-upload-progress-status").text('0%');
    
            var form_data = new FormData();
            form_data.append("photo", $("#uploadFavicon")[0].files[0])
            form_data.append("_token", "{{csrf_token()}}")
    
            var url = "{{url('admin/settings/general/website/favicon/save')}}";  
    
            console.log(url)

    
            xhr = $.ajax({
                url: url,
                cache: false,
                async: true,
                contentType: false,
                processData:false,
                data: form_data,                 
                type: 'POST',
                xhr: function () {
                    var xhr = $.ajaxSettings.xhr();
                    xhr.upload.onprogress = function (e) {
                        // For uploads
                        if (e.lengthComputable) {
    
                            var percentage = Math.floor((e.loaded / e.total) * 100)
    
                            $("#favicon-upload-progressbar-div").find('.progress > .progress-bar').css('width', percentage+'%')
                            $("#favicon-upload-progress-status").text(percentage+'%');
                            console.log(percentage);
                        }
                    };
                    return xhr;
                },
                success : function(data) {
                    if(!data.success) {
                        $("#uploadFavicon").val('')
                        $("#custom-favicon-selcet-text").val('')
                        $("#favicon-upload-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
                        $("#favicon-upload-progress-status").text('0%');
                        showNotification('bg-black', data.data.photo, 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
                    } else {
    
                        $("#favicon-image").attr('src', '')
                        $("#favicon-image").attr('src', data.data.favicon_url)

                    }
                },
                error : function(){
                    $("#uploadFavicon").val('')
                    $("#custom-favicon-selcet-text").val('')
                    $("#favicon-upload-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
                    $("#favicon-upload-progress-status").text('0%');
                    showNotification('bg-black', 'Unknown server error', 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
                }
            })
    
    
        })
    
    
        $("#custom-favicon-selcet-btn").on('click', function(){
            $("#uploadFavicon").trigger('click');
        });
    
        $("#uploadFavicon").on('change', function(e){

            if(e.target.files[0].size > 1024 * 1024 * 2) {
                $("#uploadFavicon").val('')
                $("#custom-favicon-selcet-text").val('')
                showNotification('bg-black', 'Photo size must be within 2MB', 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
                return;
            }


            $("#custom-favicon-selcet-text").val(e.target.files[0].name);   
            $("#favicon-upload-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
            $("#favicon-upload-progress-status").text('0%');       
        })





        /**
            upload logo photo
         */
        var xhr = null;
        $("#custom-logo-upload-btn").on('click', function(){
    
            
            if(!$("#uploadLogo")[0].files.length) {
                showNotification('bg-black', 'Select photo first', 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
                return;
            }

            $("#logo-upload-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
            $("#logo-upload-progress-status").text('0%');
    
            var form_data = new FormData();
            form_data.append("photo", $("#uploadLogo")[0].files[0])
            form_data.append("_token", "{{csrf_token()}}")
    
            var url = "{{url('admin/settings/general/website/logo/save')}}";  
    
            console.log(url)

    
            xhr = $.ajax({
                url: url,
                cache: false,
                async: true,
                contentType: false,
                processData:false,
                data: form_data,                 
                type: 'POST',
                xhr: function () {
                    var xhr = $.ajaxSettings.xhr();
                    xhr.upload.onprogress = function (e) {
                        // For uploads
                        if (e.lengthComputable) {
    
                            var percentage = Math.floor((e.loaded / e.total) * 100)
    
                            $("#logo-upload-progressbar-div").find('.progress > .progress-bar').css('width', percentage+'%')
                            $("#logo-upload-progress-status").text(percentage+'%');
                            console.log(percentage);
                        }
                    };
                    return xhr;
                },
                success : function(data) {
                    if(!data.success) {
                        $("#uploadLogo").val('')
                        $("#custom-logo-selcet-text").val('')
                        $("#logo-upload-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
                        $("#logo-upload-progress-status").text('0%');
                        showNotification('bg-black', data.data.photo, 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
                    } else {
    
                        $("#logo-image").attr('src', '')
                        $("#logo-image").attr('src', data.data.logo_url)

                    }
                },
                error : function(){
                    $("#uploadLogo").val('')
                    $("#custom-logo-selcet-text").val('')
                    $("#logo-upload-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
                    $("#logo-upload-progress-status").text('0%');
                    showNotification('bg-black', 'Unknown server error', 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
                }
            })
    
    
        })
    
    
        $("#custom-logo-selcet-btn").on('click', function(){
            $("#uploadLogo").trigger('click');
        });
    
        $("#uploadLogo").on('change', function(e){

            if(e.target.files[0].size > 1024 * 1024 * 2) {
                $("#uploadLogo").val('')
                $("#custom-logo-selcet-text").val('')
                showNotification('bg-black', 'Photo size must be within 2MB', 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
                return;
            }


            $("#custom-logo-selcet-text").val(e.target.files[0].name);   
            $("#logo-upload-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
            $("#logo-upload-progress-status").text('0%');       
        })





    
    
        var general_website_save_url = '{{url("admin/settings/general/website/save")}}'
        $("#general-website-form").on('submit', function(event){
            event.preventDefault();
    
            var data = $(this).serializeArray();
            console.log(data)
    
            $.post(general_website_save_url, data, function(response){
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