@extends('admin.layouts.master')
@section('title', 'Intracity Ride detail')
@section('rides_active', 'active')
@section('intracity_rides_active', 'active')
@section('top-header')
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>RIDE DETAILS</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <!-- <div class="header">
                <h2>UESR PROFILE</h2>
                </div> -->
            <div class="body">
                <div class="row clearfix">
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="header" style="">
                                <h2 style="display:inline-block">
                                    <img src="{{url('images/user-icon.png')}}" style="width:50px;height:50px;border-radius:50%">
                                    USER
                                </h2>
                                <h2 class="pull-right p-r-20" style="width: 0;">
                                    <i class="material-icons">stars</i>{{$ride->user->rating}}
                                </h2>
                            </div>
                            <div class="body">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>Name</td>
                                            <td class="text-right"><small>{{$ride->user->fname.' '.$ride->user->lname}}</small></td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td class="text-right"><small>{{$ride->user->email}}</small></td>
                                        </tr>
                                        <tr>
                                            <td>Mobile</td>
                                            <td class="text-right"><small>{{$ride->user->full_mobile_number}}</small></td>
                                        </tr>
                                        <tr>
                                            <td>Driver Rated</td>
                                            <td class="text-right"><small>{{$ride->driver_rating}}</small></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="header " style="border-bottom: 1px solid #ffffff2e !important">
                                <h2 style="display:inline-block">
                                    <img src="{{$ride->driver->profilePhotoUrl()}}" style="width:50px;height:50px;border-radius:50%">
                                    DRIVER
                                </h2>
                                <h2 class="pull-right p-r-20" style="width: 0;">
                                    <i class="material-icons">stars</i>
                                    {{$ride->driver->rating}}
                                </h2>
                            </div>
                            <div class="body ">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>Name</td>
                                            <td class="text-right"><small>{{$ride->driver->fname.' '.$ride->driver->lname}}</small></td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td class="text-right"><small>{{$ride->driver->email}}</small></td>
                                        </tr>
                                        <tr>
                                            <td>Mobile</td>
                                            <td class="text-right"><small>{{$ride->driver->full_mobile_number}}</small></td>
                                        </tr>
                                         <tr>
                                            <td>User Rated</td>
                                            <td class="text-right"><small>{{$ride->user_rating}}</small></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="body">
                                <div class="row clearfix">
                                    <div class="col-sm-6">
                                        <img src="{{$mapUrl}}" style="width:100%">
                                    </div>
                                    <div class="col-sm-6">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>PICKUP ADDRESS</th>
                                                    <th>DROP ADDRESS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><i class="material-icons col-green font-12">fiber_manual_record</i>{{$ride->source_address}}</td>
                                                    <td><i class="material-icons col-red font-12">fiber_manual_record</i>{{$ride->destination_address}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Start Time: @if($ride->ride_start_time)<br>{{$ride->getStartTime($default_timezone)}}@endif</td>
                                                    <td>End Time: @if($ride->ride_end_time)<br>{{$ride->getStartTime($default_timezone)}}@endif</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">Request Time: {{$ride->created_at->setTimezone($default_timezone)->format('h:i a')}}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">Duration: {{$ride->ride_time}} minute</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">Distance Traveled: {{$ride->ride_distance}} km</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">Date:
                                                        @if($ride->ride_end_time)
                                                        {{date('d M, Y', strtotime($ride->ride_end_time))}}
                                                        @else
                                                        {{date('d M, Y', strtotime($ride->created_at))}}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr class="font-bold col-teal">
                                                    <td colspan="2">Ride Status:
                                                        {{$ride->ride_status}}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                @if($ride->invoice)

                <div class="row clearfix">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="body table-responsive">
                                <div class="row clearfix">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>INVOICE REFERENCE</th>
                                                    <th class="text-right">{{$ride->invoice->invoice_reference}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Payment Mode</td>
                                                    <td class="text-right">{{$ride->invoice->payment_mode}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Payment Status</td>
                                                    <td class="text-right">{{$ride->invoice->payment_status}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Ride Fare</td>
                                                    <td class="text-right">{{$currency_symbol}}{{$ride->invoice->ride_fare}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Access Fee</td>
                                                    <td class="text-right">{{$currency_symbol}}{{$ride->invoice->access_fee}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tax</td>
                                                    <td class="text-right">{{$currency_symbol}}{{$ride->invoice->tax}}</td>
                                                </tr>
                                            </tbody>
                                            <thead>
                                                <tr>
                                                    <th>Total<small>(Rounded Off)</small></th>
                                                    <th class="text-right">{{$currency_symbol}}{{$ride->invoice->total}}</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                @endif


            </div>
        </div>
    </div>
</div>
@endsection
@section('bottom')
<script></script>
@endsection