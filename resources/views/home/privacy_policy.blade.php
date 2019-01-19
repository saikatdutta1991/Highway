@extends('home.layouts.master')
@section('title', "Privacy Policy")
@section('top-header')
<style>
    .s-section {
    padding : 40px 0;
    }
</style>
@endsection
@section('content')
<header class="bg-gradient" style="padding-bottom: 3rem">
    <div class="container">
        <h1 class="header-title">Privacy Policy</h1>
    </div>
</header>
<div class="section s-section">
    <div class="container" id="main-container">
        <span id="progress-container"></span>
        <div class="row">
            <div class="col-md-12">
                {!! $privacyPolicy !!}
            </div>
        </div>
    </div>
</div>
@include('home.layouts.address')
@endsection