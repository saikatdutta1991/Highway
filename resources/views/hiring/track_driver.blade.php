<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Track Driver</title>
    <style>
        body {
            margin: 0;
        }
        .no-tracking {
            width: 100%;
            height: 100%;
            position: fixed;
            background: #e74c3c;
            top: 0px;
            left: 0px;
            z-index: 9999;
        }

        .no-tracking p {
            color: #ecf0f1;
            font-size: 1.5em;
            font-family: sans-serif;
            display: inline-block;
            padding: 15px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        .hide {
            display:none;
        }

        #map-canvas {
            position:fixed !important;
            width:100%;
            height:100%;
            
        }

        .info {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            background: #2c3e50;
            color: #ecf0f1;
            padding: 10px;
        }

    </style>
</head>
<body>
    
    <div class="no-tracking @if($booking) hide @endif">
        <p>Tracking only available when driver is on the way to location.</p>
    </div>
    
    @if($booking)
    <div id="map-canvas"></div>
    <div class="info">ETA : <span id="eta"></span> &nbsp; Distance : <span id="ed"></span></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{$google_maps_api_key_booking_track}}&libraries=places"></script>
    <script>
        
        /** map tracking code */
        var locationurl = "{{route('hiring.bookings.track.location', ['booking_id' => $booking->id])}}"
        var directionsDisplay = new google.maps.DirectionsRenderer();
        var directionsService = new google.maps.DirectionsService();
        var map;
        var initLatitude = {{$booking->pickup_latitude}}
        var initLongitude = {{$booking->pickup_longitude}}
        var initLocation = new google.maps.LatLng(initLatitude, initLongitude);
        var totalDistance = '';
        var totalDuration = '';
        var sourceMarker, destMarker;
        
        function initialize() 
        {
            map = new google.maps.Map(document.getElementById('map-canvas'), {
                zoom:16,
                center: initLocation,
                disableDefaultUI: true
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
                
            if(!sourceMarker) {
                //sourceMarker.setMap(null);
                sourceMarker = new google.maps.Marker({
                    position: start,
                    map: map,
                    icon : {            
                        url: "https://image.flaticon.com/icons/svg/1541/1541400.svg",
                        scaledSize: new google.maps.Size(50, 50), // scaled size
                        origin: new google.maps.Point(0,0), // origin
                        anchor: new google.maps.Point(25, 20) // anchor
                    }
                });
            }
            

            if(destMarker) {
                destMarker.setMap(null);
            }
            destMarker = new google.maps.Marker({
                position: end,
                map: map,
                icon : {
                    url: "https://www.flaticon.com/premium-icon/icons/svg/2007/2007324.svg",
                    scaledSize: new google.maps.Size(50, 50), // scaled size
                    origin: new google.maps.Point(0,0), // origin
                    anchor: new google.maps.Point(25, 20) // anchor
                }
            });
    
                        
            var request = {
                origin: start,
                destination: end,
                travelMode: google.maps.TravelMode.DRIVING
            };
        
            directionsService.route(request, function (response, status) {
                console.log('response', response);
                
                var legs = response.routes[0].legs;
                totalDistance = legs[0].distance.text;
                totalDuration = legs[0].duration.text;
                $("#eta").text(totalDuration);
                $("#ed").text(totalDistance);     
        
                if (status == google.maps.DirectionsStatus.OK) {
                    
                    directionsDisplay.setOptions({
                        preserveViewport: true,
                        suppressMarkers: true,
                        polylineOptions: {
                            strokeColor: '#E74C3C',
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
                    //map.setZoom(map.getZoom() + 0.3)
                } 
        
            });
        
            
        }
        
            
        
        /** fetch map locations */
        function getMapPoints()
        {
            $.get(locationurl, function(response){

                console.log("locationurl", response);
                if(response.data) {
                    $(".no-tracking").hide();
                    calcRoute(response.data.pickup_latitude, response.data.pickup_longitude, response.data.latitude, response.data.longitude)
                } else {
                    $(".no-tracking").show();
                }
                
            });
        }  

        setInterval(() => {
            getMapPoints();
        }, 5000);
        
    </script>
    @endif

</body>
</html>