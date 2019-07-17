@extends('admin.layouts.master')
@section('title', 'Fake Locations')
@section('driver_active', 'active')
@section('fake_locations_active', 'active')
@section('top-header')
<style>
.iframe-container{
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* Ratio 16:9 ( 100%/16*9 = 56.25% ) */
}
.iframe-container > *{
    display: block;
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    margin: 0;
    padding: 0;
    height: 100%;
    width: 100%;
}
    .service_icon_box {
        background: #00000038;
        padding: 10px;
        margin: 1px;
        width: 70px;
        height: 70px;
        text-align: center;
    }

    .service_icon_text {
        color: black;
        font-weight: 700;
        font-size: 10px;
    }

    .input-group-addon .material-icons {
        font-size: xx-large !important;
        cursor:pointer;
    }   
    .input-group-addon {
        vertical-align: text-bottom;
    }
    #autocomplete {
        border: 1px solid #0000002b;
        padding: 15px;
        border-radius: 50px;
    }
    /* .enable-btn {
        display: inline-block;
        border-radius: 23px;
        background: #81818159;
        color: black;
        margin-top: 15px;
        position: relative;
        padding: 5px 5px 5px 10px;
        box-shadow: inset 0 0 10px #00000033;
    } */
    .map-loader {
        font-family: sans-serif;
        font-weight: 500;
        font-size: 20px;
        text-align: center;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>FAKE LOCATIONS</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    ADD OR REMOVE FAKE LOCATIONS FOR NEARBY DRIVERS
                    <small>Add + button to add new marker, click on marker, then remove button to delete marker. Click save button to save locations.</small>
                    <div class="switch enable-btn">
                        <label>Enable or Disable<input type="checkbox" @if($fake_location_enabled == 'on') checked @endif id="enable-input"><span class="lever switch-col-pink"></span></label>
                    </div>
                </h2>
            </div>
            <div class="body">
                <div class="row clearfix">
                    <div class="col-sm-12" style="margin-bottom:0">
                        <div class="input-group">
                            <span class="input-group-addon">
                            <i class="material-icons search-addon col-cyan">search</i>
                            </span>
                            <div class="form">
                                <input type="text" class="form-control" autofocus id="autocomplete" placeholder="Type & Search Location">
                            </div>
                            <span class="input-group-addon">
                            <i class="material-icons add_new_marker col-red" title="Add new marker">add</i>
                            <i class="material-icons save_locations_btn col-green" title="Save">save</i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row clearfix">
                    <div id="map" class="iframe-container" ><div class="map-loader">Loading map..</div></div>
                </div>
                <div class="" style="display: flex;">
                    @foreach($services as $service)
                    <div class="service_icon_box">
                        <img data-service="{{$service->code}}" class="service_icon">
                        <br><span class="service_icon_text">{{$service->name}}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- #END# With Material Design Colors -->
    </div>
</div>
@endsection
@section('bottom')
<script src="https://maps.googleapis.com/maps/api/js?key={{$google_maps_api_key}}&libraries=places"></script>
<script>    

    var icons = {
        AUTO : '{{asset("images/auto-ricksaw.png")}}',
        MICRO : '{{asset("images/taxi.png")}}',
        PRIME : '{{asset("images/automobile.png")}}'
    };


    function getIcon(service)
    {
        return icons[service] == undefined ? 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png' :  icons[service];
    }


    function changeMarkerService(id, obj)
    {
        console.log(`changeMarkerService(${id})`);
    
        let marker = markers.find( (marker) =>{
            return marker.unique_id == id;
        });
        console.log('marker', marker, obj.value);
    
        if(!marker) {
            return;
        }
        marker['service'] = obj.value;
        marker.setIcon(getIcon(obj.value));
       
    }



    function addInfoWindow(marker) {
        
        let content = `

            <select onchange="changeMarkerService('${marker.unique_id}', this)">
                <option value="">Select</option>
                @foreach($services as $service)
                <option value="{{$service->code}}">{{$service->name}}</option>
                @endforeach
            </select>
            <i class="material-icons col-red" onclick="removeMarker('${marker.unique_id}')" style="cursor:pointer;vertical-align: middle;">delete_forever</i>
       
        `;
        
        google.maps.event.addListener(marker, 'click', function() {
            marker.setAnimation(null);
            infowindow.setContent(content);
            infowindow.open(map, this);
        });

        google.maps.event.addListener(marker, 'drag', function(event) {
            marker.setAnimation(null);
        });


    }
    
    
    function uniqueId()
    {
        return '_' + Math.random().toString(36).substr(2, 9);
    }
    
    
    function getMarker(latitude, longitude, service = '') {
        let marker = new google.maps.Marker({
            position: new google.maps.LatLng(latitude, longitude),
            draggable:true,
            icon : getIcon(service)
        });
        marker['unique_id'] = uniqueId();
        marker['service'] = service;
        addInfoWindow(marker);
        return marker;
    }
    
    
    var markers = [
        @foreach($locations as $location) 
            getMarker({{$location->latitude}}, {{$location->longitude}}, '{{$location->service}}'),
        @endforeach
    ];
    
    
    function placeMarkers() {
    
        markers.forEach( marker => {
            marker.setMap(map);
        });
    }
    
    
    function addNewMarker(latitude, longitude) {
        let marker = getMarker(latitude, longitude, 'AUTO');
        marker.setAnimation(google.maps.Animation.BOUNCE);
        markers.push(marker);
        placeMarkers();
    }
    
    function removeMarker(id)
    {
        console.log(`removeMarker(${id})`);
    
        let markerIndex = markers.findIndex( (marker) =>{
            return marker.unique_id == id;
        });
        console.log('markerIndex', markerIndex);
    
        if(markerIndex < 0) {
            return;
        }
    
        markers[markerIndex].setMap(null);
        markers.splice(markerIndex, 1);
    }
    
    
    
    var map;
    var autocomplete;
    var currLatitude, currLongitude;
    var infowindow;
    function initMap()
    {
        var latlng = new google.maps.LatLng(currLatitude, currLongitude);
    
        map = new google.maps.Map(document.getElementById('map'), {
            center: latlng,
            zoom: 17,
            mapTypeId: 'roadmap'
        });
    
        infowindow = new google.maps.InfoWindow();
    
        //init autocomplete search 
        autocomplete = new google.maps.places.Autocomplete((document.getElementById('autocomplete')));
        autocomplete.addListener('place_changed', placeChanged);
    
    
        /** place markers */
        placeMarkers();
    
    }
    
    function placeChanged()
    {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();
        console.log('place chnged', place)
        try {
    
            currLatitude = place.geometry.location.lat();
            currLongitude = place.geometry.location.lng();
    
            var latlng = new google.maps.LatLng(currLatitude, currLongitude);
            
            console.log(currLatitude, currLongitude)
            initMap();
    
        } catch(e) {
            console.log(e)
            document.getElementById('autocomplete').value=''
            showNotification('bg-black', 'unknown error happened', 'top', 'right', 'animated flipInX', 'animated flipOutX');
        }
        
    }
    
    
    
    $(document).ready(function(){

        /** load service sample images */
        $(".service_icon").each(function(index, item){
            $(item).attr('src', getIcon($(item).data('service')));
        });


        $(".save_locations_btn").on("click", function(){

            let locations = [];
            markers.forEach( marker => {
                locations.push({ 'latitude' : marker.position.lat(), 'longitude' : marker.position.lng(), 'service' : marker.service });
            });

            $.post("{{route('admin.driver.fake.locations.save')}}", {_token:"{{csrf_token()}}", locations : locations}, function(response){
                console.log(response)
                showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
            })
            .fail(function(response) {
                showNotification('bg-black', 'Unknown server error', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            });


        });
    

        $("#enable-input").on("change", function(){
            let enable = $("#enable-input").is(':checked') ? 'on' : 'off';
            $.post("{{route('admin.driver.fake.locations.enable')}}", {_token:"{{csrf_token()}}", enable : enable}, function(response){
                console.log(response)
                showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
            })
            .fail(function(response) {
                showNotification('bg-black', 'Unknown server error', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            });
        });

    
        $(".add_new_marker").on("click", function(){
            addNewMarker(currLatitude, currLongitude);
        });
    
    
    
        //get current location if possible
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position){
                console.log('current position', position)
                //init map with current location
                currLatitude = position.coords.latitude;
                currLongitude = position.coords.longitude;
                initMap();
            }, function(){
                console.log('Navigator not available');
                alert("Navigator not available");
            });
        } 
    
    });
</script>
@endsection