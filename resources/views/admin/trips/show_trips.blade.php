@extends('admin.layouts.master')
@section('title', 'Outside Rides')
@section('rides_active', 'active')
@section('higiway_trips_active', 'active')
@section('top-header')
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>OUTSIDE RIDES</h2>
    </div>
    <!-- With Material Design Colors -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        LIST OF DRIVER TRIPS
                        <small>Here you can see all driver created highway trips</small>                    
                    </h2>
                </div>
                <small>
                    <div class="body table-responsive">
                        @if(!$trips->count())
                        <div class="alert bg-pink">
                            No Trips Found
                        </div>
                        @else
                        <table class="table table-condensed table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Driver</th>
                                    <th>Seats Booked</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trips as $trip)
                                <tr>
                                    <td>{{$trip->id}}</td>
                                    <td>{{$trip->name}}</td>
                                    <td>
                                        <a  data-toggle="tooltip" 
                                            data-placement="left" 
                                            title="See driver" 
                                            href="{{route('admin.show.driver', ['driver_id' => $trip->driver_id])}}">
                                        {{$trip->driver->fname.' '.$trip->driver->lname}}
                                        </a>
                                    </td>
                                    <td>{{$trip->seats_available}}/{{$trip->seats}}</td>
                                    <td>{{$trip->tripFormatedTimestampString()}}</td>
                                    <td>{{$trip->status}}</td>
                                    <td>
                                        <a title="See Bookings" class="btn bg-red btn-xs waves-effect" href="{{route('admin.show.bookings')}}?trip_id={{$trip->id}}" target="_blank">Bookings</a> 
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pull-right">
                            {!! $trips->render() !!}
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