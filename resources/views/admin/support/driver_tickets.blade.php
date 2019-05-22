@extends('admin.layouts.master')
@section('title', 'Driver-Tickets')
@section('support_active', 'active')
@section('driver_support_tickets', 'active')
@section('top-header')
<link href="{{url('admin_assets/admin_bsb')}}/plugins/light-gallery/css/lightgallery.css" rel="stylesheet">
<style></style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>DRIVER TICKETS</h2>
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
                DRIVER TICKETS
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
                        <th>AUDIOS</th>
                        <th>STATUS</th>
                        <th>RAISED ON</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td>{{$ticket->number}}</td>
                        <td><a data-toggle="tooltip" data-placement="left" title="Click to see driver : {{$ticket->driver->fullname()}}" href="{{route('admin.show.driver', ['driver_id' => $ticket->driver->id])}}" target="_blank">{{$ticket->driver->fullMobileNumber()}}</a></td>
                        <td>{{$ticket->type}}</td>
                        <td>
                            <i style="cursor:pointer" title="Expand description" data-toggle="collapse" class="material-icons" data-target="#description_ticket_{{$ticket->id}}" >keyboard_arrow_down</i>
                        </td>
                        <td>
                            <i style="cursor:pointer" title="Expand photos" data-toggle="collapse" class="material-icons" data-target="#photos_ticket_{{$ticket->id}}" >keyboard_arrow_down</i>
                        </td>
                        <td>
                            <i style="cursor:pointer" title="Expand photos" data-toggle="collapse" class="material-icons" data-target="#audios_ticket_{{$ticket->id}}" >keyboard_arrow_down</i>
                        </td>
                        <td id="ticket_status_{{$ticket->id}}">{{$ticket->status}}</td>
                        <td>{{$ticket->raisedOn($default_timezone)}}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn bg-pink btn-xs waves-effect dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i class="material-icons">view_list</i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li class="update_ticket_menu_item" data-ticket-id="{{$ticket->number}}"><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-green">done</i>UPDATE</a></li>
                                </ul>
                            </div>
                        </td>
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
                                            <div id="aniimated_thumbnials_{{$ticket->id}}" class="list-unstyled clearfix">
                                                @if($ticket->photo1_url != '')
                                                <a href="{{$ticket->photo1_url}}" data-sub-html="Photo 1">
                                                <img class=" thumbnail" src="{{$ticket->photo1_url}}" style="width:150px;height:150px;float:left;margin-bottom:0px;margin-right:15px;">
                                                </a>
                                                @endif
                                                @if($ticket->photo2_url != '')
                                                <a href="{{$ticket->photo2_url}}" data-sub-html="Photo 2"> 
                                                    <img class=" thumbnail" src="{{$ticket->photo2_url}}" style="width:150px;height:150px;float:left;margin-bottom:0px;margin-right:15px;">
                                                </a>
                                                @endif
                                                @if($ticket->photo3_url != '')
                                                <a href="{{$ticket->photo3_url}}" data-sub-html="Photo 3">
                                                    <img class=" thumbnail" src="{{$ticket->photo3_url}}"style="width:150px;height:150px;float:left;margin-bottom:0px;margin-right:15px;">
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    <tr>
                    <tr class="collapse" id="audios_ticket_{{$ticket->id}}">
                        <td colspan="8" >
                            @if($ticket->voice_url)
                            <audio controls autoplay>
                                <source src="{{$ticket->voice_url}}" type="audio/ogg">
                                <source src="{{$ticket->voice_url}}" type="audio/mpeg">
                                <source src="{{$ticket->voice_url}}" type="audio/mp3">
                                <source src="{{$ticket->voice_url}}" type="audio/wav">
                                Your browser does not support the audio element.
                            </audio>
                            @endif
                        </td>
                    <tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row pull-right">
            {!! $tickets->render() !!}
            </div>
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>

<!-- support update modal -->
<div class="modal fade" id="support_ticket_update_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">UPDATE SUPPORT TICKET</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <form id="cance_ride_form">
                        <input type="hidden" value="{{csrf_token()}}" name="_token">
                        <input type="hidden" name="support_ticket_id">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <b>Ticket Number</b>
                                <div class="form-line">
                                    <input type="text" required class="form-control"  name="support_ticket_id" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                                <div class="form-group">
                                    <b>Status</b>
                                    <div class="form-line">
                                        <select class="form-control show-tick" name="status">
                                            <option value = "pending">Pending</option>
                                            <option value = "processing">Processing</option>
                                            <option value = "resolved">Resolved</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <b>Remarks/Comment</b>
                                <div class="form-line">
                                    <input type="text" required class="form-control" placeholder="Type you remarks or comment here" name="remarks" id="remarks">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" id="support_ticket_update_save_btn">SAVE</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<!-- support update modal -->

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


        var updateticketapi = "{{route('admin.support.driver.ticket.update', ['ticket_number' => '*'])}}";
        $("#support_ticket_update_save_btn").on('click', function(){

            event.preventDefault();

            var data = $('#cance_ride_form').serializeArray();
            console.log(data)

            let ticketid = $("input[name=support_ticket_id]").val();
            let apiurl = updateticketapi.replace('*', ticketid)

            console.log(apiurl)

            $.post(apiurl, data, function(response){
                console.log(response)

                if(response.success){
                    showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    $("#support_ticket_update_modal").modal('hide');
                    $("#ticket_status_"+response.data.ticket.id).text(response.data.ticket.status);
                    return;
                }

                showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');

            }).fail(function(){
                showNotification('bg-black', 'Internal server error. Try again !!', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            })

        })


        $(".update_ticket_menu_item").on('click', function(){
            let ticketid = $(this).data('ticket-id');
            $("input[name=support_ticket_id]").val(ticketid);
            $("#support_ticket_update_modal").modal('show')
        })




    });  
            
</script>
@endsection