@extends('admin.layouts.master')
@section('trips_active', 'active')
@section('trips_new_route_add_active', 'active')
@section('title', 'Add new route')
@section('top-header')
<style>
    .address-dot
    {
    color:green !important;
    }
    .dd 
    {
    float : none;
    }
    .dd .card
    {
    margin-bottom : 0;
    }
    .dd .dd3-content
    {
    padding:0px;
    }
    .dd .dd3-handle
    {
    z-index : 1;
    }
    .trip-point-div
    {
    border: 1px solid rgba(204, 204, 204, 0.35);
    padding-top: 20px;
    margin-left: 0px;
    margin-right: 0px;
    position:relative;
    }
    .point-bottom-arrow
    {
    margin: 0px !important; 
    }
    .point-bottom-arrow > i 
    {
        position: absolute;
        top: -9px;
        color: green;
        background: #0000000d;
    }
    .trip-point-div:last-child
    {
        display:none !important;
    }
    .point-title
    {
        display: inline-block;
        position: absolute;
        left: 0px;
        padding: 5px;
        background: black;
        color: white;
        top: -10px;
        font-size: 10px;
    }
    .point-delete-btn
    {
        color: red;
        right: 0px;
        top: 0px;
        position: absolute;
        background: #00000012;
        cursor: pointer;
        padding: 5px;
    }
    .location-search-btn
    {
        padding: 5px;
        right: 34px;
        top: 0px;
        position: absolute;
        background: #00000012;
        cursor: pointer;
    }
    #map_canvas {
    height: 350px;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>ADD ROUTE</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        ADD NEW TRIP ROUTE
                        <small>Choose source, destination and intermediate pickup points. First and last point will be the source and destination point. Enter location name and latlong points.</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="add-new-route-form" action="{{route('admin.add-new-route')}}" method="POST">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <b>Route Name</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">text_format</i>
                                    </span>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: Pune-Mumbai Express" value="{{ old('name') }}" name="name" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <label> Trip Pionts</label>
                                <small>(Add trip points in order)</small>
                                <button title="Add new intermediate point"  id="add-new-point" type="button" class="btn bg-green btn-xs waves-effect">
                                    +Add Point
                                </button>
                                <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Enter details of each intermediate points. Calculate and enter name, latitude, longitude. First and second point will be source and destination.">help_outline</i>
                            </div>
                        </div>
                        <div class="row clearfix trip-point-div" data-point-order="1">
                            <i class="material-icons location-search-btn" title="Search location">search</i>
                            <i class="material-icons point-delete-btn" title="Remove point">delete_forever</i>
                            <div class="point-title">Point 1</div>
                            <div class="col-md-6">
                                <b> Address</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons address-dot">fiber_manual_record</i>
                                    </span>
                                    <div class="form-line">
                                        <input  required name="points[0][address]" type="text" class="form-control" placeholder="Ex: Hosur road, GB Playa, Near RNS motors, Garvebhavi Palya, Bengaluru, Karnataka 560068" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b> Latitude</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input step="any"  required name="points[0][latitude]" type="number" class="form-control" placeholder="Ex: 12.8957554" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b> Longitude</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input step="any"  required name="points[0][longitude]" type="number" class="form-control" placeholder="Ex: 12.8957554" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <b>City</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input required name="points[0][city]" type="text" class="form-control" placeholder="Ex: Bangalore" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <b>Country</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input required name="points[0][country]" type="text" class="form-control" placeholder="Ex: India" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <b>Zip-Code</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input required name="points[0][zip_code]" type="text" class="form-control" placeholder="Ex: 713234" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-center point-bottom-arrow">
                                <i class="material-icons">arrow_downward</i>
                            </div>
                        </div>
                        <div class="row clearfix trip-point-div" data-point-order="2">
                            <i class="material-icons location-search-btn" title="Search location">search</i>
                            <i class="material-icons point-delete-btn" title="Remove point">delete_forever</i>
                            <div class="point-title">Point 1</div>
                            <div class="col-md-6">
                                <b> Address</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons address-dot">fiber_manual_record</i>
                                    </span>
                                    <div class="form-line">
                                        <input  required name="points[1][address]" type="text" class="form-control" placeholder="Ex: Hosur road, GB Playa, Near RNS motors, Garvebhavi Palya, Bengaluru, Karnataka 560068" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b> Latitude</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input step="any"  required name="points[1][latitude]" type="number" class="form-control" placeholder="Ex: 12.8957554" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b> Longitude</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input step="any"  required name="points[1][longitude]" type="number" class="form-control" placeholder="Ex: 12.8957554" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <b>City</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input required name="points[1][city]" type="text" class="form-control" placeholder="Ex: Bangalore" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <b>Country</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input required name="points[1][country]" type="text" class="form-control" placeholder="Ex: India" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <b>Zip-Code</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input required name="points[1][zip_code]" type="text" class="form-control" placeholder="Ex: 713234" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-center point-bottom-arrow">
                                <i class="material-icons">arrow_downward</i>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn bg-pink waves-effect" id="add-route">
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
<!-- map modal -->
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
<!-- map modal end -->
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

        currentLocationSearchPointDiv.find('input[name$="[address]"]').val('');
        currentLocationSearchPointDiv.find('input[name$="[latitude]"]').val('');
        currentLocationSearchPointDiv.find('input[name$="[longitude]"]').val('');
        currentLocationSearchPointDiv.find('input[name$="[city]"]').val('');
        currentLocationSearchPointDiv.find('input[name$="[country]"]').val('');
        currentLocationSearchPointDiv.find('input[name$="[zip_code]"]').val('');       
    }
    
    function fillAddress(address, pos) 
    {
        console.log(address, pos)
        currentLocationSearchPointDiv.find('input[name$="[address]"]').val(address.street);
        currentLocationSearchPointDiv.find('input[name$="[latitude]"]').val(pos.lat);
        currentLocationSearchPointDiv.find('input[name$="[longitude]"]').val(pos.lng);
        currentLocationSearchPointDiv.find('input[name$="[city]"]').val(address.city);
        currentLocationSearchPointDiv.find('input[name$="[country]"]').val(address.country);
        currentLocationSearchPointDiv.find('input[name$="[zip_code]"]').val(address.zipcode);
    }
    
    
    
    function closeMapModal()
    {
        $("#mapModal").modal('hide')
    }

    let currentLocationSearchPointDivId = 0;
    let currentLocationSearchPointDiv = null;
    
    $(document).ready(function(){
        $("#mapModal").on('shown.bs.modal', function(){
            $("#address").val('')
            initialize();
        });
    
        $("body").on('click', '.location-search-btn',function(){
            var elems = $(".trip-point-div");
            var pointdiv = $(this).parent();
            currentLocationSearchPointDivId = pointdiv.data('point-order')
            console.log(currentLocationSearchPointDivId)
            currentLocationSearchPointDiv = pointdiv;
            $("#mapModal").modal('show')
            clearAddress();
            //initialize();
        })
    
    
    })
    
</script>
<script>
    $(document).ready(function(){


        $("body").on('click', '.point-delete-btn', function(){
            var elems = $(".trip-point-div");
            var pointdiv = $(this).parent();
            var currentOrder = pointdiv.data('point-order')

            if(currentOrder == 1 || currentOrder == elems.length) {
                showNotification('bg-black', 'You are allowed to delete source and destination point', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                return;
            }


            pointdiv.fadeOut();
            pointdiv.remove();

            arrangePionts();
            hideLastBottomArrow();

        });



        
        hideLastBottomArrow();
    
        $("#add-new-route-form").on('submit', function(event){
            
            event.preventDefault();
            
            var data = $("#add-new-route-form").serializeArray();

            console.log(data);

            $.post("{{route('admin.add-new-route')}}", data, function(response){
                if(response.success) {
                    showNotification('bg-black', response.text+'<br><a style="color:white;text-decoration: underline;" href="{{route('admin.show-all-routes')}}">click to see all routes</a>', 'top', 'right', 'animated flipInX', 'animated flipOutX');
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
    
    
        $("#add-new-point").on('click', function(){
            
            var elems = $(".trip-point-div");
            console.log(elems)
            var clonedElem = $(elems[1]).clone();
            /* clonedElem.removeAttr('id')
            */
            clonedElem.hide(); 
            $(elems[elems.length - 2]).after(clonedElem)
            clonedElem.fadeIn();
            clonedElem.css('background-color', '#ff000012')
            setTimeout(function(){
                clonedElem.css('background-color', 'inherit')
            }, 500)
    
            arrangePionts();

            hideLastBottomArrow();

            showNotification('bg-black', 'New intermediate point added', 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
    
        })
    
     
    })


    function hideLastBottomArrow()
    {
        var elems = $(".point-bottom-arrow");
        elems.each(function(index, item){
            if(index == elems.length -1 ) {
                $(item).hide();
                return;
            }

            $(item).show();
        })  
    }

    
    function arrangePionts()
    {
        var tripPointElements = $(".trip-point-div");
        var pointsLength = tripPointElements.length;
    
        var addressElem;
        var latitudeElem;
        var longitudeElem;
        var distanceElem;
        var timeElem;
        var elem;
    
        for(var i = 1; i <= pointsLength; i++) {
    
            console.log(tripPointElements[i-1]);
            var elem = $(tripPointElements[i-1]);
            
            elem.attr('data-point-order', i);
            
            elem.find('.point-title').text('Point ' + i)
    
            var addressElem = elem.find('input[name$="[address]"]');
            var latitudeElem = elem.find('input[name$="[latitude]"]');
            var longitudeElem = elem.find('input[name$="[longitude]"]');
            var cityElem = elem.find('input[name$="[city]"]');
            var countryElem = elem.find('input[name$="[country]"]');
            var zipCodeElem = elem.find('input[name$="[zip_code]"]');
    
            if(addressElem.length) {
                addressElem.attr('name', 'points['+(i-1)+'][address]')
            }
    
            if(latitudeElem.length) {
                latitudeElem.attr('name', 'points['+(i-1)+'][latitude]')
            }
    
            if(longitudeElem.length) {
                longitudeElem.attr('name', 'points['+(i-1)+'][longitude]')
            }
    
            if(cityElem.length) {
                cityElem.attr('name', 'points['+(i-1)+'][city]')
            }
    
            if(countryElem.length) {
                countryElem.attr('name', 'points['+(i-1)+'][country]')
            }
    
            if(zipCodeElem.length) {
                zipCodeElem.attr('name', 'points['+(i-1)+'][zip_code]')
            }
    
        }
        
    
    }
    
</script>
@endsection