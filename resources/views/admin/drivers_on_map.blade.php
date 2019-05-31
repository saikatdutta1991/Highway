@extends('admin.layouts.master')
@section('title', 'Drivers on map')
@section('driver_map_active', 'active')
@section('driver_active', 'active')
@section('top-header')
<style>
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
<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="header">
            <h2>
                SEARCH LOCATION AND SEE DRIVERS ON MAP
            </h2>
            <small>Drag your center marker to fetch nearby drivers</small>
        </div>
        <div class="body">
            <div class="row clearfix">
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="material-icons">location_on</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" autofocus id="autocomplete" placeholder="Type & Search Location">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="material-icons">space_bar</i>
                        </span>
                        <div class="form-line">
                            <input type="number" class="form-control" name="radius" placeholder="Radius (Distance)" value="100">
                        </div>
                        <span class="input-group-addon">{{$distance_unit}}</span>
                    </div>
                </div>
            </div>
            <div class="row clearfix">
                <div class="col-sm-12">
                    <div id="map" style="height:350px"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>
@endsection
@section('bottom')
<script>
    var driver_base_url = "{{route('admin.show.driver', ['driver_id' => '*'])}}/";
    var currLatitude = 12.963215;
    var currLongitude = 77.585568;
    var radius;
    
    $(document).ready(function(){
    
        radius = $("input[name='radius']").val();
        console.log('radius', radius)
    
        //get current location if possible
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position){
                console.log('current position', position)
                //init map with current location
                currLatitude = position.coords.latitude;
                currLongitude = position.coords.longitude;
                initMapScript();
            }, function(){
                console.log('Navigator not available')
                //take predefined location and init map
                initMapScript();
            });
        } 
    
    });
    
    
    
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
    var autocomplete;
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
    
        initCenterMarker('Your position', latlng)
    
        getNearbyDrivers(currLatitude, currLongitude);
    
    }
    
    
    var drivers = [];
    function getNearbyDrivers(latitude, longitude, callback)
    {
        //drawCircle();
        radius = $("input[name='radius']").val();
        var url = '{{route("admin-nearby-drivers")}}?';
        url += 'current_latitude='+latitude+'&current_longitude='+longitude+'&radius='+radius;
        $.get(url, function(response){
    
            console.log(response.data.drivers)
            drivers = response.data.drivers;
           
            setDriverMarkersOnMap(drivers);
        });
    }
    
    
    var markers = [];
    function setDriverMarkersOnMap(drivers)
    {
        if(map == undefined || map == null) {
            return;
        }
        //removing previous markers
        markers.forEach(function(marker){
            marker.setMap(null);
        })
    
        //markers array empty
        markers = [];
    
        var bounds = new google.maps.LatLngBounds();
    
        //ifcenter marker then push to bound
        if(cenerMarker != undefined || cenerMarker != null) {
            bounds.extend(cenerMarker.getPosition());
        }
    
        drivers.forEach(function(driver){
            console.log(driver)
    
            var latlng = new google.maps.LatLng(driver.latitude, driver.longitude);
            
            var icon = {
                url: '{{url('admin_assets/car.png')}}',
                scaledSize: new google.maps.Size(35, 35),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(32,65)
            };
    
            var marker = new MarkerWithLabel({
                icon : icon,
                animation: google.maps.Animation.DROP,
                position: latlng,
                map: map,
                title: 'Hello World!',
                labelContent: driver.fname+' '+driver.lname,
                labelAnchor: new google.maps.Point(40,80),
                labelClass: "my-custom-class-for-label", // your desired CSS class
                labelInBackground: true
            });
            
            let driverDetailsUrl = driver_base_url.replace('*', driver.id);
           
            var content = `
                    <h5>
                        DRIVER DETAIL
                    </h5>
                    <small>Name : ${driver.fname} ${driver.lname}</small><br>
                    <small>Vehile No. : ${driver.vehicle_number} </small><br>
                    <small>Mobile. : ${driver.full_mobile_number} </small><br>
                    <small>Full Details : <a href="${driverDetailsUrl}">Click Here</a></small>
               `;
            /* infowindow.setContent(content);
            infowindow.open(map, marker); */
    
            google.maps.event.addListener(marker, 'click', function() {
                infowindow.setContent(content);
                infowindow.open(map, this);
            });
    
    
            bounds.extend(marker.getPosition());
    
            marker.setMap(map);
            markers.push(marker);
    
        })
    
        map.fitBounds(bounds);
    
        if(!drivers.length) {
            map.setZoom(12);
        }
    
        console.log('markers', markers)
    
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
    
    
            initCenterMarker('Your position', latlng)
            getNearbyDrivers(currLatitude, currLongitude);
    
        } catch(e) {
            console.log(e)
            document.getElementById('autocomplete').value=''
            showNotification('bg-black', 'unknown error happened', 'top', 'right', 'animated flipInX', 'animated flipOutX');
        }
        
    }
    
    
    
    
    
    var cenerMarker;
    var centermarerpinurl = '{{url('admin_assets/placeholder.svg')}}';
    function initCenterMarker(title, latlng)
    {
        console.log('centermarker', cenerMarker)
    
        //if centermarker remove first
        if(cenerMarker) {
            map.setCenter(latlng);
            cenerMarker.setPosition(latlng);
            return;
        }
    
    
        //center marker icon
        var icon = {
            url: centermarerpinurl,
            scaledSize: new google.maps.Size(30, 30),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(32,65),
            labelOrigin: new google.maps.Point(15,13)
        };
    
        //initialize center marker
        cenerMarker = new google.maps.Marker({
            map: map,
            icon: icon,
            animation: google.maps.Animation.DROP,
            title: title,
            position: latlng,
            draggable: true,
            label: {
                text: 'GO',
                color: "black",
                fontSize: "9px",
                fontWeight: "bold"
            }
        });
    
    
        google.maps.event.addListener(cenerMarker, 'dragend', function() {
            console.log('Drag ended');
            console.log(cenerMarker.getPosition().lat(), cenerMarker.getPosition().lng())
            currLatitude = cenerMarker.getPosition().lat()
            currLongitude = cenerMarker.getPosition().lng();
            getNearbyDrivers(currLatitude, currLongitude);
        });
    
    
    
    }
</script>
@endsection