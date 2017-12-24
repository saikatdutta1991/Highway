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
</style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>EDIT DRIVER</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="header">
            <h2>
                {{strtoupper($driver->fname.' '.$driver->lname)}} 
            </h2>
            <small>Here you can view, edit, update driver details</small>
           
            <ul class="header-dropdown m-r--5">
                                <li class="dropdown">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <ul class="dropdown-menu pull-right">
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">Action</a></li>
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">Another action</a></li>
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">Something else here</a></li>
                                    </ul>
                                </li>
                            </ul>

        </div>
    


        <div class="body">
            <div class="alert bg-green">
                                Lorem ipsum dolor sit amet, id fugit tollit pro, illud nostrud aliquando ad est, quo esse dolorum id
                            </div>
            <div class="row clearfix">

                <div class="col-sm-4">
                    <img src="http://icons.iconarchive.com/icons/paomedia/small-n-flat/512/user-male-icon.png" class="img-responsive thumbnail" >
                    <i class="material-icons col-green" style="position: absolute;top: -10px;border-radius: 50%;background: white;" data-toggle="tooltip" data-placement="left" title="Avaiable">fiber_manual_record</i>
                </div>
                <div class="col-sm-4">

                    <div class="input-group">
                        <div class="form-line">
                            <input type="text" class="form-control" placeholder="Last Name">
                        </div>
                    </div>
                    
                </div>
                <div class="col-sm-4">

                    <div class="input-group">
                        <div class="form-line">
                            <input type="text" class="form-control" placeholder="Last Name">
                        </div>
                    </div>
        
                    
                </div>

                <div class="col-sm-4">

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" placeholder="Email">
                        </div>
                        <span class="input-group-addon">
                            <i class="material-icons col-green"  data-toggle="tooltip" data-placement="left" title="Email verified">done_all</i>
                        </span>
                    </div>
                    
                </div>

                <div class="col-sm-4">
                    
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">phone</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" placeholder="Mobile Number">
                        </div>
                        <span class="input-group-addon">
                            <i class="material-icons col-green"  data-toggle="tooltip" data-placement="left" title="Mobile number verified">done_all</i>
                        </span>
                        
                    </div>
                    
                </div>

                <div class="col-sm-4">
                    
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">stars</i> 
                        </span>
                        <div class="form-line disabled">
                            <input type="text" class="form-control" placeholder="Rating" disabled>
                        </div>
                       
                    </div>
                    
                </div>

                <div class="col-sm-4">
                    
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">view_headline</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" placeholder="Vehicle Number">
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
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="30">30</option>
                                        <option value="40">40</option>
                                        <option value="50">50</option>
                                    </select>
                        </div>
                       
                    </div>
                     
                    
                </div>

                <div class="col-sm-4">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">verified_user</i>  
                        </span>
                        <div class="form-line" style="border-bottom: none;">
                            <div class="switch">
                                <label style="vertical-align: sub;"><input type="checkbox"><span class="lever switch-col-deep-orange"></span></label>
                            </div>
                        </div>
                       
                    </div>
                   
                    
                    
                </div>

                <div class="col-sm-8">
                    
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">camera_alt</i>    
                        </span>
                        <div class="form-line">
                            <input type="file" style="z-index:1" class="form-control" placeholder="Vehicle Number">
                        </div>
                        <span class="input-group-addon">
                            <i class="material-icons col-red">cancel</i>
                        </span>
                       
                    </div>
                     
                    
                </div>

                


            
               

            </div>
            

           
        </div>

        


    </div>


    <div class="card">
                <div class="header">
                    <h2>
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


    <div class="card">
                <div class="header">
                    <h2>
                        GALLERY
                        <small>All pictures taken from <a href="https://unsplash.com/" target="_blank">unsplash.com</a></small>
                    </h2>
                </div>
                <div class="body">
                    <div id="aniimated-thumbnials" class="list-unstyled row clearfix">
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                            <a href="{{$driver->getExtraPhotosUrl()['vehicle_rc_photo_url']}}" data-sub-html="Demo Description">
                                <img class="img-responsive thumbnail" src="{{$driver->getExtraPhotosUrl()['vehicle_rc_photo_url']}}">
                            </a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- #END# With Material Design Colors -->
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