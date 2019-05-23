@extends('admin.layouts.master')
@section('title', 'Payouts-Filter')
@section('payouts_active', 'active')
@section('top-header')
<style></style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>PAYOUT FILTER</h2>
</div>

<!-- filter form -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2><i class="material-icons" style="vertical-align: middle;">search</i>FILTER INPUTS</h2>
            </div>
            <div class="body">
                <form action="{{route('admin.payouts.show')}}" method="GET">
                    <div class="row clearfix">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">date_range</i>
                                </span>
                                <div class="form-line">
                                    <input type="text" required name="from_date" class="form-control date" placeholder="From Date - Ex: 31/12/2016" value="{{$fromDate}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">date_range</i>
                                </span>
                                <div class="form-line">
                                    <input type="text" required name="to_date" class="form-control date" placeholder="To Date - Ex: 31/12/2016" value="{{$toDate}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="checkbox" id="city_rides_checkbox" class="filled-in chk-col-red" name="city_rides" @if($cityRides) checked @endif> 
                                <label for="city_rides_checkbox">City Rides</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="checkbox" id="highay_rides_checkbox" class="filled-in chk-col-red" name="highway_rides" @if($highwayRides) checked @endif>
                                <label for="highay_rides_checkbox">Highway Rides</label>
                            </div>
                        </div>
                    </div>
                        
                    <div class="row clearfix">
                        <div class="col-sm-12" style="margin-bottom:0px;">
                            <button type="submit" class="btn bg-pink waves-effect btn-block" name="submit" value="submit">
                            <i class="material-icons">search</i>
                            <span>FILTER</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- #END# With Material Design Colors -->
    </div>
</div>
<!-- filter form end -->




<!-- With Material Design Colors -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    Drivers rides between {{$fromDate}} - {{$toDate}}
                </h2>
            </div>
            <div class="body table-responsive">
                <table class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>Slr. no<br>(Unique Id)</th>
                            <th>Driver Name</th>
                            <th>Phone</th>
                            <th>Vehicle No.</th>
                            <th>Vehicle Type</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Type of Ride</th>
                            <th>From(Location)</th>
                            <th>To(Location)</th>
                            <th>Status<br>(Completed, Canceled)</th>
                            <th>Payment Mode</th>
                            <th>Base Fare</th>
                            <th>Access Fee</th>
                            <th>Tax</th>
                            <th>Referral / Coupon Discount</th>
                            <th>Cancellation Fee</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                            @foreach($record['city_rides'] as $ride)
                            <tr>
                                <td>{{$ride['driver_id']}}</td>
                                <td>{{$ride['fname']}} {{$ride['lname']}}</td>
                                <td>{{$ride['full_mobile_number']}}</td>
                                <td>{{$ride['vehicle_number']}}</td>
                                <td>{{$ride['vehicle_type']}}</td>
                                <td>{{$ride['date']}}</td>
                                <td>{{$ride['ride_start_time']}}</td>
                                <td>{{$ride['ride_end_time']}}</td>
                                <td>City</td>
                                <td>{{$ride['from_location']}}</td>
                                <td>{{$ride['to_location']}}</td>
                                <td>{{$statusCollection[$ride['status']]}}</td>
                                <td>{{$ride['payment_mode']}}</td>
                                <td>{{$ride['ride_fare']}}</td>
                                <td>{{$ride['access_fee']}}</td>
                                <td>{{$ride['tax']}}</td>
                                <td>- {{$ride['referral_bonus_discount']+$ride['coupon_discount']}}</td>
                                <td>{{$ride['cancellation_charge']}}</td>
                                <td>{{$ride['total']}}</td>
                            </tr>
                            @endforeach
                            @foreach($record['highway_rides'] as $ride)
                            <tr>
                                <td>{{$ride['driver_id']}}</td>
                                <td>{{$ride['fname']}} {{$ride['lname']}}</td>
                                <td>{{$ride['full_mobile_number']}}</td>
                                <td>{{$ride['vehicle_number']}}</td>
                                <td>{{$ride['vehicle_type']}}</td>
                                <td>{{$ride['date']}}</td>
                                <td>{{$ride['ride_start_time']}}</td>
                                <td>{{$ride['ride_end_time']}}</td>
                                <td>Highway</td>
                                <td>{{$ride['from_location']}}</td>
                                <td>{{$ride['to_location']}}</td>
                                <td>{{$statusCollection[$ride['status']]}}</td>
                                <td>{{$ride['payment_mode']}}</td>
                                <td>{{$ride['ride_fare']}}</td>
                                <td>{{$ride['access_fee']}}</td>
                                <td>{{$ride['tax']}}</td>
                                <td>- {{$ride['referral_bonus_discount']+$ride['coupon_discount']}}</td>
                                <td>{{$ride['cancellation_charge']}}</td>
                                <td>{{$ride['total']}}</td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- #END# With Material Design Colors -->
    </div>
</div>
@endsection
@section('bottom')
<script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>
<script>

    function hideSideBar() 
    {
        $('body').addClass('ls-closed');
        setTimeout(() => {
            $(".bars").css('display', 'block');
            $('.navbar-brand').attr('style', 'margin-left:20px !important')
        }, 500);
    }


    $(document).ready(()=>{
        hideSideBar();

        $('.date').inputmask('dd/mm/yyyy', { placeholder: '__/__/____' });

    });    
</script>
@endsection