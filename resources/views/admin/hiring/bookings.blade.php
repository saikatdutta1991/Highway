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
<div class="modal" id="assign-model">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">ASSIGN DRIVER MANUALLY</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="booking_id">
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="material-icons">mail_outline</i>
                    </span>
                    <div class="form-line">
                        <input type="email" class="form-control date" placeholder="Enter driver's email id want to assign and the search">
                    </div>
                    <span class="input-group-addon">
                    <i class="material-icons">search</i>
                    </span>
                </div>
                <div style="color: red;text-align: center;display:none">
                    <i class="material-icons" style="vertical-align: bottom;color: red;">error_outline</i> No driver found. Try another email please.
                </div>
                <div >
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
                        <td rowspan="4">
                            <img style="width: 100px;height: 100px;" src="http://localhost:8000/drivers/profile/photos/driver__82416747564f2abe2ed0954f1fd91716_1565196476.png" alt="">
                        </td>
                    </tr>
                    <tr>
                        <td>Saikat Duuta</td>
                        <td>
                        <div class="rating"></div>
                        </td>
                    </tr>
                    <tr>
                        <th>Eamil</th>
                        <th>Moible</th>
                    </tr>
                    <tr>
                        <td>saikatdutta1991@gmail.com</td>
                        <td>+919093036897</td>
                    </tr>
                    </tbody>
                </table>  
                </div>
                              
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect">CONFIRM & ASSIGN</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('bottom')
<script src="http://auxiliary.github.io/rater/scripts/rater.js"></script>
<script> 

    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip(); 
    
        $(".assign-driver").on("click", function(){
            let bookingid = $(this).data("booking-id");console.log(bookingid)
            $("#assign-model input[name=booking_id]").val(bookingid);
            $("#assign-model span[name=booking_id_text]").text(bookingid);
            $("#assign-model").modal("show");
        });
        $("#assign-model").modal("show");

        $(".rating").rate().rate("setValue", 4);;
    
    });
</script>
@endsection