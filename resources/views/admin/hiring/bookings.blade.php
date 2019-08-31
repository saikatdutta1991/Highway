@extends('admin.layouts.master')
@section('title', 'Driver Bookings')
@section('rides_active', 'active')
@section('driver_bookings_active', 'active')
@section('top-header')
<style>
    .cell-icon {
    vertical-align: text-top;
    font-size: 15px;
    }
    .icon-btn {
    cursor : pointer;
    }
    .text {
    margin-top:0 !important;
    }
    @-moz-keyframes spin {
        from { -moz-transform: rotate(0deg); }
        to { -moz-transform: rotate(360deg); }
    }
    @-webkit-keyframes spin {
        from { -webkit-transform: rotate(0deg); }
        to { -webkit-transform: rotate(360deg); }
    }
    @keyframes spin {
        from {transform:rotate(0deg);}
        to {transform:rotate(360deg);}
    }
    div.details-loader {
        text-align: center;
        height : 100vh;
    }
    div.details-loader #spinner {
        transform: translate(-50%, -50%);
        position: absolute;
        left: 50%;
        top: 50%;
    }
    div.details-loader #spinner i{
        font-size: 50px;
        -webkit-animation: spin 4s infinite linear;
        vertical-align:middle;
    }
    #booking-details-modal .modal-dialog {
        margin-left: 0;
        margin-top: 0;
        width: 100vw;
        height : 100vh;
    }

    .close {
        opacity : initial;
    }

    /* #booking-details-modal .modal-dialog .modal-content {
        height : 100%;
    } */
</style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>DRIVER BOOKINGS</h2>
</div>
<!-- Widgets -->
<div class="row clearfix">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-pink hover-expand-effect">
            <div class="icon">
                <i class="material-icons">event_note</i>
            </div>
            <div class="content">
                <div class="text">BOOKINGS</div>
                <div class="number count-to" data-from="0" data-to="{{$bookingsCount}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-red hover-expand-effect">
            <div class="icon">
                <i class="material-icons">done_all</i>
            </div>
            <div class="content">
                <div class="text">COMPLETED</div>
                <div class="number count-to" data-from="0" data-to="{{$completedCount}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-purple hover-expand-effect">
            <div class="icon">
                <i class="material-icons">money_off</i>
            </div>
            <div class="content">
                <div class="text">PAYMENT PENDING</div>
                <div class="number count-to" data-from="0" data-to="{{$paymentPendingCount}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green hover-expand-effect">
            <div class="icon">
                <i class="material-icons">local_atm</i>
            </div>
            <div class="content">
                <div class="text">EARNINGS</div>
                <div class="number count-to" data-from="0" data-to="{{$earnings}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green hover-expand-effect">
            <div class="icon">
                <i class="material-icons">local_atm</i>
            </div>
            <div class="content">
                <div class="text">CASH EARNINGS</div>
                <div class="number count-to" data-from="0" data-to="{{$cashEarnings}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green hover-expand-effect">
            <div class="icon">
                <i class="material-icons">credit_card</i>
            </div>
            <div class="content">
                <div class="text">ONLINE EARNINGS</div>
                <div class="number count-to" data-from="0" data-to="{{$onlineEarnings}}" data-speed="1000" data-fresh-interval="20"></div>
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
                BOOKINGS @if(request()->has('name')) OF USER {{strtoupper(request()->name)}} @endif
            </h2>
        </div>
        <div class="body table-responsive">
            <table class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>USER</th>
                        <th>DRIVER</th>
                        <th>PACKAGE</th>
                        <th>CAR TYPE</th>
                        <th>DATE</th>
                        <th>STATUS</th>
                        <th>AMOUNT</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td>{{$booking->id}}</td>
                        <td><a href="{{route('admin.show.user', ['user_id' => $booking->user->id])}}">{{$booking->user->fname.' '.$booking->user->lname}}</a></td>
                        @if($booking->driver)
                        <td><a href="{{route('admin.show.driver', ['driver_id' => $booking->driver->id])}}">{{$booking->driver->fname.' '.$booking->driver->lname}}</a></td>
                        @else
                        <td>N/A</td>
                        @endif
                        <td>{{$booking->package->name}}</td>
                        <td>{{$booking->car_transmission_type}} - {{$booking->car_type}}</td>
                        <td>
                            <span style="display: inline-flex;align-items: center;"><i class="material-icons cell-icon">date_range</i> {{$booking->formatedDate($default_timezone)}}</span>
                            <br>
                            <span style="display: inline-flex;align-items: center;"><i class="material-icons cell-icon">query_builder</i> {{$booking->formatedTime($default_timezone)}}</span>
                        </td>
                        <td>{{$booking->status_text}}</td>
                        @if($booking->invoice)
                        <td>{{$currency_symbol}}{{$booking->invoice->total}}</td>
                        @else
                        <td>N/A</td>
                        @endif
                        <td>
                            <i class="material-icons icon-btn assign-driver" data-toggle="tooltip" title="Assign driver manually" data-booking-id="{{$booking->id}}">perm_identity</i>
                            <i class="material-icons icon-btn show-detail-btn" data-toggle="tooltip" title="Show in detail" data-booking-id="{{$booking->id}}">details</i>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row pull-right">
                {!! $bookings->render() !!}
            </div>
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>
<div class="modal" id="assign-model" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">ASSIGN DRIVER MANUALLY</h4>
            </div>
            <div class="modal-body">
                <form action="" id="driver_assign_form">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="booking_id">
                    <input type="hidden" name="driver_id">
                </form>
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="material-icons">mail_outline</i>
                    </span>
                    <div class="form-line">
                        <input type="email" class="form-control date" placeholder="Driver's email id or phone number want to search" name="email_input">
                    </div>
                    <span class="input-group-addon">
                    <i class="material-icons icon-btn" id="search-driver">search</i>
                    </span>
                </div>
                <div style="color: red;text-align: center;display:none" id="no-driver-div">
                    <i class="material-icons" style="vertical-align: bottom;color: red;">error_outline</i> No driver found. Try another email please.
                </div>
                <div id="driver-div">
                    <p>Driver found in our record, you can assign this driver for following booking <span name="booking_id_text"></span></p>
                    <table class="table table-dark">
                        <thead>
                            <tr>
                                <th>Picture</th>
                                <th>Name</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="6">
                                    <img style="width: 100px;height: 100px;" src="" alt="" id="dpicture">
                                </td>
                            </tr>
                            <tr>
                                <td id="dname"></td>
                                <td>
                                    <div id="drating" style="float:left;margin-right:5px;"></div><span id="dratingt"></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Eamil</th>
                                <th>Moible</th>
                            </tr>
                            <tr>
                                <td id="dmail"></td>
                                <td id="dmobile"></td>
                            </tr>
                            <tr>
                                <td>
                                    Driver ready get hired ?
                                    <span id="dready">
                                        <i class="material-icons true" style="vertical-align: bottom;color:green">check</i>
                                        <i class="material-icons false" style="vertical-align: bottom;color:red">clear</i>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Manual Transmission Car
                                    <span id="dmcar">
                                        <i class="material-icons true" style="vertical-align: bottom;color:green">check</i>
                                        <i class="material-icons false" style="vertical-align: bottom;color:red">clear</i>
                                    </span>
                                    <br>
                                    Automatic Transmission Car
                                    <span id="dacar">
                                        <i class="material-icons true" style="vertical-align: bottom;color:green">check</i>
                                        <i class="material-icons false" style="vertical-align: bottom;color:red">clear</i>
                                    </span>
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect assign-confirm">CONFIRM & ASSIGN</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="booking-details-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="material-icons">close</i></button>
                <button type="button" class="close"><i class="material-icons" style="color:#e74c3c" onclick="printDiv('printarea1')">print</i></button>
                <h4 class="modal-title">BOOKING DETAILS</h4>
            </div>
            <div class="modal-body" id="printarea1">

            </div>
            <div class="modal-footer">                
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('bottom')
<script src="http://auxiliary.github.io/rater/scripts/rater.js"></script>
<script> 
    let searchdriverapi = "{{route('admin.driver.search')}}?querystr=";
    let assigndriverapi = "{{route('admin.hiring.booking.assign.driver')}}";
    let bookingdetailsapi = "{{route('admin.hiring.booking.details.template', [ 'booking_id' => '*' ])}}";
    $(document).ready(function(){

        $(".show-detail-btn").on("click", function(){
            $("#booking-details-modal .modal-body").html(`<div class="details-loader"><span id="spinner"><i class="material-icons" >loop</i>Loading..</span></div>`);
            $("#booking-details-modal").modal("show");

            let bookingid = $(this).data("booking-id");
            let api = bookingdetailsapi.replace("*", bookingid);
            $.get(api, function(response){
                $("#booking-details-modal .modal-body").html(response);
            });

        });


        $("#assign-model .assign-confirm").on('click', function(){
            let data = $('#driver_assign_form').serializeArray();

            $.post(assigndriverapi, data, function(response){
                console.log(response)

                if(response.success){
                    showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    return;
                }

                showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');

            }).fail(function(){
                showNotification('bg-black', 'Internal server error. Try again !!', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            })

        })


        $("#search-driver").on("click", function(){
            let querystr = $("#assign-model input[name=email_input]").val();
            $.get(searchdriverapi + querystr, function(response){
                console.log("response", response);
                if(!response.success) {
                    $("#no-driver-div").show();
                    $("#driver-div").hide();
                    return;
                }

                $("#assign-model #dname").text(response.data.fname + " " + response.data.lname);
                $("#assign-model #dpicture").attr("src", response.data.profile_picture_url);
                $("#assign-model #dmail").text(response.data.email);
                $("#assign-model #dmobile").text(response.data.full_mobile_number);
                $("#drating").rate().rate("setValue", response.data.rating);
                $("#dratingt").text(response.data.rating);
                if(response.data.ready_to_get_hired) {
                    $("#dready .true").show();
                    $("#dready .false").hide();
                } else {
                    $("#dready .true").hide();
                    $("#dready .false").show();
                }

                if(response.data.automatic_transmission) {
                    $("#dacar .true").show();
                    $("#dacar .false").hide();
                } else {
                    $("#dacar .true").hide();
                    $("#dacar .false").show();
                }

                if(response.data.manual_transmission) {
                    $("#dmcar .true").show();
                    $("#dmcar .false").hide();
                } else {
                    $("#dmcar .true").hide();
                    $("#dmcar .false").show();
                }

                $("#assign-model input[name=driver_id]").val(response.data.id);

                $("#no-driver-div").hide();
                $("#driver-div").show();
                

            });
        });



        $('[data-toggle="tooltip"]').tooltip(); 
    
        $(".assign-driver").on("click", function(){
            let bookingid = $(this).data("booking-id");console.log(bookingid)
            $("#assign-model input[name=booking_id]").val(bookingid);
            $("#assign-model input[name=driver_id]").val('');
            $("#assign-model span[name=booking_id_text]").text(bookingid);
            $("#no-driver-div").hide();
            $("#driver-div").hide();
            $("#assign-model").modal("show");
        });
        //$("#assign-model").modal("show");
    
        
    
    });
    
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }
</script>
@endsection