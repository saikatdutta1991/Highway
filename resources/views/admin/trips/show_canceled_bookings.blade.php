@extends('admin.layouts.master')
@section('title', 'Canceled Bookings')
@section('rides_active', 'active')
@section('canceled_bookings_active', 'active')
@section('top-header')
<style>
.refund-btn {
    color: green;
    cursor: pointer;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>CANCELED BOOKINGS</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    LIST OF ALL CANCELED BOOKINGS
                    <small>Here you can refund for canceled bookings</small>                    
                </h2>
            </div>
            <small>
                <div class="body table-responsive">
                    @if(!$cnclBookings->count())
                    <div class="alert bg-pink">
                        No Canceled Bookings Found
                    </div>
                    @else
                    
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>InvoiceID</th>
                                <th>User</th>
                                <th>Trip</th>
                                <th>Date</th>
                                <th>Canceled By</th>
                                <th>Paid Amount</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cnclBookings as $booking)
                            <tr>
                            <td title="Invoice Id : {{$booking->invoice->invoice_reference}}">...{{substr($booking->invoice->invoice_reference, -7)}}</td>
                            <td>
                                <a  data-toggle="tooltip" 
                                    data-placement="left" 
                                    title="Click to view user" 
                                    href="javascript:void(0)" 
                                    class="edit-user-btn" 
                                    data-user-id="{{$booking->user->id}}">{{$booking->user->fname}}
                                </a>
                            </td>
                            <td>{{$booking->trip->name}}</td>
                            <td>{{$booking->trip->tripFormatedDateString($default_timezone)}}</td>
                            <td>{{$booking->formatedCanceledBy()}}</td>
                            <td>{{$currency_symbol}}{{$booking->invoice->total}}</td>
                            <td @if($booking->invoice->payment_status == 'FULL_REFUNDED') style="color:green" @endif>{{$booking->invoice->payment_status == 'FULL_REFUNDED'?'REFUNDED':$booking->invoice->payment_status}}</td>
                            <td>
                                @if($booking->payment_status == 'PAID')
                                <button title="Click to full refund" type="button" data-booking-id="{{$booking->id}}" class="btn bg-red btn-xs waves-effect refund-btn">Full Refund</button> 
                                <button title="Click to partial refund" type="button" data-booking-id="{{$booking->id}}" data-paid-amount="{{$booking->invoice->total}}" class="btn bg-red btn-xs waves-effect partial-refund-btn">Partial Refund</button> 
                                @endif
                            </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pull-right">
                        {!! $cnclBookings->render() !!}
                    </div>
                    @endif
                </div>
            </small>
            </div>
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>

<!-- partial refund modal -->
<div class="modal fade" id="partial_refund_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">CANCELLATION CHARGE SETTINGS</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <form id="partial_refund_form">
                        <input type="hidden" value="{{csrf_token()}}" name="_token">
                        <input type="hidden" value="" name="booking_id">
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line disabled">
                                    <input type="number" disabled class="form-control" value="0.00" min="0" name="paid_amount">
                                    <label class="form-label">Paid Amount({{$currency_symbol}})</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line diabled">
                                    <input type="number" class="form-control" value="0.00" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="refund_amount">
                                    <label class="form-label">Refund Amount({{$currency_symbol}})</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="number" class="form-control" value="0.00" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="cancellation_charge">
                                    <label class="form-label">Cancellation Charge({{$currency_symbol}})</label>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="col-sm-12" id="partial_refund_form-error-div" style="display:none">
                        <div class="preloader pl-size-xs" style="float:left;margin-right: 5px;display:none">
                            <div class="spinner-layer pl-red-grey">
                                <div class="circle-clipper left">
                                    <div class="circle"></div>
                                </div>
                                <div class="circle-clipper right">
                                    <div class="circle"></div>
                                </div>
                            </div>
                        </div>
                        <h6 class="res-text" style="float:left;display:none">Processing ...</h6>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" id="partial-refund-confirm-btn">CONFIRM</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<!-- end partial refund modal -->


@endsection
@section('bottom')
<script>

    var csrf_token = "{{csrf_token()}}";
    let refund_url = "{{url('admin')}}/routes/trips/bookings/{booking_id}/refund-full"
    let refund_partial_url = "{{url('admin')}}/routes/trips/bookings/{booking_id}/refund-partial"

    $(document).ready(function(){


        $("#partial_refund_form input[name=cancellation_charge]").on('keyup', function(){
            var cancellationamount = $(this).val()
            var cancellationamount = parseFloat(cancellationamount)
            //$(this).val(cancellationamount.toFixed(2))

            var paidamount = $("#partial_refund_form input[name=paid_amount]").val()
            var refundamount = paidamount - cancellationamount;
            $("#partial_refund_form input[name=refund_amount]").val(refundamount.toFixed(2))

            console.log(paidamount, refundamount, cancellationamount)

        })


        $("#partial_refund_form input[name=refund_amount]").on('keyup', function(){
            var refund_amount = $(this).val()
            var refund_amount = parseFloat(refund_amount)
            

            var paidamount = $("#partial_refund_form input[name=paid_amount]").val()
            var cancellationamount = paidamount - refund_amount;
            $("#partial_refund_form input[name=cancellation_charge]").val(cancellationamount.toFixed(2))

            console.log(paidamount, refund_amount, cancellationamount)

        })


        $("#partial-refund-confirm-btn").on('click', function(){

            showServiceFareLoader('Processing ...');


            var paidamount = $("#partial_refund_form input[name=paid_amount]").val()
            var refundamount = $("#partial_refund_form input[name=refund_amount]").val()

            console.log("paidamount, refundamount", paidamount, refundamount)

            if(refundamount <= 0) {
                showServiceFareError('Refund amount must be greater than zero')
                return
            }

            if(parseFloat(refundamount) > parseFloat(paidamount)) {
                showServiceFareError('Refund amount must be lesser or same as paid amount')
                return
            }
      
            var data = $('#partial_refund_form').serializeArray();
            var bookingId = $("#partial_refund_form input[name=booking_id]").val()
            var url = refund_partial_url.replace('{booking_id}', bookingId)
        
            $.post(url, data, function(response){
                console.log(response)
                
                if(response.success) {
                    showServiceFareSuccess(response.text);
                } else {
                    showServiceFareError(response.text)
                }
                            
            }).fail(function(response) {
                showServiceFareError('Unknown server error')
            });

        })

        $(".partial-refund-btn").on('click', function(){
           
            $("#partial_refund_modal").modal('show')
            var paidamount = $(this).data('paid-amount')
            var bookingid = $(this).data('booking-id')

            $("#partial_refund_form input[name=booking_id]").val(bookingid)
            $("#partial_refund_form input[name=paid_amount]").val(paidamount)

            $("#partial_refund_form input[name=cancellation_charge]").keyup()

        })




        /**
            edit user link click handler
        */
        $(".edit-user-btn").on('click', function(){

            var userId = $(this).data('user-id');
            console.log(userId)
            var url = "{{url('admin/users')}}/"+userId;
            window.open(url, '_blank');

        });


        $(".refund-btn").on('click', function(){
           
            let clickedBtn = this;

            swal({
                title: "Are you sure to refund this booking",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, refund",
                cancelButtonText: "No, cancel please",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function (isConfirm) {
                if (!isConfirm)  {
                    return false;
                }
                
                
                var bookingId = $(clickedBtn).data('booking-id')
                var url = refund_url.replace('{booking_id}', bookingId)
                console.log(url)
                 
                        
                $.post(url, {_token:csrf_token}, function(response){
                    console.log(response)
                    if(response.success) {
                        showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    } else {
                        showNotification('bg-red', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    }

                    swal.close();
                                
                }).fail(function(response) {
                    
                    showNotification('bg-red', 'Unknown server error.', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    swal.close();
                });
                

            });


        })

        
    });



    function hideServiceFareErrorResDiv()
    {
        $("#partial_refund_form-error-div").hide();
        $("#partial_refund_form-error-div > .preloader").hide()
        $("#partial_refund_form-error-div > .res-text").hide()
    }
    
    function showServiceFareLoader(message)
    {
        $("#partial_refund_form-error-div").show();
        $("#partial_refund_form-error-div > .preloader").show()
        $("#partial_refund_form-error-div > .res-text").show().html(message).removeClass('col-red').addClass('col-black').removeClass('col-green');
    }
    
    function showServiceFareError(message)
    {
        $("#partial_refund_form-error-div").show();
        $("#partial_refund_form-error-div > .preloader").hide()
        $("#partial_refund_form-error-div > .res-text").show().html('<i style="vertical-align:middle" class="material-icons">error_outline</i>'+message).addClass('col-red').removeClass('col-black').removeClass('col-green');
    }

    function showServiceFareSuccess(message)
    {
        $("#partial_refund_form-error-div").show();
        $("#partial_refund_form-error-div > .preloader").hide()
        $("#partial_refund_form-error-div > .res-text").show().html('<i style="vertical-align:middle" class="material-icons">check_circle</i>'+message).addClass('col-green').removeClass('col-black');   
    }
        
    
</script>
@endsection