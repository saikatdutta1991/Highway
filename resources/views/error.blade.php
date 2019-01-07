@extends('home.layouts.master')
@section('title', $title)
@section('top-header')
<style>
    h4 {
    color:white
    }
    .acard {
    margin-bottom:5px;
    border-radius:0px;
    }
    .acard .card-header {
    background: #f56091;
    }
    .acard .card-header h5 .btn {
    color:white;
    font-size: 15px;
    }
    .btn-link:hover {
    cursor:pointer;
    }
    .btn-link:hover {
    text-decoration: none;
    }
    .s-section {
    padding : 40px 0;
    }
    /* background: #f56091; */
</style>
@endsection
@section('content')
<header class="bg-gradient" style="padding-bottom: 3rem">
    <div class="container">
        <h4>{{$header}}</h4>
    </div>
</header>
<div class="section s-section">
    <div class="container">
		{!! $message !!}
	</div>
</div>
@include('home.layouts.address')
@endsection