@extends('home.layouts.master')
@section('title', "{$website_title} Referrals - Referrals")
@section('top-header')
<style>
    .s-section {
        padding : 40px 0;
    }

    .title {
        font-weight: 300;
        line-height: 2.25rem;
        font-size: 1.875rem;
        color: #424242;
        font-family: inherit;
    }


</style>
@endsection
@section('content')
<header class="bg-gradient" style="padding-bottom: 1srem">
    <!-- <div class="container">
        <h1 class="header-title">Terms & Conditions</h1>
    </div> -->
</header>
<div class="section s-section">
    <div class="container" id="main-container">
        <h1 class="title" style=>You've been invited to <span class="website_name">{{$website_name}}</span>!</h1>
        <div class="subtitle" data-hide-in="iOS,Android">
            <p>Please open this link from your mobile device.</p>
            <p><b class="shortlink">{{route('referrals.redirect', ['referrer_code' => $referrer_code])}}</b> Or copy and page code while register : {{$referrer_code}}</p>
        </div>
        <img src="{{asset('images/invited.svg')}}" class="youre-invited">
    </div>
</div>
@include('home.layouts.address')
@endsection