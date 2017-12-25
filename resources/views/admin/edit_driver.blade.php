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
                    <div class="number count-to" data-from="0" data-to="1" data-speed="1000" data-fresh-interval="20">1</div>
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
                    <div class="number count-to" data-from="0" data-to="4" data-speed="1000" data-fresh-interval="20">4</div>
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
                    <div class="number count-to" data-from="0" data-to="2" data-speed="1000" data-fresh-interval="20">2</div>
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
                    <div class="number count-to" data-from="0" data-to="0" data-speed="1000" data-fresh-interval="20">0</div>
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
                    <div class="number count-to" data-from="0" data-to="0" data-speed="1000" data-fresh-interval="20">0</div>
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
                    <div class="number count-to" data-from="0" data-to="64.00" data-speed="1000" data-fresh-interval="20">64</div>
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
                        @if($driver->is_approved == 0)
                        <i class="material-icons col-green" data-toggle="tooltip" data-placement="left" title="Driver approved" style="vertical-align: sub;">done_all</i>
                        @else
                        <i class="material-icons col-grey" data-toggle="tooltip" data-placement="left" title="Driver not approved" style="vertical-align: sub;">done_all</i>
                        @endif
                    </h2>
                    <small>Here you can view, edit, update driver details</small>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-deep-orange">save</i> Save Profile</a></li>
                                @if($driver->is_approved == 0)
                                <li><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-green">verified_user</i> Approve</a></li>
                                @else
                                <li><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-red">verified_user</i> Disapprove</a></li>
                                @endif
                                <li><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-pink">file_upload</i> Change Picture</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div class="alert bg-green" style="display:none" id="profile-update-alert"></div>
                    <div class="row clearfix">
                        <div class="col-sm-4">
                            <img src="{{$driver->profilePhotoUrl()}}" class="img-responsive thumbnail" >
                            <i class="material-icons col-green" style="position: absolute;top: -10px;border-radius: 50%;background: white;" data-toggle="tooltip" data-placement="left" title="Avaiable">fiber_manual_record</i>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group">
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
                                    <select class="form-control show-tick">
                                        <option value="">-- Saervice Type --</option>
                                        @foreach($vehicleTypes as $type)
                                        <option @if($type['code'] == $driver->vehicle_type) selected @endif value="{{$type['code']}}">{{$type['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
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
@endsection
@section('bottom')
<!-- Dropzone Plugin Js -->
<script src="{{url('admin_assets/admin_bsb')}}/plugins/dropzone/dropzone.js"></script>
<!-- Light Gallery Plugin Js -->
<script src="{{url('admin_assets/admin_bsb')}}/plugins/light-gallery/js/lightgallery-all.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.js"></script>
<script>
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
            $.getScript("https://cdn.sobekrepository.org/includes/gmaps-markerwithlabel/1.9.1/gmaps-markerwithlabel-1.9.1.min.js", function(){
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