@extends('admin.layouts.master')
@section('title', 'Canceled Bookings')
@section('trips_active', 'active')
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
                                <i title="Click to refund" class="refund-btn material-icons" data-booking-id="{{$booking->id}}">settings_backup_restore</i>
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
@endsection
@section('bottom')
<script>

    var csrf_token = "{{csrf_token()}}";
    let refund_url = "{{url('admin')}}/routes/trips/bookings/{booking_id}/refund-full"

    $(document).ready(function(){

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
        
    
</script>
@endsection