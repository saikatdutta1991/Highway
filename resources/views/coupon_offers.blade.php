@extends('home.layouts.master')
@section('title', 'Offers')
@section('top-header')
<style>
    h2 {
    color:white
    }
    .card-container {
    }
    .card-outer {
    }
</style>
@endsection
@section('content')
<header class="bg-gradient" style="padding-bottom:100px;">
    <div class="container mt-5">
        <h2>Top {{$website_name}} Coupons & Exclusive Free Ride Codes</h2>
        <!-- <p class="tagline">The one and only solution for any kind of mobila app landing needs. Just change the screenshots and texts and you are good to go. </p> -->
    </div>
    <!-- <div class="img-holder mt-3"><img src="{{asset('web/home/')}}/images/iphonex.png" alt="phone" class="img-fluid"></div> -->
</header>
<div class="section" id="offers">
    <div class="container">
        <div class="section-title">
            <small>OFFERS</small>
            <!-- <h3>Upgrade to Pro</h3> -->
        </div>
        <div class="row card-container">
            @foreach($coupons as $coupon)
            <div class="col-md-4 col-sm-6 card-outer">
                <div class="card col-md-12 p-0 mb-5">
                    <h5 class="card-header">{{$coupon->name}}</h5>
                    <div class="card-body">
                        <!-- <h5 class="card-title">Special title treatment</h5> -->
                        @if($coupon->discount_type=='flat'){{$currency_symbol}}@endif{{intval($coupon->discount_amount)}}@if($coupon->discount_type=='percentage')%@endif
                        @if($coupon->discount_type=='flat')-Minimum : {{$coupon->minimum_purchase}}{{$currency_symbol}}@endif
                        @if($coupon->discount_type=='percentage')-Upto : {{$coupon->maximum_discount_allowed}}{{$currency_symbol}}@endif
                        <p class="card-text">{{$coupon->description}}</p>
                        Use Code <button class="btn btn-primary">{{$coupon->code}}</button>
                    </div>
                    <div class="card-footer text-muted">
                        Expires: {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $coupon->expires_at)->setTimezone($default_timezone)->format('d-m-Y')}}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 card-outer">
                <div class="card col-md-12 p-0 mb-5">
                    <h5 class="card-header">{{$coupon->name}}</h5>
                    <div class="card-body">
                        <!-- <h5 class="card-title">Special title treatment</h5> -->
                        @if($coupon->discount_type=='flat'){{$currency_symbol}}@endif{{intval($coupon->discount_amount)}}@if($coupon->discount_type=='percentage')%@endif
                        @if($coupon->discount_type=='flat')-Minimum : {{$coupon->minimum_purchase}}{{$currency_symbol}}@endif
                        @if($coupon->discount_type=='percentage')-Upto : {{$coupon->maximum_discount_allowed}}{{$currency_symbol}}@endif
                        <p class="card-text">{{$coupon->description}}</p>
                        Use Code <button class="btn btn-primary">{{$coupon->code}}</button>
                    </div>
                    <div class="card-footer text-muted">
                        Expires: {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $coupon->expires_at)->setTimezone($default_timezone)->format('d-m-Y')}}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@include('home.layouts.address')
@endsection