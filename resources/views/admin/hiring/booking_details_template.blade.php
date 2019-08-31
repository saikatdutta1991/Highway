@extends('admin.layouts.master')
@section('title', 'Driver Bookings')
@section('rides_active', 'active')
@section('driver_bookings_active', 'active')
@section('content')
<div class="card">
    <div class="header">
        <h2>
            BOOKING DETAILS
        </h2>
    </div>
    <div class="body table-responsive">
        <table class="table table-condensed table-hover">
            <tbody>
                <tr>
                    <th>Name</th>  
                    <th>Email</th>    
                    <th>Mobile</th>                    
                    <td rowspan="8"><img src="{{$booking->pickup_location_map}}" alt=""></td>
                </tr>
                <tr>
                    <td>{{$booking->user->fname}} {{$booking->user->fname}}</td>
                    <td>{{$booking->user->email}}</td>
                    <td>{{$booking->user->full_mobile_number}}</td>
                </tr>
                <tr>
                    <th>Package</th> 
                    <th>Address</th>
                    <th>Status</th>                    
                </tr>
                <tr>
                    <td>{{$booking->package->name}}</td>
                    <td>{{$booking->pickup_address}}</td>
                    <td>{{$booking->status_text}}</td>
                </tr>
                <tr>
                    <th>Car Type</th> 
                    <th>Trip Type</th>             
                </tr>
                <tr>
                    <td>{{$booking->car_transmission_type}} - {{$booking->car_tye}}</td>
                    <td>{{$booking->trip_type}}</td>
                </tr>
                <tr>
                    <th>Booking Date</th>
                    <th>Trip Date</th>                    
                </tr>
                <tr>
                    <td>{{$booking->formatedBookingDate($default_timezone)}} @ {{$booking->formatedBookingTime($default_timezone)}}</td>
                    <td>{{$booking->formatedDate($default_timezone)}} @ {{$booking->formatedTime($default_timezone)}}</td>
                </tr>
                <tr>
                    <th>Driver Started</th> 
                    <th>Trip Started</th>
                    <th>Trip Ended</th>                    
                </tr>
                <tr>
                    <td>{{$booking->driver_started}}</td>
                    <td>{{$booking->trip_started}}</td>
                    <td>{{$booking->trip_ended}}</td>
                </tr>
                @if($booking->driver)
                <tr>
                    <th>Driver Picture</th>
                    <th>Driver Name</th> 
                    <th>Email</th>
                    <th>Mobile</th>                  
                </tr>
                <tr>     
                    <td rowspan="2"><img src="{{$booking->driver->profile_picture_url}}" width="100px" height="100px"></td>               
                    <td>{{$booking->driver->fname}} {{$booking->driver->lname}}</td>
                    <td>{{$booking->driver->email}}</td>
                    <td>{{$booking->driver->full_mobile_number}}</td>
                </tr>
                <tr>
                    <th>Ready To Get Hired</th>
                    <th>Manual Car</th> 
                    <th>Automatic Car</th>             
                </tr>
                <tr>     
                    <td></td>
                    <td>{{$booking->driver->ready_to_get_hired ? "Yes" : "No"}}</td>
                    <td>{{$booking->driver->manual_transmission ? "Yes" : "No"}}</td>
                    <td>{{$booking->driver->automatic_transmission ? "Yes" : "No"}}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection
@section('bottom')
@endsection