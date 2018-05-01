@extends('admin.layouts.master')
@section('trips_active', 'active')
@section('trips_add_point_active', 'active')
@section('title', 'Add new trip point')
@section('top-header')
<style>
    .address-dot
    {
    color:green !important;
    }
    #map_canvas {
    height: 350px;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>TRIP POINT</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        ADD NEW TRIP POINT
                        <small>Choose point address, latitude and longitude etc.</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="add-new-point-form" action="{{route('admin.add-new-point')}}" method="POST">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <b> Address</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons address-dot">fiber_manual_record</i>
                                    </span>
                                    <div class="form-line">
                                        <input required name="address" type="text" class="form-control" placeholder="Ex: Hosur road, GB Playa, Near RNS motors, Garvebhavi Palya, Bengaluru, Karnataka 560068" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <b> Latitude</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input step="any" required name="latitude" type="number" class="form-control" placeholder="Ex: 12.8957554" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <b> Longitude</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input step="any"  required name="longitude" type="number" class="form-control" placeholder="Ex: 12.8957554" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <b> City</b>
                                <div class="input-group">
                                    <!-- <span class="input-group-addon">
                                        <i class="material-icons address-dot">fiber_manual_record</i>
                                        </span> -->
                                    <div class="form-line">
                                        <input required name="city" type="text" class="form-control" placeholder="Ex: Bangalore" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <b> Country</b>
                                <div class="input-group">
                                    <!--  <span class="input-group-addon">
                                        <i class="material-icons address-dot">fiber_manual_record</i>
                                        </span> -->
                                    <div class="form-line">
                                        <input required name="country" type="text" class="form-control" placeholder="Ex: India" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <b> Zip Code</b>
                                <div class="input-group">
                                    <!--  <span class="input-group-addon">
                                        <i class="material-icons address-dot">fiber_manual_record</i>
                                        </span> -->
                                    <div class="form-line">
                                        <input required name="zip_code" type="number" class="form-control" placeholder="Ex: 713213" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn bg-pink waves-effect" id="search-map-modal-btn">
                                <i class="material-icons">search</i>
                                <span>SEARCH</span>
                                </button>
                                <button type="submit" class="btn bg-pink waves-effect" id="add-point">
                                <i class="material-icons">save</i>
                                <span>ADD</span>
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
</div>
<!-- Modal -->
<div id="mapModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="border-radius: 2px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Search your trip point location</h4>
                <small>Enter Your location > Click find > Drag marker to choose location</small>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <input id="address" type="textbox" value="" placeholder = "Enter your location" class="form-control input-md">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-info" onclick="codeAddress()">Find</button>
                        <button type="button" class="btn btn-info" onclick="closeMapModal()">Done</button>
                    </div>
                </div>
                <br>                       
                <div id="map_canvas"></div>
            </div>
        </div>
    </div>
</div>
<!-- modal end -->
@endsection
@section('bottom')
<script src="https://maps.googleapis.com/maps/api/js?key={{$setting->get('google_maps_api_key')}}"></script>
<script>
    var geocoder;
    var map;
    var marker;
    var infowindow = new google.maps.InfoWindow({
    size: new google.maps.Size(150, 50)
    });
    
    function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var mapOptions = {
    zoom: 8,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
    google.maps.event.addListener(map, 'click', function() {
    infowindow.close();
    });
    }
    
    function geocodePosition(pos) {
    geocoder.geocode({
    latLng: pos
    }, function(responses) {
    
        console.log('responses', responses, pos)
        var results = responses;
        var street = "";
        var city = "";
        var state = "";
        var country = "";
        var zipcode = "";
        for (var i = 0; i < results.length; i++) {
    
            if (results[i].types[0] === "locality") {
                city = results[i].address_components[0].long_name;
                state = results[i].address_components[2].long_name;
    
            }
            if (results[i].types[0] === "postal_code" && zipcode == "") {
                zipcode = results[i].address_components[0].long_name;
    
            }
            if (results[i].types[0] === "country") {
                country = results[i].address_components[0].long_name;
    
            }
            if (results[i].types[0] === "route" && street == "") {
    
                for (var j = 0; j < 3; j++) {
                    if (j == 0) {
                        street = results[i].address_components[j].long_name;
                    } else {
                        street += ", " + results[i].address_components[j].long_name;
                    }
                }
    
            }
            if (results[i].types[0] === "street_address") {
                    for (var j = 0; j < 4; j++) {
                        if (j == 0) {
                            street = results[i].address_components[j].long_name;
                        } else {
                            street += ", " + results[i].address_components[j].long_name;
                        }
                    }
    
                }
            }
            if (zipcode == "") {
                if (typeof results[0].address_components[8] !== 'undefined') {
                    zipcode = results[0].address_components[8].long_name;
                }
            }
            if (country == "") {
                if (typeof results[0].address_components[7] !== 'undefined') {
                    country = results[0].address_components[7].long_name;
                }
            }
            if (state == "") {
                if (typeof results[0].address_components[6] !== 'undefined') {
                    state = results[0].address_components[6].long_name;
                }
            }
            if (city == "") {
                if (typeof results[0].address_components[5] !== 'undefined') {
                    city = results[0].address_components[5].long_name;
                }
            }
    
            var address = {
                "street": street,
                "city": city,
                "state": state,
                "country": country,
                "zipcode": zipcode,
            };
           
            fillAddress(address, pos)
            
      
    
    
    
    
        if (responses && responses.length > 0) {
        marker.formatted_address = responses[0].formatted_address;
        } else {
        marker.formatted_address = 'Cannot determine address at this location.';
        }
        infowindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
        infowindow.open(map, marker);
        });
    }
    
    function codeAddress() {
    var address = document.getElementById('address').value;
    geocoder.geocode({
    'address': address
    }, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
    map.setCenter(results[0].geometry.location);
    console.log('code address location', results[0].geometry.location)
    geocodePosition(results[0].geometry.location)
    if (marker) {
    marker.setMap(null);
    if (infowindow) infowindow.close();
    }
    marker = new google.maps.Marker({
    map: map,
    draggable: true,
    position: results[0].geometry.location
    });
    google.maps.event.addListener(marker, 'dragend', function() {
    geocodePosition(marker.getPosition());
    });
    google.maps.event.addListener(marker, 'click', function() {
    
    if (marker.formatted_address) {
    infowindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
    } else {
    infowindow.setContent(address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
    }
    infowindow.open(map, marker);
    });
    google.maps.event.trigger(marker, 'click');
    } else {
    alert('Geocode was not successful for the following reason: ' + status);
    }
    });
    }
    
    google.maps.event.addDomListener(window, "load", initialize);
</script>
<script>
    function clearAddress()
    {
        $("input[name='address']").val('');
        $("input[name='city']").val('');
        $("input[data='city']").val('');
        $("input[name='country']").val('');
        $("input[data='country']").val('');
        $("input[name='zip_code']").val('');
        $("input[data='zip_code']").val('');
        $("input[name='latitude']").val('');
        $("input[name='longitude']").val('');
    }
    
    function fillAddress(address, pos) 
    {
        console.log(address, pos)
        $("input[name='address']").val(address.street);
        $("input[name='city']").val(address.city);
        $("input[data='city']").val(address.city);
        $("input[name='country']").val(address.country);
        $("input[data='country']").val(address.country);
        $("input[name='zip_code']").val(address.zipcode);
        $("input[data='zip_code']").val(address.zipcode);
        $("input[name='latitude']").val(pos.lat);
        $("input[name='longitude']").val(pos.lng);
    }
    
    
    
    function closeMapModal()
    {
        $("#mapModal").modal('hide')
    }
    
    $(document).ready(function(){
        $("#mapModal").on('shown.bs.modal', function(){
            $("#address").val('')
            initialize();
        });
    
         $("#search-map-modal-btn").on('click', function(){
            $("#mapModal").modal('show')
            clearAddress();
            //initialize();
        })
    
    
    })
    
</script>
<script>
    $(document).ready(function(){
        
    
        $("#add-new-point-form").on('submit', function(event){
    
            event.preventDefault();
            
            var data = $("#add-new-point-form").serializeArray();
    
            console.log(data);
    
            $.post("{{route('admin.add-new-point')}}", data, function(response){
                if(response.success) {
                    clearAddress();
                    showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                } else {
    
                    for (var property in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(property)) {
                            showNotification('bg-red', response.data.errors[property], 'top', 'right', 'animated flipInX', 'animated flipOutX');
                            break;
                        }
                    }
    
                    showNotification('bg-red', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                }
            });
    
        })
    
    
         
    })
    
    
    
    
    
</script>
@endsection