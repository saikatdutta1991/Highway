
        <table class="table table-condensed table-hover">
            <tbody>
                <tr>
                    <th>Name</th>  
                    <th>Email</th>    
                    <th>Mobile</th>                    
                    <td rowspan="10" width="400px" height="400px"><img width="100%" height="100%" src="{{$booking->pickup_location_map}}" alt=""></td>
                </tr>
                <tr>
                    <td>{{$booking->user->fname}} {{$booking->user->fname}}</td>
                    <td>{{$booking->user->email}}</td>
                    <td>{{$booking->user->full_mobile_number}}</td>
                </tr>
                <tr>
                    <th>Package</th> 
                    <th>Address</th>
                    <th>Status</th>                    
                </tr>
                <tr>
                    <td>{{$booking->package->name}}</td>
                    <td>{{$booking->pickup_address}}</td>
                    <td>{{$booking->status_text}}</td>
                </tr>
                <tr>
                    <th>Car Type</th> 
                    <th>Trip Type</th>             
                </tr>
                <tr>
                    <td>{{$booking->car_transmission_type}} - {{$booking->car_tye}}</td>
                    <td>{{$booking->trip_type}}</td>
                </tr>
                <tr>
                    <th>Booking Date</th>
                    <th>Trip Date</th>                    
                </tr>
                <tr>
                    <td>{{$booking->formatedBookingDate($default_timezone)}} @ {{$booking->formatedBookingTime($default_timezone)}}</td>
                    <td>{{$booking->formatedDate($default_timezone)}} @ {{$booking->formatedTime($default_timezone)}}</td>
                </tr>
                <tr>
                    <th>Driver Started</th> 
                    <th>Trip Started</th>
                    <th>Trip Ended</th>                    
                </tr>
                <tr>
                    <td>{{$booking->driver_started}}</td>
                    <td>{{$booking->trip_started}}</td>
                    <td>{{$booking->trip_ended}}</td>
                </tr>
                @if($booking->driver)
                <tr>
                    <th>Driver Picture</th>
                    <th>Driver Name</th> 
                    <th>Email</th>
                    <th>Mobile</th>                  
                </tr>
                <tr>     
                    <td rowspan="2"><img src="{{$booking->driver->profile_picture_url}}" width="100px" height="100px"></td>               
                    <td>{{$booking->driver->fname}} {{$booking->driver->lname}}</td>
                    <td>{{$booking->driver->email}}</td>
                    <td>{{$booking->driver->full_mobile_number}}</td>
                </tr>
                <tr>
                    <th>Ready To Get Hired</th>
                    <th>Manual Car</th> 
                    <th>Automatic Car</th>             
                </tr>
                <tr>     
                    <td></td>
                    <td>{{$booking->driver->ready_to_get_hired ? "Yes" : "No"}}</td>
                    <td>{{$booking->driver->manual_transmission ? "Yes" : "No"}}</td>
                    <td>{{$booking->driver->automatic_transmission ? "Yes" : "No"}}</td>
                </tr>
                @endif
            </tbody>
        </table>
        
@if($booking->invoice)
        <table class="table table-condensed table-hover">
            <tbody>
                <tr>
                    <th>INVOICE DETAILS</th>  
                </tr>
                <tr>
                    <th>Invoice Reference</th>
                    <th>Payment Mode</th>  
                    <th>Payment Status</th>  
                </tr>
                <tr>
                    <td>{{$booking->invoice->invoice_reference}}</td>
                    <td>{{$booking->invoice->payment_mode}}</td>
                    <td>{{$booking->invoice->payment_status}}</td>
                </tr>
                <tr>
                    <th>Ride Fare</th> 
                    <th>Night Charge</th>
                    <th>Coupon Discount</th>
                    <th>Tax</th>
                    <th>Total</th>                    
                </tr>
                <tr>
                    <td>{{$booking->invoice->ride_fare}}</td>
                    <td>{{$booking->invoice->night_charge}}</td>
                    <td>-{{$booking->invoice->coupon_discount}}</td>
                    <td>{{$booking->invoice->tax}}</td>
                    <td>{{$booking->invoice->total}}</td>
                </tr>

                @if($booking->invoice->transaction)
                <tr>
                    <th>Transaction ID</th> 
                    <th>Payment Method</th>
                    <th>Transaction Status</th>
                    <th>Transaction Date</th>                  
                </tr>
                <tr>
                    <td>{{$booking->invoice->transaction->trans_id}}</td>
                    <td>{{$booking->invoice->transaction->payment_method}}</td>
                    <td>{{$booking->invoice->transaction->status}}</td>
                    <td>{{\Carbon\Carbon::parse($booking->invoice->transaction->created_at)->setTimezone($default_timezone)->format('d-m-y @ h:i a')}}</td>
                </tr>
                @endif

            </tbody>
@endif