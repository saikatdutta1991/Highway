@extends('admin.layouts.master')
@section('title', 'Payouts-Filter')
@section('payouts_active', 'active')
@section('driver_active', 'active')
@section('top-header')
<!-- Bootstrap Material Datetime Picker Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />
<!-- JQuery DataTable Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">
<style>
.dataTables_wrapper input[type="search"] {
    border: 1px solid #ddd;
    border-radius : 5px;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>DRIVER PAYOUT FILTER</h2>
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
                                    <input type="text" required name="from_date" class="form-control datetimepicker" placeholder="Ex: 31/12/1990"  value="{{$fromDate}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">date_range</i>
                                </span>
                                <div class="form-line">
                                <input type="text" required name="to_date" class="form-control datetimepicker" placeholder="Ex: 31/12/1990"  value="{{$toDate}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">select_all</i>
                                </span>
                                <div class="form-line">
                                    <select class="form-control show-tick" name="ride_type">
                                        <option value = "city_rides" @if($cityRides && !$highwayRides) selected @endif>City Rides</option>
                                        <option value = "highway_rides" @if(!$cityRides && $highwayRides) selected @endif>Highway Rides</option>
                                        <option value = "both_city_highway_rides" @if($cityRides && $highwayRides) selected @endif>City & Highway Rides</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">phone</i>
                                </span>
                                <div class="form-line">
                                    <input type="text" name="full_mobile_number" class="form-control" value="{{$full_mobile_number}}" placeholder="Phone number Ex:+919093036897(Optional)">
                                </div>
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
                    <i class="material-icons" style="vertical-align: middle;">view_list</i>
                    DRIVERS PAYOUTS BETWEEN {{$fromDate}} - {{$toDate}} -- RECORDS FOUND {{$totalRecords}}
                </h2>
            </div>
            <div class="body table-responsive">
                <table class="table table-condensed table-hover" id="data-table">
                    <thead>
                        <tr>
                            <th>Slr. no</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Vehicle No.</th>
                            <th>Vehicle Type</th>
                            <th>Rating</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Ride Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
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
                                <td>{{$vehicleTypes->where('code', $ride['vehicle_type'])->first()['name']}}</td>
                                <th>{{$ride['driver_rating']}}</td>
                                <td>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ride['date'], 'UTC')->setTimezone($default_timezone)->format('d/m/Y')}}</td>
                                <td>@if($ride['ride_start_time']){{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ride['ride_start_time'], 'UTC')->setTimezone($default_timezone)->format('h:i A')}}@endif</td>
                                <td>@if($ride['ride_end_time']){{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ride['ride_end_time'], 'UTC')->setTimezone($default_timezone)->format('h:i A')}}@endif</td>
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
                                <td>{{$vehicleTypes->where('code', $ride['vehicle_type'])->first()['name']}}</td>
                                <th>{{$ride['driver_rating']}}</td>
                                <td>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ride['date'], 'UTC')->setTimezone($default_timezone)->format('d/m/Y')}}</td>
                                <td>@if($ride['ride_start_time']){{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ride['ride_start_time'], 'UTC')->setTimezone($default_timezone)->format('h:i A')}}@endif</td>
                                <td>@if($ride['ride_end_time']){{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ride['ride_end_time'], 'UTC')->setTimezone($default_timezone)->format('h:i A')}}@endif</td>
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
<!-- Moment Plugin Js -->
<script src="{{url('admin_assets/admin_bsb')}}/plugins/momentjs/moment.js"></script>
<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
<!-- Jquery DataTable Plugin Js -->
<script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-datatable/jquery.dataTables.js"></script>
<script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
<script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
<script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
<script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
<script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
<script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
<script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
<script src="{{url('admin_assets/admin_bsb')}}/plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>
<script>

    @if($totalRecords)
    let title = "DRIVERS_PAYOUTS_BETWEEN_{{$fromDate}}_{{$toDate}}";
    $('title').html(title);
    @endif

    function hideSideBar() 
    {
        $('body').addClass('ls-closed');
        setTimeout(() => {
            $(".bars").css('display', 'block');
            $('.navbar-brand').attr('style', 'margin-left:20px !important')
        }, 500);
    }


    $(document).ready(()=>{
        //hideSideBar();

        $('.datetimepicker').bootstrapMaterialDatePicker({
            format: 'DD/MM/YYYY',
            time:false
        });

        $('#data-table').DataTable( {
            dom: 'Bfrtip',
            pageLength: 100,
            aLengthMenu: [[25, 50, 75, -1], [25, 50, 75, "All"]],
            responsive: true,
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        });

    });    
</script>
@endsection