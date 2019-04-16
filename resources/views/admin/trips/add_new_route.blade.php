@extends('admin.layouts.master')
@section('trips_active', 'active')
@section('trips_new_route_active', 'active')
@section('title', 'Add new route')
@section('top-header')
<style>
    .input-group .form-control {
        z-index : initial;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>CREATE ROUTE</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        ADD NEW TRIP ROUTE
                        <small>Choose source, destination and trip fare details.</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="add-new-route-form" action="" method="POST">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <b>Source Point</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">flight_takeoff</i>
                                    </span>
                                    <div class="form-line">
                                        <select class="form-control show-tick" name="from_location">
                                            @foreach($locations as $location)
                                            <option value="{{$location->id}}">{{$location->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <b>Destination Point</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">flight_land</i>
                                    </span>
                                    <div class="form-line">
                                        <select class="form-control show-tick" name="to_location">
                                            @foreach($locations as $location)
                                            <option value="{{$location->id}}">{{$location->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b>Base Fare</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        {{$currency_symbol}}
                                    </span>
                                    <div class="form-line">
                                        <input type="number" class="form-control" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="base_fare" value="0.00">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b>Tax Fee</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        {{$currency_symbol}}
                                    </span>
                                    <div class="form-line">
                                        <input type="number" class="form-control" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="tax_fee" value="0.00">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b>Access Fee</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        {{$currency_symbol}}
                                    </span>
                                    <div class="form-line">
                                        <input type="number" class="form-control" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="access_fee" value="0.00">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b>Total</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        {{$currency_symbol}}
                                    </span>
                                    <div class="form-line">
                                        <input type="number" class="form-control" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)" name="total_fare" style="cursor: not-allowed;font-size: 25px;font-weight: 700;" value="0.00">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <b>Aprox. Trip Time Duration</b>
                                <div style="display:flex;">
                                    <i class="material-icons" style="padding: 6px 12px;padding-left: 0;">access_time</i>
                                    <div class="input-group" style="width: 120px;">
                                        <div class="form-line">
                                            <input type="number" class="form-control" min="1" max="99" name="aprox_time_hour" value="01" step="1" onchange="if(parseInt(this.value,10)<10)this.value='0'+this.value;" required>
                                        </div>
                                        <span class="input-group-addon">Hour</span>
                                    </div>
                                    <span style="padding: 6px 12px;font-weight: bold;padding-top:12px;">:</span>
                                    <div class="input-group" style="width: 120px;">
                                        <div class="form-line">
                                            <input type="number" class="form-control" min="0" max="99" name="aprox_time_min" value="00" step="1" onchange="if(parseInt(this.value,10)<10)this.value='0'+this.value;" required>
                                        </div>
                                        <span class="input-group-addon">Min.</span>
                                    </div>
                                </div>
                            </div>
                            
                            
                        </div>
                       
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn bg-pink waves-effect" id="add-route">
                                <i class="material-icons">save</i>
                                <span>CREATE</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
@endsection
@section('bottom')
<script>
var fareOnblurCallback = function(){
        
    var base_fare = parseFloat($("input[name='base_fare']").val())
    var tax_fee = parseFloat($("input[name='tax_fee']").val())
    var access_fee = parseFloat($("input[name='access_fee']").val())
    var total = (base_fare + access_fee + tax_fee).toFixed(2)
    console.log(base_fare)
    $("input[name='total_fare']").val(total)
}

$(document).ready(function(){
    

    $("input[name='base_fare']").on('blur', fareOnblurCallback)
    $("input[name='tax_fee']").on('blur', fareOnblurCallback)
    $("input[name='access_fee']").on('blur', fareOnblurCallback)


    $("#add-new-route-form").on('submit', function(event){
        event.preventDefault()
        var data = $(this).serializeArray();
        console.log(data)

        $.post("{{route('admin.add_new_route')}}", data, function(response){

            if(response.success) {
                $("#add-new-location-modal").modal('hide')
                showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                
                setTimeout(function(){
                    window.location.href = "{{route('admin.show-all-routes')}}"
                }, 1000)
                
            } else  {
                showNotification('bg-red', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
            }
        })
        .fail(function(){
            swal("Internal server error. Try later.", "", "error");
        });


    })


})
</script>
@endsection