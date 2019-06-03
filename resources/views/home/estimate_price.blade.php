@extends('home.layouts.master')
@section('title', "Estimate Price")
@section('top-header')
<style>
.header-title {
    font-size:2rem;
    margin-bottom: 0.2rem;
}
.tagline {
    max-width: initial;
    font-size: 1rem;
    color: white;
}
h3 {
    font-family: 'UberMove', 'Open Sans', 'Helvetica Neue', Helvetica, sans-serif;
}

#city_price_calculate_form .input-group-text {
    background-color: #db3e8d;
    border: 1px solid #db3e8d;
    color: white;
}

#map-canvas {
    height: 450px;
    width: 100%;
    margin: 0px;
    padding: 0px;
    border: 1px solid #00000024;
}




@media screen and (min-width: 767px) {
    .fare-table {
        border: 1px solid #00000026;
        width: 200px;
        position: absolute;
        z-index: 1;
        top: 10px;
        left: 25px;
        border-radius: 5px;
        overflow: hidden;
    }
}

</style>
@endsection
@section('content')
<header class="bg-gradient" style="padding-bottom: 3rem">
    <div class="container">
        <h1 class="header-title">How much does a ride with <span class="website_name">{{$website_name}}</span> cost?</h1>
        <p class="tagline">Plan your next trip with the price estimator. Know before you go, so there’s no math and no surprises.</p>
    </div>
</header>
<div class="section s-section">
    <div class="container" id="main-container">
        <span id="progress-container"></span>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"><span class="website_name">{{$website_name}}</span> City Ride Price Calculator</h4>

                        <form class="form-inline" id="city_price_calculate_form">
                            <div class="input-group mb-3 input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i class="material-icons">location_on</i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Pickup location" id="city_ride_pickup_input" required>
                                <input type="hidden" id="city_ride_pickup_latitude_input">
                                <input type="hidden" id="city_ride_pickup_longitude_input">
                            </div>
                            <div class="input-group mb-3 ml-3 input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i class="material-icons">pin_drop</i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Drop location" id="city_ride_drop_input" required>
                                <input type="hidden" id="city_ride_drop_latitude_input">
                                <input type="hidden" id="city_ride_drop_longitude_input">
                            </div>
                            <div class="input-group mb-3 ml-3 input-group">
                            <button type="submit" class="btn btn-primary btn">Calculate</button>
                            </div>
                        </form>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-dark table-hover fare-table table-sm">
                                    <thead>
                                    <tr>
                                        <th colspan="2">Estimated Price</th>
                                    </tr>
                                    <tr>
                                        <th>Service</th>
                                        <th>Price</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <div id="map-canvas"></div>
                            </div>
                        </div>
                        <br>
                        <p class="card-text" style="color:black;">*Note: Sample rider prices are estimates only and do not reflect variations due to discounts, traffic delays, or other factors. Flat rates and minimum fees may apply. Actual prices may vary.</p>    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="section light-bg">
    <div class="container">
        <div class="section-title">
            <h3>How city ride prices are calculated</h3>
            <p></p>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card features">
                    <div class="card-body">
                        <div class="media">
                            <span class="ti-map-alt gradient-fill ti-3x mr-3"></span>
                            <div class="media-body">
                                <h4 class="card-title">Base Fare</h4>
                                <p class="card-text"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card features">
                    <div class="card-body">
                        <div class="media">
                            <span class="ti-location-arrow gradient-fill ti-3x mr-3"></span>
                            <div class="media-body">
                                <h4 class="card-title">Access Fee</h4>
                                <p class="card-text"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card features">
                    <div class="card-body">
                        <div class="media">
                            <span class="ti-loop gradient-fill ti-3x mr-3"></span>
                            <div class="media-body">
                                <h4 class="card-title">Per Kilometer</h4>
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
<script>

var directionsDisplay = new google.maps.DirectionsRenderer();
var directionsService = new google.maps.DirectionsService();
var getPriceApi = "{{route('priceesimate.list')}}";
var currencySymbol = '{{$currency_symbol}}';

function initAutoPlaceSearch() {
    let cityPickupInput = new google.maps.places.Autocomplete(document.getElementById('city_ride_pickup_input'));
    let cityDropInput = new google.maps.places.Autocomplete(document.getElementById('city_ride_drop_input'));
    google.maps.event.addListener(cityPickupInput, 'place_changed', function () {
        let place = cityPickupInput.getPlace();
        document.getElementById('city_ride_pickup_latitude_input').value = place.geometry.location.lat();
        document.getElementById('city_ride_pickup_longitude_input').value = place.geometry.location.lng();
    });
    google.maps.event.addListener(cityDropInput, 'place_changed', function () {
        let place = cityDropInput.getPlace();
        document.getElementById('city_ride_drop_latitude_input').value = place.geometry.location.lat();
        document.getElementById('city_ride_drop_longitude_input').value = place.geometry.location.lng();
    });
}

function initMap()
{
    map = new google.maps.Map(document.getElementById('map-canvas'), {
        zoom:16,
        center: new google.maps.LatLng(12.9352273, 77.62443310000003)
    });
    directionsDisplay.setMap(map);
}


async function getEstimateFare(distance)
{
    return new Promise(function(resolve, reject) {
        
        $.get(`${getPriceApi}?distance=${distance}`, (response) => {
            resolve(response.data);
        });
        
    });
}

function showPrice(priceData)
{
    let tableBody = $(".fare-table > tbody");

    //clear table body
    tableBody.html('');

    priceData.forEach(price => {
        
        let row = `<tr>
            <td>${price.service_name}</td>
            <td>${currencySymbol}${price.fare.total}</td>
        </tr>`;
        tableBody.append(row);
    });
}

function calculateAndShowRoute(start, end) 
{

    new google.maps.Marker({
        position: start,
        map: map,
        icon : {
        
                url: "https://cdn0.iconfinder.com/data/icons/map-location-solid-style/91/Map_-_Location_Solid_Style_06-256.png",
                scaledSize: new google.maps.Size(50, 50), // scaled size
                origin: new google.maps.Point(0,0), // origin
                anchor: new google.maps.Point(25, 20) // anchor
            
        }
    });

    new google.maps.Marker({
        animation: google.maps.Animation.BOUNCE,
        position: end,
        map: map,
        icon : {
        
                url: "https://cdn4.iconfinder.com/data/icons/miu/24/editor-flag-notification-glyph-256.png",
                scaledSize: new google.maps.Size(50, 50), // scaled size
                origin: new google.maps.Point(0,0), // origin
                anchor: new google.maps.Point(25, 20) // anchor
            
        }
    });



    var totalDistance = '';

    var request = {
        origin: start,
        destination: end,
        travelMode: google.maps.TravelMode.DRIVING
    };

    directionsService.route(request, async function (response, status) {

        if (status == google.maps.DirectionsStatus.OK) {
            
            directionsDisplay.setOptions({
                preserveViewport: true,
                suppressMarkers: true,
                polylineOptions: {
                    strokeColor: 'black',
                    strokeWeight: 3,
                    clickable: true,
                    geodesic: true
                }
            })
            directionsDisplay.setDirections(response);

            var bounds = new google.maps.LatLngBounds();
            bounds.union(directionsDisplay.getDirections().routes[0].bounds);
            map.setCenter(bounds.getCenter()); 
            map.fitBounds(bounds);
            map.setZoom(map.getZoom() + 0.3);


            var legs = response.routes[0].legs;
            totalDistance = legs[0].distance.value;      

            console.log('calculateAndShowRoute', response, totalDistance);

            let priceData = await getEstimateFare(totalDistance);
            console.log('priceData', priceData);
            showPrice(priceData);


        } 

    });

    
}






$(document).ready(async ()=>{
    initAutoPlaceSearch();
    initMap();

    let priceData = await getEstimateFare(0);
    console.log(priceData);
    showPrice(priceData);


    //handle calculate price btn submit
    $("#city_price_calculate_form").on('submit', (event) => {
        event.preventDefault();

        let start = new google.maps.LatLng($("#city_ride_pickup_latitude_input").val(), $("#city_ride_pickup_longitude_input").val());
        let end = new google.maps.LatLng($("#city_ride_drop_latitude_input").val(), $("#city_ride_drop_longitude_input").val());

        calculateAndShowRoute(start, end);

    });

});
</script>
@endsection