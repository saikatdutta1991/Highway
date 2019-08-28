@extends('admin.layouts.master')
@section('title', 'Driver Bookings')
@section('rides_active', 'active')
@section('driver_bookings_active', 'active')
@section('top-header')
<style>
.cell-icon {
    vertical-align: text-top;
    font-size: 15px;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>DRIVER BOOKINGS</h2>
</div>
<!-- Widgets -->
<div class="row clearfix">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-pink hover-expand-effect">
            <div class="icon">
                <i class="material-icons">event_note</i>
            </div>
            <div class="content">
                <div class="text">BOOKINGS</div>
                <div class="number count-to" data-from="0" data-to="{{$bookingsCount}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-red hover-expand-effect">
            <div class="icon">
                <i class="material-icons">done_all</i>
            </div>
            <div class="content">
                <div class="text">COMPLETED</div>
                <div class="number count-to" data-from="0" data-to="{{$completedCount}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-purple hover-expand-effect">
            <div class="icon">
                <i class="material-icons">money_off</i>
            </div>
            <div class="content">
                <div class="text">PAYMENT PENDING</div>
                <div class="number count-to" data-from="0" data-to="{{$paymentPendingCount}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green hover-expand-effect">
            <div class="icon">
                <i class="material-icons">local_atm</i>
            </div>
            <div class="content">
                <div class="text">EARNINGS</div>
                <div class="number count-to" data-from="0" data-to="{{$earnings}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green hover-expand-effect">
            <div class="icon">
                <i class="material-icons">local_atm</i>
            </div>
            <div class="content">
                <div class="text">CASH EARNINGS</div>
                <div class="number count-to" data-from="0" data-to="{{$cashEarnings}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green hover-expand-effect">
            <div class="icon">
                <i class="material-icons">credit_card</i>
            </div>
            <div class="content">
                <div class="text">ONLINE EARNINGS</div>
                <div class="number count-to" data-from="0" data-to="{{$onlineEarnings}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
</div>
<!-- #END# Widgets -->
<!-- With Material Design Colors -->
<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="header">
            <h2>
                BOOKINGS @if(request()->has('name')) OF USER {{strtoupper(request()->name)}} @endif
            </h2>
        </div>
        <div class="body table-responsive">
            <table class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>USER</th>
                        <th>DRIVER</th>
                        <th>PACKAGE</th>
                        <th>ADDRESS</th>
                        <th>DATE</th>
                        <th>STATUS</th>
                        <th>AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td>{{$booking->id}}</td>
                        <td><a href="{{route('admin.show.user', ['user_id' => $booking->user->id])}}">{{$booking->user->fname.' '.$booking->user->lname}}</a></td>
                        @if($booking->driver)
                        <td><a href="{{route('admin.show.driver', ['driver_id' => $booking->driver->id])}}">{{$booking->driver->fname.' '.$booking->driver->lname}}</a></td>
                        @else
                        <td>N/A</td>
                        @endif
                        <td><i class="material-icons cell-icon">local_mall</i>{{$booking->package->name}}</td>
                        <td>{{$booking->pickup_address}}</td>
                        <td>
                            <span style="display: inline-flex;align-items: center;"><i class="material-icons cell-icon">date_range</i> {{$booking->formatedDate($default_timezone)}}</span>
                            <br>
                            <span style="display: inline-flex;align-items: center;"><i class="material-icons cell-icon">query_builder</i> {{$booking->formatedTime($default_timezone)}}</span>
                        </td>
                        <td>{{$booking->status_text}}</td>
                        @if($booking->invoice)
                        <td>{{$currency_symbol}}{{$booking->invoice->total}}</td>
                        @else
                        <td>N/A</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row pull-right">
            {!! $bookings->render() !!}
            </div>
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>
@endsection
@section('bottom')
<script> 
</script>
@endsection