@extends('home.layouts.master')
@section('title', "Track Booking : {$booking->booking_id}")
@section('top-header')
<style>
    h4 {
    color:white
    }
    .acard {
    margin-bottom:20px;
    border-radius:0px;
    /* box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24); */
    transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    }
    .acard:hover {
    box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
    }
    .acard .card-header {
    background: #f56091;
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
    height: 400px;
    width: 100%;
    margin: 0px;
    padding: 0px
    }
    /* background: #f56091; */
</style>
@endsection
@section('content')
<header class="bg-gradient" style="padding-bottom: 3rem">
    <div class="container">
        <h4>Track Your Booking</h4>
    </div>
</header>
<div class="section s-section">
    <div class="container">
        @include('tracking.booking_progress')
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
                    <div id="booking-details" class="collapse show" aria-labelledby="headingOne">
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
                    <div id="driver-details" class="collapse show">
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
                    <input type="button" id="routebtn" value="route" />
                    <div id="map-canvas"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('home.layouts.address')
@endsection
@section('bottom-script')
<script src="https://maps.googleapis.com/maps/api/js?key={{$google_maps_api_key_booking_track}}&libraries=places"></script>
<script>
        function mapLocation() {
            var directionsDisplay;
            var directionsService = new google.maps.DirectionsService();
            var map;

            function initialize() {
                directionsDisplay = new google.maps.DirectionsRenderer();
                var chicago = new google.maps.LatLng(12.9398505, 77.6047609);
                var mapOptions = {
                    zoom: 14,
                    center: chicago
                };
                map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
                directionsDisplay.setMap(map);
                google.maps.event.addDomListener(document.getElementById('routebtn'), 'click', calcRoute);
                calcRoute()
            }

            function calcRoute() {


                
                    


                        //var start = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                        var start = new google.maps.LatLng({{$pickupPoint->latitude}}, {{$pickupPoint->longitude}});
                        //var end = new google.maps.LatLng(38.334818, -181.884886);
                        var end = new google.maps.LatLng({{$dropPoint->latitude}}, {{$dropPoint->longitude}});
                        var request = {
                            origin: start,
                            destination: end,
                            travelMode: google.maps.TravelMode.DRIVING
                        };
                        directionsService.route(request, function (response, status) {
                            console.log('response', response)

                            var totalDistance = 0;
                            var totalDuration = 0;
                            var legs = response.routes[0].legs;
                            for (var i = 0; i < legs.length; ++i) {
                                totalDistance += legs[i].distance.value;
                                totalDuration += legs[i].duration.value;
                            }
                            console.log('totalDistance', totalDistance)
                            console.log('totalDuration', totalDuration)

                            if (status == google.maps.DirectionsStatus.OK) {
                                directionsDisplay.setOptions({
                                    polylineOptions: {
                                        strokeColor: 'red',
                                        strokeWeight: 4
                                    }
                                })
                                directionsDisplay.setDirections(response);
                                directionsDisplay.setMap(map);
                            } else {
                                alert("Directions Request from " + start.toUrlValue(6) + " to " + end.toUrlValue(6) + " failed: " + status);
                            }
                        });

            }

            google.maps.event.addDomListener(window, 'load', initialize);
        }
        mapLocation();    

</script>
@endsection