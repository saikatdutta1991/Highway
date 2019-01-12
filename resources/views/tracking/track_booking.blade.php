@extends('home.layouts.master')
@section('title', "Track Booking : {$booking->booking_id}")
@section('top-header')
<style>
    .header-title {
    color:white
    }
    .acard {
    margin-bottom:20px;
    border-radius:0px;
    /* box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24); */
    transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
    border : none;
    }
    .acard:hover {
    box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
    }
    .acard .card-header {
    background: #f56091;
    border-radius:0;
    background-image: linear-gradient( 135deg, rgba(60, 8, 118, 0.8) 0%, rgba(250, 0, 118, 0.8) 100%);
    }
    
    
    .acard .card-header h5 .btn {
    color:white;
    font-size: 15px;
    }
    .btn-link:hover {
    cursor:pointer;
    }
    .btn-link:hover {
    text-decoration: none;
    }
    .s-section {
    padding : 40px 0;
    }
    #map-canvas {
    height: 500px;
    width: 100%;
    margin: 0px;
    padding: 0px
    }

    .progress {
        height:2.5rem;
        border-radius:0px;
    }

</style>
@endsection
@section('content')
<header class="bg-gradient" style="padding-bottom: 3rem">
    <div class="container">
        <h4 class="header-title">Track Your Booking</h4>
    </div>
</header>
<div class="section s-section">
    <div class="container" id="main-container">
        <span id="progress-container"></span>
        <div class="row">
            <div class="col-md-6">
                <div class="card acard">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#booking-details" aria-expanded="true" aria-controls="booking-details">
                            Booking Details
                            </button>
                        </h5>
                    </div>
                    <div id="booking-details" class="collapse" aria-labelledby="headingOne">
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr>
                                        <td>Booking ID</td>
                                        <th scope="row">{{$booking->booking_id}}</th>
                                    </tr>
                                    <tr>
                                        <td>Trip</td>
                                        <td>{{$trip->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Pickup Address</td>
                                        <td>{{$pickupPoint->address}}</td>
                                    </tr>
                                    <tr>
                                        <td>Drop Address</td>
                                        <td>{{$dropPoint->address}}</td>
                                    </tr>
                                    <tr>
                                        <td>Seats</td>
                                        <td>{{$booking->booked_seats}}</td>
                                    </tr>
                                    <tr>
                                        <td>Booking Date</td>
                                        <td>{{$booking->formatedBookingDate()}}</td>
                                    </tr>
                                    <tr>
                                        <td>Journey Date</td>
                                        <td>{{$trip->formatedJourneyDate($user->timezone)}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card acard">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#driver-details" aria-expanded="false" aria-controls="driver-details">
                            Driver Details
                            </button>
                        </h5>
                    </div>
                    <div id="driver-details" class="collapse">
                        <div class="card-body row">
                            <div class="col-md-4 col-sm-4">
                                <img class="card-img-top" src="{{$driver->profilePhotoUrl()}}" alt="Card image">
                            </div>
                            <div class="col-md-8 col-sm-8">
                                <table class="table table-bordered table-sm">
                                    <tbody>
                                        <tr>
                                            <td>Name</td>
                                            <th scope="row">{{$driver->fname}}{{$driver->lname}}</th>
                                        </tr>
                                        <tr>
                                            <td>Contact</td>
                                            <td>{{$driver->fullMobileNumber()}}</td>
                                        </tr>
                                        <tr>
                                            <td>Car</td>
                                            <td>{{$driver->vehicle_type}}</td>
                                        </tr>
                                        <tr>
                                            <td>Car Number</td>
                                            <td>{{$driver->vehicle_number}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card acard">
            <div class="card-header">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#map-tracking" aria-expanded="false" aria-controls="map-tracking">
                    Map Tracking
                    </button>
                </h5>
            </div>
            <div id="map-tracking" class="collapse show">
                <div class="card-body" style="padding:0px">
                    <div id="map-canvas"></div>
                    <div id="content-start">
                        <div class="card">
                            <div class="card-body">
                                <h5></h5>
                                <p class="card-text"></p>
                            </div>
                        </div>
                    </div>
                    <div id="content-end">
                        <div class="card">
                            <div class="card-body">
                                <h5></h5>
                                <p class="card-text"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('home.layouts.address')
@endsection
@section('bottom-script')
<script src="https://maps.googleapis.com/maps/api/js?key={{$google_maps_api_key_booking_track}}&libraries=places"></script>
<link rel="stylesheet" href="{{asset('web/popupmarker.css')}}">
<script src="{{asset('web/popupmarker.js')}}"></script>
<script>
    /** fetch booking progress every 30 sec */
    function getProgress()
    {
        $.get('{{route("track-booking-progress", ["bookingid" => $booking->booking_id])}}', function(response){
            $("#progress-container").html(response)
        });
    }
    getProgress();
    
    
    /** map tracking code */
    var mapPointsUrl = "{{route('track-booking-map', ['bookingid' => $booking->booking_id])}}"
    var directionsDisplay = new google.maps.DirectionsRenderer();
    var directionsService = new google.maps.DirectionsService();
    var map;
    var initLatitude = {{$pickupPoint->latitude}}
    var initLongitude = {{$pickupPoint->longitude}}
    var initLocation = new google.maps.LatLng(initLatitude, initLongitude);
    var totalDistance = '';
    var totalDuration = '';
    
    function initialize() 
    {
        map = new google.maps.Map(document.getElementById('map-canvas'), {
            zoom: 14,
            center: initLocation
        });
        directionsDisplay.setMap(map);
        getMapPoints();
    }
    
    google.maps.event.addDomListener(window, 'load', initialize);
    
            
    function calcRoute(slat, slng, dlat, dlng) 
    {
        totalDistance = '';
        totalDuration = '';
    
        var start = new google.maps.LatLng(slat, slng);
        var end = new google.maps.LatLng(dlat, dlng);
    
        var popup1 = new Popup(start, document.getElementById('content-start'));
        popup1.setMap(map);
        var popup2 = new Popup(end, document.getElementById('content-end'));
        popup2.setMap(map);
                       
        var request = {
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode.DRIVING
        };
    
        directionsService.route(request, function (response, status) {
            
            var legs = response.routes[0].legs;
            totalDistance = legs[0].distance.text;
            totalDuration = legs[0].duration.text;        
    
            if (status == google.maps.DirectionsStatus.OK) {
                
                directionsDisplay.setOptions({
                    suppressMarkers: true,
                    polylineOptions: {
                        strokeColor: 'black',
                        strokeWeight: 3,
                        clickable: true,
                        geodesic: true
                    }
                })
                directionsDisplay.setDirections(response);
    
            } 
    
        });
    
        
    }
    
        
    
    /** fetch map locations */
    var contentStart = $("#content-start");
    var contentEnd = $("#content-end");
    function getMapPoints()
    {
        $.get(mapPointsUrl, function(response){
            console.log(response)
            contentStart.find('.card-body h5').text(response.source.title)
            var address = response.source.address == '' ? `Distance: ${totalDistance}<br>ETA:${totalDuration}` : response.source.address
            contentStart.find('.card-body p').html(address)
            
            address = response.destination.address == '' ? `Distance: ${totalDistance}<br>ETA:${totalDuration}` : response.destination.address
            contentEnd.find('.card-body h5').text(response.destination.title)
            contentEnd.find('.card-body p').text(response.destination.address)
            
            calcRoute(response.source.lat, response.source.lng, response.destination.lat, response.destination.lng)
    
        });
    }
    
    
    
    setInterval(function() {
        getProgress()
        getMapPoints()
    }, 30000);
    
        
         
    
</script>
@endsection