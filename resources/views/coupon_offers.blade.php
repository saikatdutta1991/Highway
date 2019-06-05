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
    .expire-tag {
        font-size: 11px;
        right: 5px;
        bottom: 5px;
        color: #0000007a;
        position: absolute;
    }
    .coupon-name {
        position: absolute;
        left: 15px;
        top: 15px;
        color: white;
        background: #0000001f;
        padding: 10px;
        font-size: 1em;
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
                <div class="card">
                    <img class="card-img-top" src="{{$coupon->banner_picture_url}}" alt="Card image">
                    <h4 class="coupon-name">{{$coupon->name}}</h4>
                    <div class="card-body">
                        @if($coupon->discount_type=='flat'){{$currency_symbol}}@endif{{intval($coupon->discount_amount)}}@if($coupon->discount_type=='percentage')%@endif
                        @if($coupon->discount_type=='flat')-Minimum : {{$coupon->minimum_purchase}}{{$currency_symbol}}@endif
                        @if($coupon->discount_type=='percentage')-Upto : {{$coupon->maximum_discount_allowed}}{{$currency_symbol}}@endif
                        <p class="card-text">{{$coupon->description}}</p>
                        <a href="#" class="btn btn-primary">{{$coupon->code}}</a>
                    </div>
                    <span class="expire-tag">Expires: {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $coupon->expires_at)->setTimezone($default_timezone)->format('d-m-Y')}}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@include('home.layouts.address')
@endsection