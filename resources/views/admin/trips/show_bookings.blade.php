@extends('admin.layouts.master')
@section('title', 'Outside Rides')
@section('rides_active', 'active')
@section('higiway_trips_bookings_active', 'active')
@section('top-header')
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>BOOKINGS</h2>
    </div>
    <!-- With Material Design Colors -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        LIST OF USER TRIP BOOKINGS
                        <small>Here you can see all trip bookings</small>                    
                    </h2>
                </div>
                <small>
                    <div class="body table-responsive">
                        @if(!$bookings->count())
                        <div class="alert bg-pink">
                            No Bookings Found
                        </div>
                        @else
                        <table class="table table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>User</th>
                                    <th>Name</th>
                                    <th>Seats</th>
                                    <th>Booking Date</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>{{$booking->booking_id}}</td>
                                    <td>
                                        <a  data-toggle="tooltip" 
                                            data-placement="left" 
                                            title="See user" 
                                            target="_blank"
                                            href="{{route('admin.show.user', ['user_id' => $booking->user->id])}}">
                                        {{$booking->user->fname}}
                                        </a>
                                    </td>
                                    <td>
                                        <a  data-toggle="tooltip" 
                                            data-placement="left" 
                                            title="See trip"
                                            target="_blank" 
                                            href="{{route('admin.show.trips')}}?trip_id={{$booking->trip->id}}">
                                        {{$booking->trip->name}}
                                        </a>
                                    </td>
                                    <td>{{$booking->booked_seats}}</td>
                                    <td>{{$booking->formatedBookingDate()}}</td>
                                    <td>{{$booking->booking_status}}</td>
                                    <td>{{$booking->payment_status}}</td>
                                    <td>{{$currency_symbol}}{{$booking->invoice->total}}</td>                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pull-right">
                            {!! $bookings->render() !!}
                        </div>
                        @endif
                    </div>
                </small>
            </div>
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>
@endsection
@section('bottom')
<script></script>
@endsection