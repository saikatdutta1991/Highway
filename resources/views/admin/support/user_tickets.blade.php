@extends('admin.layouts.master')
@section('title', 'User-Tickets')
@section('support_active', 'active')
@section('user_support_tickets', 'active')
@section('top-header')
<link href="{{url('admin_assets/admin_bsb')}}/plugins/light-gallery/css/lightgallery.css" rel="stylesheet">
<style></style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>USER TICKETS</h2>
</div>
<!-- Widgets -->
<div class="row clearfix">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-pink hover-expand-effect">
            <div class="icon">
                <i class="material-icons">headset_mic</i>
            </div>
            <div class="content">
                <div class="text">TICKETS</div>
                <div class="number count-to" data-from="0" data-to="{{$ticketsCount}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-red hover-expand-effect">
            <div class="icon">
                <i class="material-icons">headset_mic</i>
            </div>
            <div class="content">
                <div class="text">PENDING</div>
                <div class="number count-to" data-from="0" data-to="{{$pendingTickets}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-purple hover-expand-effect">
            <div class="icon">
                <i class="material-icons">headset_mic</i>
            </div>
            <div class="content">
                <div class="text">PROCESSING</div>
                <div class="number count-to" data-from="0" data-to="{{$processingTickets}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green hover-expand-effect">
            <div class="icon">
                <i class="material-icons">headset_mic</i>
            </div>
            <div class="content">
                <div class="text">RESOLVED</div>
                <div class="number count-to" data-from="0" data-to="{{$resolvedTickets}}" data-speed="1000" data-fresh-interval="20"></div>
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
                USER TICKETS
            </h2>
        </div>
        <div class="body table-responsive">
            <table class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>TICKET NO.</th>
                        <th>CONTACT NO.</th>
                        <th>TYPE</th>
                        <th>DESCRIPTION</th>
                        <th>PHOTOS</th>
                        <th>STATUS</th>
                        <th>RAISED ON</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td>{{$ticket->number}}</td>
                        <td><a data-toggle="tooltip" data-placement="left" title="Click to see user : {{$ticket->user->fullname()}}" href="{{route('admin.show.user', ['user_id' => $ticket->user->id])}}" target="_blank">{{$ticket->user->fullMobileNumber()}}</a></td>
                        <td>{{$ticket->type}}</td>
                        <td>
                            <i style="cursor:pointer" title="Expand description" data-toggle="collapse" class="material-icons" data-target="#description_ticket_{{$ticket->id}}" >keyboard_arrow_down</i>
                        </td>
                        <td>
                            <i style="cursor:pointer" title="Expand photos" data-toggle="collapse" class="material-icons" data-target="#photos_ticket_{{$ticket->id}}" >keyboard_arrow_down</i>
                        </td>
                        <td>{{$ticket->status}}</td>
                        <td>{{$ticket->raisedOn($default_timezone)}}</td>
                        <td></td>
                    </tr>
                    <tr class="collapse" id="description_ticket_{{$ticket->id}}">
                        <td colspan="8" >{{$ticket->description}}</td>
                    <tr>
                    <tr class="collapse" id="photos_ticket_{{$ticket->id}}">
                        <td colspan="8" >
                            <div class="row clearfix">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="card">
                                        <div class="body">
                                            <div id="aniimated_thumbnials_{{$ticket->id}}" class="list-unstyled row clearfix">
                                                @if($ticket->photo1_url != '')
                                                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                                    <a href="{{$ticket->photo1_url}}" data-sub-html="Photo 1">
                                                    <img class="img-responsive thumbnail" src="{{$ticket->photo1_url}}">
                                                    </a>
                                                    <small>Photo 1</small>
                                                </div>
                                                @endif
                                                @if($ticket->photo2_url != '')
                                                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                                    <a href="{{$ticket->photo2_url}}" data-sub-html="Photo 1">
                                                    <img class="img-responsive thumbnail" src="{{$ticket->photo2_url}}">
                                                    </a>
                                                    <small>Photo 2</small>
                                                </div>
                                                @endif
                                                @if($ticket->photo3_url != '')
                                                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                                    <a href="{{$ticket->photo3_url}}" data-sub-html="Photo 1">
                                                    <img class="img-responsive thumbnail" src="{{$ticket->photo3_url}}">
                                                    </a>
                                                    <small>Photo 3</small>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    <tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row pull-right">
                <div>
                </div>
            </div>
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>
@endsection
@section('bottom')
<script src="{{url('admin_assets/admin_bsb')}}/plugins/light-gallery/js/lightgallery-all.js"></script>
<script>
    $(document).ready(function(){
        
        @foreach($tickets as $ticket)
        $('#aniimated_thumbnials_{{$ticket->id}}').lightGallery({
            thumbnail: true,
            selector: 'a'
        });
        @endforeach

    });  
            
</script>
@endsection