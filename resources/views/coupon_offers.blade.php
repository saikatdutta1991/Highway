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
</style>
@endsection
@section('content')
<header class="bg-gradient" style="padding-bottom: 3rem">
    <div class="container">
        <h1 class="header-title">Hot offers from <span class="website_name">{{$website_name}}</span></h1>
        <p class="tagline">We provide solutions that brings joy.</p>
    </div>
</header>
<div class="section" id="offers">
    <div class="container">
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
            @endforeach
        </div>
    </div>
</div>
@include('home.layouts.address')
@endsection