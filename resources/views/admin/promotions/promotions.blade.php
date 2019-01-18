@extends('admin.layouts.master')
@section('title', 'Promotions')
@section('promotions_active', 'active')
@section('promotions_list_active', 'active')
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>PROMOTIONS</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="header">
            <h2>
                LIST OF ALL PROMOTIONS
                <small>List of all promotions created and status</small>
            </h2>
        </div>
        <div class="body table-responsive">
            @if($promotions->count() == 0)
            <div class="alert bg-pink">
                No promotions found
            </div>
            @else
            <table class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>TITLE</th>
                        <th>EMAIL</th>
                        <th>PUSH NOTIFICATION</th>
                        <th>BROADCAST TYPE</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promotions as $promotion)
                    <tr>
                        <td><a data-toggle="tooltip" data-placement="left" title="">{{$promotion->title}}</a></td>
                        <td>
                            <input type="checkbox" disabled class="filled-in chk-col-pink" @if($promotion->has_email) checked @endif/>
                            <label for=""></label>
                        </td>
                        <td>
                            <input type="checkbox" disabled class="filled-in chk-col-pink" @if($promotion->has_pushnotification) checked @endif/>
                            <label for=""></label>
                        </td>
                        <td>{{$promotion->broadcastTypeText()}}</td>
                        <td>{{$promotion->status}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>
@endsection
@section('bottom')
@endsection