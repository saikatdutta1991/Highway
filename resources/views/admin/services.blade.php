@extends('admin.layouts.master')
@section('title', 'Services')
@section('services_active', 'active')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
<style>
.header-buttons {
    position: relative !important;
    padding-left:15px;
    margin-top:15px;
    top:initial !important;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>SERVICES</h2>
    </div>
    
    <!-- With Material Design Colors -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        LIST OF ALL SERVICES
                        <small>Here you can see all services, add, edit, update services</small>
                    </h2>
                    <ul class="header-dropdown m-r--5 header-buttons">
                        <li>
                            <button id="add-service-btn" type="button" data-toggle="tooltip" data-placement="left" title="Add new service" class="font-bold btn bg-red btn-block btn-xs waves-effect">
                                <i class="material-icons col-pink" style="vertical-align:middle;top:0px;font-size: 17px;">add</i>New Service
                            </button>
                        </li>
                        <li>
                            <button type="button" id="tax-percentage-button" data-toggle="tooltip" data-placement="left" title="Set ride service tax percentage" class="font-bold btn bg-red btn-block btn-xs waves-effect">
                                <i class="material-icons col-pink" style="vertical-align:middle;top:0px;font-size: 17px;">add</i>Service Tax
                            </button>
                        </li>
                        <li>
                            <button type="button" id="cancellation-charge-button" data-toggle="tooltip" data-placement="left" title="Set ride cancellation charge" class="font-bold btn bg-red btn-block btn-xs waves-effect">
                            <i class="material-icons col-pink" style="vertical-align:middle;top:0px;font-size: 17px;">add</i>Cancellation Charge
                            </button>
                        </li>
                        <li>
                            <button type="button" id="driver-cancel-ride-limit" data-toggle="tooltip" data-placement="left" title="Set driver cancel ride request limit" class="font-bold btn bg-red btn-block btn-xs waves-effect">
                            <i class="material-icons col-pink" style="vertical-align:middle;top:0px;font-size: 17px;">add</i>Driver Ride Cancel Limit
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="body table-responsive">
                    @if(!count($services))
                    <div class="alert bg-pink">
                        No services found
                    </div>
                    @else
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>SERVICE NAME</th>
                                <th>Order Number</th>
                                <th>CREATED</th>
                                <th>NO. DRIVERS</th>
                                <th>HIGHWAY</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                            <tr id="service_row_{{$service['id']}}" data-service-name="{{$service['name']}}">
                                <td>{{$service['id']}}</td>
                                <td>{{$service['name']}}</td>
                                <td class="order">{{isset($service['order']) ? $service['order'] : 0}}</td>
                                <td>{{date('d M, Y', strtotime($service['created_at']))}}</td>
                                <td>{{$service['used_by_driver']}}</td>
                                <td>
                                    <div class="switch enable-highway-switch">
                                        <label><input type="checkbox" data-service-id="{{$service['id']}}" @if(isset($service['is_highway_enabled']) && $service['is_highway_enabled']==1) checked @endif><span class="lever switch-col-deep-orange"></span></label>
                                    </div>
                                </td>
                                <td style="">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn bg-pink btn-xs waves-effect dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <i class="material-icons">view_list</i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            <li class="service-edit-btn" data-service-id="{{$service['id']}}"><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-green">mode_edit</i>Edit</a></li>
                                            <li class="service-set-order-btn" data-service-id="{{$service['id']}}"><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-green">list</i>Set Order</a></li>
                                            <li class="@if($service['used_by_driver']) disabled @endif service-delete-btn" @if($service['used_by_driver']) data-toggle="tooltip" data-placement="left" title="Service can't be deleted because drivers are registed with this service already" @endif data-service-id="{{$service['id']}}"><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-red">delete</i>Delete</a></li>
                                            <li class="service_fare_btn" data-service-id="{{$service['id']}}"><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-blue">attach_money</i>Service Fare</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>
<div class="modal fade" id="service_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="smallModalLabel">ADD OR UPDATE SERVICE</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <form id="service_add_form">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="form-line">
                                    <b>Service Name</b>
                                    <input type="hidden" class="form-control" name="_token" value="{{csrf_token()}}">
                                    <input type="hidden" class="form-control" name="service_id" value="">
                                    <input type="hidden" class="form-control" name="_action" value="">
                                    <input type="text" class="form-control" placeholder="Ex: Prime" name="service_name" onkeyup="this.value=this.value.charAt(0).toUpperCase() + this.value.slice(1)">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="col-sm-12" id="add-service-error-div" style="display:none">
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
                        <h6 class="res-text" style="float:left;display:none">Sending ...</h6>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" id="service-save-btn">SAVE</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>

<!-- ride fare -->
<div class="modal fade" id="service_fare_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">SERVICE INTRACITY FARE DETAILS</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <form id="service_fare_add_form">
                        <div class="col-sm-12">
                            <div class="form-group form-float disabled">
                                <div class="form-line">
                                    <input type="text" class="form-control col-grey" value=" " name="service_name" disabled>
                                    <input type="hidden" class="form-control col-grey" value="" name="service_id">
                                    <input type="hidden" class="form-control col-grey" value="{{csrf_token()}}" name="_token">
                                    <label class="form-label">Service Name</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="number" class="form-control" value="0.00" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="minimun_price">
                                    <label class="form-label">Mininum Price({{$currency_symbol}})</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="number" class="form-control" value="0.00" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="base_price">
                                    <label class="form-label">Base Price({{$currency_symbol}})</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="number" class="form-control" value="0.00" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="access_fee">
                                    <label class="form-label">Acess Fee({{$currency_symbol}})</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="number" class="form-control" value="0.00" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="wait_time_price">
                                    <label class="form-label">Wait Time Charge({{$currency_symbol}})</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 p-l-0 p-r-0">
                           <!--  <hr> -->
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon">First</span>
                                    <div class="form-line">
                                        <input type="number" class="form-control" value="0" min="0" onblur="this.value=parseInt(this.value)" name="first_distance">
                                    </div>
                                    <span class="input-group-addon">{{$distance_unit}}</span>
                                </div>
                            </div>
                            
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="form-line">
                                        <input type="number" class="form-control" value="0.00" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="first_distance_price"> 
                                    </div>
                                    <span class="input-group-addon">{{$currency_symbol}}</span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="input-group">
                                    <span class="input-group-addon">then</span>
                                    <div class="form-line">
                                        <input type="number" class="form-control" value="0.00" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="after_first_distance_price">
                                    </div>
                                    <span class="input-group-addon">{{$currency_symbol}}/{{$distance_unit}}</span>
                                </div>
                            </div>
                            <!-- <hr> -->
                        </div>
                        
                    </form>
                    <div class="col-sm-12" id="service-fare-error-div" style="display:none">
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
                        <h6 class="res-text" style="float:left;display:none">Sending ...</h6>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" id="service-fare-save-btn">SAVE</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<!-- ride fare -->

<!-- intracity tax percentage -->
<div class="modal fade" id="ride_fare_tax_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="smallModalLabel">RIDE SERVICE TAX</h4>
                <small>This tax percentage will be added to any types of ride services</small>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <form id="ride_tax_form">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="number" class="form-control" value="{{$rideTaxPecentage}}" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="tax_percentage">
                                    <label class="form-label">Tax Percentage(%)</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" id="tax-percentage-save-btn">SAVE</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<!-- intracity tax percentage -->

<!-- cancellation charge modal -->
<div class="modal fade" id="cancellation_charge_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">CANCELLATION CHARGE SETTINGS</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <form id="cancellation_charge_add_form">
                        <input type="hidden" value="{{csrf_token()}}" name="_token">
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="number" class="form-control" value="{{$cancellationCharge}}" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="ride_request_cancellation_charge">
                                    <label class="form-label">Cancellation Charge({{$currency_symbol}})</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="number" class="form-control" value="{{$cancellationChargeAfterMinute}}" min="0" onblur="this.value=parseInt(this.value)" name="ride_request_cancellation_charge_after_minute_trip_started">
                                    <label class="form-label">Cancellation charge will be applied after minute(s)</label>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="col-sm-12" id="cancellation-charge-error-div" style="display:none">
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
                        <h6 class="res-text" style="float:left;display:none">Saving ...</h6>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" id="cancellation-charge-save-btn">SAVE</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<!-- end cancellation charge modal -->

<!-- driver cancel ride limit modal -->
<div class="modal fade" id="driver_cancel_ride_limit_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">DRIVER CANCEL RIDE REQUEST LIMIT</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <form id="driver_cancel_ride_limit_form">
                        <input type="hidden" value="{{csrf_token()}}" name="_token">
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="number" required class="form-control" value="{{$driver_cancel_ride_request_limit}}" min="0" step="1" onblur="this.value=parseInt(this.value)" name="driver_cancel_ride_request_limit">
                                    <label class="form-label">Maximum Cancel Limit</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" id="driver-cancel-ride-limit-save-btn">SAVE</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<!-- driver cancel ride limit modal end -->

@endsection
@section('bottom')
<script>

    

    var service_add_url = "{{url('admin/services/add')}}";
    var service_fare_base = "{{url('admin/services')}}";
    var csrf_token = "{{csrf_token()}}";
    var save_tax_percentage = "{{url('admin/services/tax/save')}}";
    var save_cancellation_charge = "{{url('admin/services/cancellation-charge/save')}}";
    var save_drirver_ride_cancel_limit = "{{route('admin.service.driver-cancel-ride-request-limit')}}";


    $(".enable-highway-switch input[type='checkbox']").on('change', function(){

        var btnELem = $(this);       
        var enable = $(this).is(":checked") ? 'true' : 'false';
        var service_id = btnELem.data('service-id')
        var service_name = $('#service_row_'+service_id).data('service-name')

        var data = {
            service_id : service_id,
            service_name : service_name,
            enable : enable,
            _token : csrf_token,
            _action : 'enable_highway'
        };

        $.post(service_add_url, data, function(response){
            if(response.success) {
                return;
            } 
            btnELem.prop('checked', !btnELem.is(":checked"))

        }).fail(function(){
            swal("Internal server error. Try later.", "", "error");
            btnELem.prop('checked', !btnELem.is(":checked"))
        });


    })




    $(".service-set-order-btn").on('click', async function(){
    
        var btnELem = $(this);

        swal({
            title: "",
            text: "Set Service Order Manually",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            animation: "slide-from-top",
            inputPlaceholder: "Enter service order number"
        }, function(inputValue){
            
            if (inputValue === false) return false;

            if (inputValue === "" || Number.isInteger(inputValue)) {
                swal.showInputError("You need to type integer");
                return false
            }


            var service_id = btnELem.data('service-id')
            var service_name = $('#service_row_'+service_id).data('service-name')

            var data = {
                service_id : service_id,
                service_name : service_name,
                order : inputValue,
                _token : csrf_token,
                _action : 'set_order'
            };
            $.post(service_add_url, data, function(response){

                if(response.success) {
                    $('#service_row_'+service_id).find('.order').text(inputValue)
                    swal("Service order updated successfully", "", "success"); 
                } 

            }).fail(function(){
                swal("Internal server error. Try later.", "", "error");
            });


            //swal.close(); 
        
        });



    })








    $("#tax-percentage-button").on('click', function(){
        $("#ride_fare_tax_modal").modal('show')
    })

    $("#cancellation-charge-button").on('click', function(){
        $("#cancellation_charge_modal").modal('show')
    })

    $("#driver-cancel-ride-limit").on('click', function(){
        $("#driver_cancel_ride_limit_modal").modal('show')
    })
    
    

    $("#driver-cancel-ride-limit-save-btn").on('click', function(){

        var data = $('#driver_cancel_ride_limit_form').serializeArray();
        console.log(data)
        $.post(save_drirver_ride_cancel_limit, data, function(response){

            if(response.success) {
                $("#driver_cancel_ride_limit_modal").modal('hide')
                showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                return;
            } 

            showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
        }).fail(function(){
            showNotification('bg-black', 'Internal server error. Contact to developer', 'top', 'right', 'animated flipInX', 'animated flipOutX');
        })

    })

    $("#cancellation-charge-save-btn").on('click', function(){

        var data = $('#cancellation_charge_add_form').serializeArray();
        console.log(data)
        $.post(save_cancellation_charge, data, function(response){

            if(response.success) {
                $("#cancellation_charge_modal").modal('hide')
                showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                return;
            } 

            showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
        }).fail(function(){
            showNotification('bg-black', 'Internal server error. Contact to developer', 'top', 'right', 'animated flipInX', 'animated flipOutX');
        })

    })


    $("#tax-percentage-save-btn").on('click', function(){

        var data = $('#ride_tax_form').serializeArray();
        console.log(data)
        $.post(save_tax_percentage, data, function(response){

            if(response.success) {
                $("#ride_fare_tax_modal").modal('hide')
                showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                return;
            } 

            showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
        }).fail(function(){
            showNotification('bg-black', 'Internal server error. Contact to developer', 'top', 'right', 'animated flipInX', 'animated flipOutX');
        })

    })
    

    $("#service-fare-save-btn").on('click', function(){
        var servicFareForm = $("#service_fare_add_form");
        var data = servicFareForm.serializeArray()
        var service_id = servicFareForm.find("input[name='service_id']").val()
        var url = service_fare_base+'/'+service_id+'/ridefare';

        showServiceFareLoader('Saving ...');

        $.post(url, data, function(response){

            if(response.success) {
                showServiceFareSuccess('Ride fare saved successfully');
                return;
            } 

            showServiceFareError(response.text);

        }).fail(function(){
            showServiceFareError('Internal server error. Try again reloading the page.');
        })

    })

    function serviceRideFareFormValues(servicFareForm, mp, af, bp, fd, fdp, afdp, wtp)
    {
        console.log(mp)
        servicFareForm.find("input[name='minimun_price']").val(mp)
        servicFareForm.find("input[name='access_fee']").val(af)
        servicFareForm.find("input[name='base_price']").val(bp)
        servicFareForm.find("input[name='first_distance']").val(fd)
        servicFareForm.find("input[name='first_distance_price']").val(fdp)
        servicFareForm.find("input[name='after_first_distance_price']").val(afdp)
        servicFareForm.find("input[name='wait_time_price']").val(wtp)
    }

    $(".service_fare_btn").on('click', function(){

        var service_id = $(this).data('service-id');
        var service_name = $("#service_row_"+service_id).data('service-name');

        console.log(service_id, service_name)

        $("#service_fare_modal").modal('show')

        var servicFareForm = $("#service_fare_add_form");

        servicFareForm.find("input[name='service_name']").val(service_name)
        servicFareForm.find("input[name='service_id']").val(service_id)

        showServiceFareLoader('Fetching ...');

        var url = service_fare_base+'/'+service_id+'/ridefare';
        $.get(url, function(response){
            console.log(response)

            hideServiceFareErrorResDiv();

            if(response.success) {
                serviceRideFareFormValues(servicFareForm, response.data.ride_fare.minimun_price, 
                response.data.ride_fare.access_fee, 
                response.data.ride_fare.base_price,
                response.data.ride_fare.first_distance,
                response.data.ride_fare.first_distance_price,
                response.data.ride_fare.after_first_distance_price,
                response.data.ride_fare.wait_time_price)
                
                return;
            } 
            
            //set default fare values
            serviceRideFareFormValues(servicFareForm, '0.00', '0.00', '0.00', '0', '0.00', '0.00', '0.00')

        }).fail(function(){
            //set fare values
            serviceRideFareFormValues(servicFareForm, '0.00', '0.00', '0.00', '0', '0.00', '0.00', '0.00')
            hideServiceFareErrorResDiv();
        })

      
    })


   
    var paddingBottom = 0;
    $('.table-responsive').on('shown.bs.dropdown', function (e) {
        var $table = $(this),
            $menu = $(e.target).find('.dropdown-menu'),
            tableOffsetHeight = $table.offset().top + $table.height(),
            menuOffsetHeight = $menu.offset().top + $menu.outerHeight(true);

        paddingBottom = $(this).css("padding-bottom");
        
        if (menuOffsetHeight > tableOffsetHeight)
            $table.css("padding-bottom", menuOffsetHeight - tableOffsetHeight + 50);
    });

    $('.table-responsive').on('hide.bs.dropdown', function () {
        $(this).css("padding-bottom", paddingBottom);
    })
    


    
    


    $(".service-delete-btn").on('click', function(){
    
        //if list item disbled then dont do anything
        if($(this).hasClass('disabled')) {
            return;
        }
    
    
        var btnELem = $(this);
    
    
        swal({
            title: "Are you sure to delete this service?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete",
            cancelButtonText: "No, cancel",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function (isConfirm) {
            if (!isConfirm) {
                return false;
            } 
    
           
            var service_id = btnELem.data('service-id')
            var service_name = $('#service_row_'+service_id).data('service-name')
    
            var data = {
                service_id : service_id,
                service_name : service_name,
                _token : csrf_token,
                _action : 'delete'
            };
            $.post(service_add_url, data, function(response){
    
                if(response.success) {
                    $("#service_modal").modal('hide');
                    swal("Service Deleted successfully", "", "success"); 
    
                    $('#service_row_'+service_id).fadeOut();
                    
                } else if(!response.success && response.type == 'INTERNAL_SERVER_ERROR'){
                    swal(response.text, "", "error");
                } else if(!response.success && response.type == 'MISSING_PARAMTERS') {
                    swal(response.text, "", "error");
                }
    
    
            }).fail(function(){
                swal("Internal server error. Try later.", "", "success");
            });
    
                
            swal.close();        
            return true;
        });
    
    
    
    
    })
    
    
    $("#add-service-btn").on('click', function(){
        hideMessageErrorResDiv()
        $("#service_modal input[name='service_id']").val('')
        $("#service_modal input[name='service_name']").val('')
        $("#service_modal input[name='_action']").val('add')
        $("#service_modal").modal('show');
    
        setTimeout(function(){
            $("#service_modal input[name='service_name']").focus()
        },500)
    
    })
    
    
    $(".service-edit-btn").on('click',function(){
        hideMessageErrorResDiv()
        var service_id = $(this).data('service-id');
        var service_name = $('#service_row_'+service_id).data('service-name')
        $("#service_modal input[name='service_id']").val(service_id)
        $("#service_modal input[name='service_name']").val(service_name)
        $("#service_modal input[name='_action']").val('update')
        $("#service_modal").modal('show');
        setTimeout(function(){
            $("#service_modal input[name='service_name']").focus().select()
        },500)
        
    });
    
    
    
    $("#service-save-btn").on('click', function(){
    
        var data = $("#service_add_form").serializeArray();
    
        console.log(data)
    
        showServiceAddLoader('Saving ...')
    
        $.post(service_add_url, data, function(response){
    
            if(response.success) {
                $("#service_modal").modal('hide');
                swal("Service saved. Wait till services refresh", "", "success"); 
    
                setTimeout(function(){
                    window.location.reload()
                },2000)
                
            } else {
                showServiceAddError(response.text);
            }
    
    
        }).fail(function(){
            showServiceAddError('Internal server error. Try later.')
        });
    
    })
    
    
    
    function hideMessageErrorResDiv()
    {
        $("#add-service-error-div").hide();
        $("#add-service-error-div > .preloader").hide()
        $("#add-service-error-div > .res-text").hide()
    }
    
    function showServiceAddLoader(message)
    {
        $("#add-service-error-div").show();
        $("#add-service-error-div > .preloader").show()
        $("#add-service-error-div > .res-text").show().text(message).removeClass('col-red').addClass('col-black');
    }
    
    function showServiceAddError(message)
    {
        $("#add-service-error-div").show();
        $("#add-service-error-div > .preloader").hide()
        $("#add-service-error-div > .res-text").show().text(message).addClass('col-red').removeClass('col-black');
    }


    function hideServiceFareErrorResDiv()
    {
        $("#service-fare-error-div").hide();
        $("#service-fare-error-div > .preloader").hide()
        $("#service-fare-error-div > .res-text").hide()
    }
    
    function showServiceFareLoader(message)
    {
        $("#service-fare-error-div").show();
        $("#service-fare-error-div > .preloader").show()
        $("#service-fare-error-div > .res-text").show().html(message).removeClass('col-red').addClass('col-black').removeClass('col-green');
    }
    
    function showServiceFareError(message)
    {
        $("#service-fare-error-div").show();
        $("#service-fare-error-div > .preloader").hide()
        $("#service-fare-error-div > .res-text").show().html('<i style="vertical-align:middle" class="material-icons">error_outline</i>'+message).addClass('col-red').removeClass('col-black').removeClass('col-green');
    }

    function showServiceFareSuccess(message)
    {
        $("#service-fare-error-div").show();
        $("#service-fare-error-div > .preloader").hide()
        $("#service-fare-error-div > .res-text").show().html('<i style="vertical-align:middle" class="material-icons">check_circle</i>'+message).addClass('col-green').removeClass('col-black');   
    }
    
</script>
@endsection