@extends('admin.layouts.master')
@section('title', 'Fake Locations')
@section('driver_active', 'active')
@section('fake_locations_active', 'active')
@section('top-header')
<style>
    .add_new_marker {
    cursor:pointer;
    }
    .input-group-addon .material-icons {
    font-size: xx-large !important;
    }   
    .input-group-addon {
    vertical-align: text-bottom;
    }
    #autocomplete {
    border: 1px solid #0000002b;
    padding: 15px;
    border-radius: 50px;
    }
    .gm-style {
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
                            <i class="material-icons add_new_marker col-green" title="Save">save</i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row clearfix">
                    <div id="map" class="col-md-12" style="height:450px;"></div>
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
    function addInfoWindow(marker) {
        let content = `<button class="btn btn-block bg-orange" onclick="removeMarker('${marker.unique_id}')">Remove</button>`;
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
    
    
    function getMarker(latitude, longitude) {
        let marker = new google.maps.Marker({
            position: new google.maps.LatLng(latitude, longitude),
            draggable:true
        });
        marker['unique_id'] = uniqueId();
        addInfoWindow(marker);
        return marker;
    }
    
    
    var markers = [
        getMarker(12.946420666754207, 77.54415974443361),
        getMarker(12.978539762259349, 77.73607716386721),
        getMarker(12.963149879497227, 77.55068287675783)
    ];
    
    
    function placeMarkers() {
    
        markers.forEach( marker => {
            marker.setMap(map);
        });
    }
    
    
    function addNewMarker(latitude, longitude) {
        let marker = getMarker(latitude, longitude);
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
            zoom: 12,
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