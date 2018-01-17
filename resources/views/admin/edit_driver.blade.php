@extends('admin.layouts.master')
@section('driver_list_active', 'active')
@section('driver_active', 'active')
@section('title', 'Edit '.$driver->fname)
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
<!-- Dropzone Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/dropzone/dropzone.css" rel="stylesheet">
<!-- Light Gallery Plugin Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/light-gallery/css/lightgallery.css" rel="stylesheet">
<style>
    .profile-photo 
    {
    width: 200px;
    height: 200px;
    /* border-radius: 50%; */
    border: 2px solid #fb483a;
    }
    .my-custom-class-for-label 
    {
    display:inline-block;
    text-align:center;
    border: 1px solid #eb3a44;
    border-radius: 5px;
    background: #fee1d7;
    text-align: center;
    line-height: 20px;
    font-weight: bold;
    font-size: 14px;
    color: green;
    }
    #aniimated-thumbnials > div > a > img 
    {
    margin-bottom : 0px;
    height: 200px;
    width: 100%;
    }
    #aniimated-thumbnials > div
    {
    text-align : center;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>EDIT DRIVER</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-pink hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">send</i>
                </div>
                <div class="content">
                    <div class="text">TOTAL REQUESTS</div>
                    <div class="number count-to" data-from="0" data-to="{{$totalDriverRequests}}" data-speed="1000" data-fresh-interval="20">1</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">cancel</i>
                </div>
                <div class="content">
                    <div class="text">USER CANCELED</div>
                    <div class="number count-to" data-from="0" data-to="{{$totalUserCanceledRequests}}" data-speed="1000" data-fresh-interval="20">4</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-purple hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">cancel</i>
                </div>
                <div class="content">
                    <div class="text">DRIVER CANCELED</div>
                    <div class="number count-to" data-from="0" data-to="{{$totalDriverCanceledRequests}}" data-speed="1000" data-fresh-interval="20">2</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-green hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">copyright</i>
                </div>
                <div class="content">
                    <div class="text">CASH PAYMENTS</div>
                    <div class="number">{{$totalCashPaymentEarned}}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-green hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">credit_card</i>
                </div>
                <div class="content">
                    <div class="text">PAYU PAYMENTS</div>
                    <div class="number">{{$totalPayuPaymentEarned}}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-light-blue hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">monetization_on</i>
                </div>
                <div class="content">
                    <div class="text">TOTAL REVENUE</div>
                    <div class="number">{{$totalCashPaymentEarned+$totalPayuPaymentEarned}}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-orange hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">stars</i>
                </div>
                <div class="content">
                    <div class="text">RATING</div>
                    <div class="number">{{$driver->rating}}</div>
                </div>
            </div>
        </div>
    </div>
    <!-- With Material Design Colors -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        <i class="material-icons" style="vertical-align:sub">account_circle</i>
                        DRIVER PROFILE
                        <i class="material-icons col-green" style="@if($driver->is_approved == 0) display:none @endif" id="driver-approve-icon" data-toggle="tooltip" data-placement="left" title="Driver approved" style="vertical-align: sub;">done_all</i>
                        <i class="material-icons col-grey" style="@if($driver->is_approved == 1) display:none @endif" id="driver-disapprove-icon" data-toggle="tooltip" data-placement="left" title="Driver not approved" style="vertical-align: sub;">done_all</i>
                    </h2>
                    <small>Here you can view, edit, update driver details</small>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li data-driver-id="{{$driver->id}}" id="profile-update-menu-item"><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons">save</i> Save profile</a></li>
                                <li data-driver-id="{{$driver->id}}" style="@if($driver->is_approved == 1) display:none !important @endif" data-is-approve = "1" class="approve-switch"><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-green">verified_user</i> Approve</a></li>
                                <li data-driver-id="{{$driver->id}}" style="@if($driver->is_approved == 0) display:none !important @endif" data-is-approve = "0" class="approve-switch"><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-red">verified_user</i> Disapprove</a></li>
                                <li id="profile-photo-upload-menu-item"><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons">file_upload</i> Change picture</a></li>
                                <li id="password-reset-menu-item"><a href="javascript:void(0);" class="waves-effect waves-block"><i class="material-icons">https</i> Reset password</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div class="alert bg-pink" style="display:none" id="profile-update-alert"></div>
                    <div class="row clearfix">
                        <div class="col-sm-4">
                            <img id="profile-img" src="{{$driver->profilePhotoUrl()}}" class="img-responsive thumbnail" >
                            <i class="material-icons col-green" style="position: absolute;top: -10px;border-radius: 50%;background: white;" data-toggle="tooltip" data-placement="left" title="Avaiable">fiber_manual_record</i>
                        </div>
                        <form id="driver-profile-form">
                            {!! csrf_field() !!}
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">perm_identity</i>
                                    </span>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Frist Name" name="first_name" value="{{$driver->fname}}" onkeyup="this.value=this.value.charAt(0).toUpperCase() + this.value.slice(1)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Last Name"  name="last_name" value="{{$driver->lname}}" onkeyup="this.value=this.value.charAt(0).toUpperCase() + this.value.slice(1)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">email</i>
                                    </span>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Email" name="email" value="{{$driver->email}}">
                                    </div>
                                    <span class="input-group-addon">
                                    @if($driver->is_email_verified)
                                    <i class="material-icons col-green"  data-toggle="tooltip" data-placement="left" title="Email verified">done_all</i>
                                    @else 
                                    <i class="material-icons col-grey"  data-toggle="tooltip" data-placement="left" title="Email not verified">done_all</i>
                                    @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">phone</i>
                                    </span>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="mobile_number" value="{{$driver->country_code}}-{{$driver->mobile_number}}"placeholder="Mobile Number" data-toggle="tooltip" data-placement="left" title="+[countrycode][-][mobileno] eg: +91-9093034785">
                                    </div>
                                    <span class="input-group-addon">
                                    @if($driver->is_mobile_number_verified)
                                    <i class="material-icons col-green"  data-toggle="tooltip" data-placement="left" title="Mobile number verified">done_all</i>
                                    @else 
                                    <i class="material-icons col-grey"  data-toggle="tooltip" data-placement="left" title="Mobile number not verified">done_all</i>
                                    @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">view_headline</i>
                                    </span>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Vehicle Number" name="vehicle_number" value="{{$driver->vehicle_number}}" onkeyup="this.value=this.value.toUpperCase()">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">directions_bus</i>    
                                    </span>
                                    <div class="form-line">
                                        <select class="form-control show-tick" name="service_type">
                                            <option value="">-- Saervice Type --</option>
                                            @foreach($vehicleTypes as $type)
                                            <option @if($type['code'] == $driver->vehicle_type) selected @endif value="{{$type['code']}}">{{$type['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
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
                        <i class="material-icons" style="vertical-align: sub;">room</i>
                        DRIVER LIVE LOCATION TRACKING
                        <small>Driver realtime location updates. Shows driver's last updated location.</small>
                    </h2>
                </div>
                <div class="body">
                    <span class="label bg-deep-orange">Latidue : <span id="latitude"></span> | Longitude : <span id="longitude"></span></span>
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div id="map" style="height:350px"></div>
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
                        <i class="material-icons" style="vertical-align:sub">attach_file</i>
                        DRIVER UPLOADED DOCUMENTS
                        <small>Here you can see all driver documents upload at registration time</small>
                    </h2>
                </div>
                <div class="body">
                    <div id="aniimated-thumbnials" class="list-unstyled row clearfix">
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['vehicle_rc_photo_url']}}" data-sub-html="Vehicle Registration Photo(RC)">
                            <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['vehicle_rc_photo_url']}}">
                            </a>
                            <small>Vehicle Registration photo</small>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['vehicle_contract_permit_photo_url']}}" data-sub-html="Vehicle Contract Permit Photo">
                            <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['vehicle_contract_permit_photo_url']}}">
                            </a>
                            <small>Vehicle Contract Permit photo</small>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['vehicle_insurance_certificate_photo_url']}}" data-sub-html="Vehicle Insurance Certificate Photo">
                            <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['vehicle_insurance_certificate_photo_url']}}">
                            </a>
                            <small>Vehicle Insurance Certificate photo</small>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['vehicle_fitness_certificate_photo_url']}}" data-sub-html="Vehicle Fitness Certificate Photo">
                            <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['vehicle_fitness_certificate_photo_url']}}">
                            </a>
                            <small>Vehicle Fitness Certificate photo</small>
                        </div>
                        @if(isset($driver->vehicle_lease_agreement_photo_name) && $driver->vehicle_lease_agreement_photo_name != '')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['vehicle_lease_agreement_photo_url']}}" data-sub-html="Vehicle Lease Agreement Photo">
                            <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['vehicle_lease_agreement_photo_url']}}">
                            </a>
                            <small>Vehicle Lease Agreement photo</small>
                        </div>
                        @endif
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['vehicle_photo_1_url']}}" data-sub-html="Vehicle Photo No. 1">
                            <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['vehicle_photo_1_url']}}">
                            </a>
                            <small>Vehicle photo No. 1</small>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['vehicle_photo_2_url']}}" data-sub-html="Vehicle Photo No. 2">
                            <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['vehicle_photo_2_url']}}">
                            </a>
                            <small>Vehicle photo No. 2</small>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['vehicle_photo_3_url']}}" data-sub-html="Vehicle Photo No. 3">
                            <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['vehicle_photo_3_url']}}">
                            </a>
                            <small>Vehicle photo No. 3</small>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['vehicle_photo_4_url']}}" data-sub-html="Vehicle Photo No. 4">
                            <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['vehicle_photo_4_url']}}">
                            </a>
                            <small>Vehicle photo No. 4</small>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['vehicle_commercial_driving_license_photo_url']}}" data-sub-html="Driver Commercial Driving Licence(DL)">
                            <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['vehicle_commercial_driving_license_photo_url']}}">
                            </a>
                            <small>Driver Driving License Photo</small>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['vehicle_police_verification_certificate_photo_url']}}" data-sub-html="Vehicle Police Verifiecation Certificate">
                            <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['vehicle_police_verification_certificate_photo_url']}}">
                            </a>
                            <small>Vehicle Police Verification Certificate Photo</small>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['bank_passbook_photo_url']}}" data-sub-html="Driver Bank Passbook">
                            <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['bank_passbook_photo_url']}}">
                            </a>
                            <small>Driver Bank Passbook Photo</small>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['aadhaar_card_photo_url']}}" data-sub-html="Driver Id Card">
                            <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['aadhaar_card_photo_url']}}">
                            </a>
                            <small>Driver ID Proof Photo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal fade" id="profile-photo-upload-modal" tabindex="-1" role="dialog" data-backdrop="static"
   data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">UPLOAD DRIVER PHOTO</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="upload-photo-form" method="POST" enctype="multipart/form-data">
                    <div class="row clearfix">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="input-group input-group-lg">
                                <span class="input-group-addon">
                                <i class="material-icons col-pink" id="custom-photo-selcet-btn" style="cursor:pointer" data-toggle="tooltip" data-placement="left" title="Click icon and select photo">add_a_photo</i>
                                </span>
                                <div class="form-line">
                                    <input id="uploadPhoto" type="file" style="display:none"/>
                                    <input type="text" id="custom-photo-selcet-text" class="form-control" placeholder="Click icon and select photo" disabled>
                                </div>
                                <span class="input-group-addon">
                                <i class="material-icons col-pink" id="custom-photo-upload-btn" style="cursor:pointer" data-toggle="tooltip" data-placement="left" title="Upload photo">file_upload</i>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="" id="photo-upload-progressbar-div">
                    <div class="progress">
                        <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                            <span class="sr-only"></span>
                        </div>
                    </div>
                    <small>Photo upload progress status: <span id="photo-upload-progress-status">0%</span></small>
                </div>
            </div>
            <div class="modal-footer">               
                <button type="button" class="btn btn-link waves-effect" id="profile-photo-cancel">CANCEL</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('bottom')
<!-- Dropzone Plugin Js -->
<script src="{{url('admin_assets/admin_bsb')}}/plugins/dropzone/dropzone.js"></script>
<!-- Light Gallery Plugin Js -->
<script src="{{url('admin_assets/admin_bsb')}}/plugins/light-gallery/js/lightgallery-all.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.js"></script>
<script>
    var driverId = {{$driver->id}};
    var csrf_token = "{{csrf_token()}}";
    $(function () {
    
    
        /**
         * password reset
         */
        $("#password-reset-menu-item").on('click', function(){
    
            swal({
                title: "Are you really want to reset password?",
                text: "New password will be sent to driver via emal and sms",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, reset",
                cancelButtonText: "No, cancel please",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    
                    var url = "{{url('admin/drivers')}}/"+driverId+'/reset-password';
    
                    $.post(url, {_token:csrf_token}, function(response){
                        console.log(response)
                        setTimeout(function(){
                            swal("Driver password reset", "<<New password also sent via email and sms>>", "success");
                        }, 1000)
                                    
                    }).fail(function(response) {
                        showNotification('bg-black', 'Unknown server error.', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    });
            
                    swal.close();
    
    
                } 
    
            });
    
    
        });
    
    
    
    
    
        /**
         * profile update menu item
         */       
        $("#profile-update-menu-item").on('click', function(){
    
            var driverId = $(this).data('driver-id');
            var url = "{{url('admin/drivers')}}/"+driverId+'/update';
            var data = $("#driver-profile-form").serializeArray();
    
            console.log(url, data)
    
            $("#profile-update-alert").slideUp()
    
            $.post(url, data, function(response){
                console.log(response)
                if(response.success) {
                    $("#profile-update-alert").slideUp()
                    showNotification('bg-black', 'Driver profile updated successfully.', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    return;
                } 
    
                //failed 
                $("#profile-update-alert").text(response.data[Object.keys(response.data)[0]]).slideDown()
    
            })
            .fail(function(response) {
                showNotification('bg-black', 'Unknown server error. Failed to approve driver', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            });
    
        });
    
    
    
    
    
        /**
            upload profile photo
         */
        var xhr = null;
        $("#profile-photo-cancel").on('click', function(){

            $("#profile-photo-upload-modal").modal('hide')

            try {
                xhr.abort();
            } catch(e){}
            
        })
        $("#custom-photo-upload-btn").on('click', function(){
    
            
            if(!$("#uploadPhoto")[0].files.length) {
                showNotification('bg-black', 'Select photo first', 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
                return;
            }

            $("#photo-upload-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
            $("#photo-upload-progress-status").text('0%');
    
            var form_data = new FormData();
            form_data.append("photo", $("#uploadPhoto")[0].files[0])
            form_data.append("_token", "{{csrf_token()}}")
    
            var url = "{{url('admin/drivers')}}/{{$driver->id}}/change-photo";  
    
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
    
                            $("#photo-upload-progressbar-div").find('.progress > .progress-bar').css('width', percentage+'%')
                            $("#photo-upload-progress-status").text(percentage+'%');
                            console.log(percentage);
                        }
                    };
                    return xhr;
                },
                success : function(data) {
                    if(!data.success) {
                        $("#uploadPhoto").val('')
                        $("#custom-photo-selcet-text").val('')
                        $("#photo-upload-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
                        $("#photo-upload-progress-status").text('0%');
                        showNotification('bg-black', data.data.photo, 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
                    } else {
    
                        $("#profile-img").attr('src', '')
                        $("#profile-img").attr('src', data.data.profile_photo_url)
    
                        setTimeout(function(){
                            $("#profile-photo-upload-modal").modal('hide')
                            swal("Driver profile photo changed successfully", "", "success");
                        }, 1000)
                    }
                },
                error : function(){
                    $("#uploadPhoto").val('')
                    $("#custom-photo-selcet-text").val('')
                    $("#photo-upload-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
                    $("#photo-upload-progress-status").text('0%');
                    showNotification('bg-black', 'Unknown server error', 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
                }
            })
    
    
        })
    
    
        $("#profile-photo-upload-menu-item").on('click', function(){
            $("#uploadPhoto").val('')
            $("#custom-photo-selcet-text").val('')
            $("#photo-upload-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
            $("#photo-upload-progress-status").text('0%');
            $("#profile-photo-upload-modal").modal('show')
    
        });
    
        $("#custom-photo-selcet-btn").on('click', function(){
            $("#uploadPhoto").trigger('click');
        });
    
        $("#uploadPhoto").on('change', function(e){

            if(e.target.files[0].size > 1024 * 1024 * 2) {
                $("#uploadPhoto").val('')
                $("#custom-photo-selcet-text").val('')
                showNotification('bg-black', 'Photo size must be within 2MB', 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
                return;
            }


            $("#custom-photo-selcet-text").val(e.target.files[0].name);   
            $("#photo-upload-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
            $("#photo-upload-progress-status").text('0%');       
        })
    
    
    
        
        $(".approve-switch").on('click', function(){
    
            var csrf_token = "{{csrf_token()}}";
            var driverId = $(this).data('driver-id');
            var isApprove = $(this).data('is-approve')
            var url = "{{url('admin/drivers')}}/"+driverId+'/approve/'+isApprove;
            var curElem = this;
            console.log(url)
    
            if(isApprove) {        
            
            $.post(url, {_token:csrf_token}, function(response){
                console.log(response)
                if(response.success) {
                    
                    showNotification('bg-black', 'Driver approved successfully', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    $("#driver-approve-icon").show();
                    $("#driver-disapprove-icon").hide();
                    $(".approve-switch[data-is-approve='1']").attr('style', 'display:none !important')
                    $(".approve-switch[data-is-approve='0']").show();
                }                 
            }).fail(function(response) {
                $(curElem).prop('checked', false);
               
                showNotification('bg-black', 'Unknown server error. Failed to approve driver', 'top', 'right', 'animated flipInX', 'animated flipOutX');
               
            });
            
        } 
        //disapprove driver show alert message first
        else {
    
            swal({
                title: "Are you really want to disappove this driver",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, disapprve",
                cancelButtonText: "No, cancel please",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    
                    swal({
                        title: "Email Notification",
                        text: "Driver will get email notificaiton",
                        type: "input",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        animation: "slide-from-top",
                        inputPlaceholder: "Type reason for disapprove"
                    }, function (inputValue) {
                        if (inputValue === false) {
                            $(curElem).prop('checked', true);
                            return false;
                        } 
    
                        if (inputValue === "") {
                            swal.showInputError("You must enter reason"); return false
                        }
                        
                        $.post(url, {_token:csrf_token, message:inputValue}, function(response){
                            console.log(response)
                            if(response.success) {
                                showNotification('bg-black', 'Driver disapproved successfully', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                                $("#driver-approve-icon").hide();
                                $("#driver-disapprove-icon").show();
                                $(".approve-switch[data-is-approve='1']").show();
                                $(".approve-switch[data-is-approve='0']").attr('style', 'display:none !important')
                            }
                                        
                        }).fail(function(response) {
                            $(curElem).prop('checked', true);
                            showNotification('bg-black', 'Unknown server error. Failed to update driver', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                        });
                        console.log('disapprove clled')
                        swal.close();
    
                    });
                } else {
                    $(curElem).prop('checked', true);
                }  
    
    
            });
    
    
    
        }
    
    
    
        });
    
    
    });
    
    
    
    
    
    
    
    var socket = null;
    socket = io('{{config("socket_server.socket_url")}}?server_key={{config("socket_server.server_internal_communication_key")}}');
    socket.on('connect', function(){
        console.log('socket connected')
    });
    
    socket.on('driver_location_updated', function(data){
    
        if(data.driver_id != {{$driver->id}} && (!map || map == undefined) ) {
            return;
        }
    
        console.log('driver_location_updated',data)
    
        latlng = new google.maps.LatLng(data.latitude, data.longitude);
        latElem.text(data.latitude);
        lngElem.text(data.longitude);
        map.setCenter(latlng);
        marker.setPosition(latlng);
    
    });
    
    
    var currLatitude = {{$driver->latitude}};
    var currLongitude = {{$driver->longitude}};
    
    function initMapScript()
    {
        
        $.getScript("https://maps.googleapis.com/maps/api/js?key={{$google_maps_api_key}}&libraries=places", function(){
            $.getScript("{{url('admin_assets/js')}}/gmaps-markerwithlabel-1.9.1.min.js", function(){
                initMap();
            })
            
        });
        
    }
    
    
    var map;
    var infowindow;
    var marker = null;
    var latlng = null;
    var latElem;
    var lngElem;
    function initMap()
    {
        latlng = new google.maps.LatLng(currLatitude, currLongitude);
    
        latElem = $("#latitude");
        latElem.text(currLatitude);
        lngElem = $("#longitude");
        lngElem.text(currLongitude);
    
        map = new google.maps.Map(document.getElementById('map'), {
            center: latlng,
            zoom: 12,
            mapTypeId: 'roadmap'
        });
    
        infowindow = new google.maps.InfoWindow();
                
        var icon = {
            url: '{{url('admin_assets/car.png')}}',
            scaledSize: new google.maps.Size(35, 35),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(32,65)
        };
        
        marker = new MarkerWithLabel({
            icon : icon,
            animation: google.maps.Animation.DROP,
            position: latlng,
            map: map,
            title: 'Hello World!',
            labelContent: '{{$driver->fname.' '.$driver->lname}}',
            labelAnchor: new google.maps.Point(40,80),
            labelClass: "my-custom-class-for-label", // your desired CSS class
            labelInBackground: true
        });
        
        //var bounds = new google.maps.LatLngBounds();
        //bounds.extend(marker.getPosition());
        marker.setMap(map);
        //map.fitBounds(bounds);
    
    }
    
    
    $(function () {
    
        initMapScript();
    
        $('#aniimated-thumbnials').lightGallery({
            thumbnail: true,
            selector: 'a'
        });
    });
</script>
@endsection