@extends('admin.layouts.master')
@section('trips_active', 'active')
@section('trips_add_route_active', 'active')
@section('title', 'Add new trip route')
@section('top-header')
<style>
    .address-dot
    {
    color:green !important;
    }
    .dd 
    {
    float : none;
    }
    .dd .card
    {
    margin-bottom : 0;
    }
    .dd .dd3-content
    {
    padding:0px;
    }
    .dd .dd3-handle
    {
    z-index : 1;
    }
    .trip-point-div
    {
    border: 1px solid rgba(204, 204, 204, 0.35);
    padding-top: 20px;
    margin-left: 0px;
    margin-right: 0px;
    position:relative;
    }
    .point-bottom-arrow
    {
    margin: 0px !important; 
    }
    .point-bottom-arrow > i 
    {
        position: absolute;
        top: -9px;
        color: green;
        background: #0000000d;
    }
    .trip-point-div:last-child
    {
        display:none !important;
    }
    .point-title
    {
        display: inline-block;
        position: absolute;
        left: 0px;
        padding: 5px;
        background: black;
        color: white;
        top: -10px;
        font-size: 10px;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>TRIP ROUTE</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        ADD NEW TRIP ROUTE
                        <small>Choose source, destination and intermediate pickup points. Set time, distance, estimated fare also.</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="add-new-route-form" action="{{route('admin.add-new-route')}}" method="POST">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <b>Trip Name</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">text_format</i>
                                    </span>
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Ex: Pune-Mumbai Express" value="{{ old('name') }}" name="name" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <label> Trip Pionts</label>
                                <small>(Add trip points in order)</small>
                                <i id="add-new-point"title="Add new intermediate point" class="material-icons" style="vertical-align: middle;cursor: pointer;">add</i>
                                <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Enter details of each intermediate points. Calculate and enter fare, time and distance from previous intermediate. That's why first point does not have fare, distance and time field.">help_outline</i>
                            </div>
                        </div>
                        <div class="row clearfix trip-point-div" id="trip_point_s" data-point-order="1">
                            <div class="point-title">Point 1</div>
                            <div class="col-md-6">
                                <b> Address</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons address-dot">fiber_manual_record</i>
                                    </span>
                                    <div class="form-line">
                                        <input  required name="points[0][address]" type="text" class="form-control" placeholder="Ex: Hosur road, GB Playa, Near RNS motors, Garvebhavi Palya, Bengaluru, Karnataka 560068" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b> Latitude</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input step="any"  required name="points[0][latitude]" type="number" class="form-control" placeholder="Ex: 12.8957554" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b> Longitude</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input step="any"  required name="points[0][longitude]" type="number" class="form-control" placeholder="Ex: 12.8957554" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-center point-bottom-arrow">
                                <i class="material-icons">arrow_downward</i>
                            </div>
                        </div>
                        <div class="row clearfix trip-point-div" id="trip_pont_d" data-point-order="2">
                            <div class="point-title">Point 2</div>
                            <div class="col-md-8">
                                <b> Address</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons address-dot">fiber_manual_record</i>
                                    </span>
                                    <div class="form-line">
                                        <input  required name="points[1][address]" type="text" class="form-control" placeholder="Ex: Hosur road, GB Playa, Near RNS motors, Garvebhavi Palya, Bengaluru, Karnataka 560068" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b> Fare({{$currency_symbol}})</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">monetization_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input step="any"  required name="points[1][fare]" type="number" class="form-control" placeholder="Ex: 12.89" value="" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b> Latitude</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input step="any" required name="points[1][latitude]" type="number" class="form-control" placeholder="Ex: 12.8957554" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b> Longitude</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">location_on</i>
                                    </span>
                                    <div class="form-line">
                                        <input step="any" required name="points[1][longitude]" type="number" class="form-control" placeholder="Ex: 12.8957554" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b> Distance(Metre)</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">space_bar</i>
                                    </span>
                                    <div class="form-line">
                                        <input  required name="points[1][distance]" type="number" class="form-control" placeholder="Ex: 10" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <b> Time(Minutes)</b>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                    <i class="material-icons">timer</i>
                                    </span>
                                    <div class="form-line">
                                        <input  required name="points[1][time]" type="number" class="form-control" placeholder="Ex: 10" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-center point-bottom-arrow">
                                <i class="material-icons">arrow_downward</i>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn bg-pink waves-effect" id="add-route">
                                <i class="material-icons">save</i>
                                <span>ADD</span>
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
    $(document).ready(function(){
        
        hideLastBottomArrow();
    
        $("#add-new-route-form").on('submit', function(event){

            event.preventDefault();
            
            var data = $("#add-new-route-form").serializeArray();

            console.log(data);

            $.post("{{route('admin.add-new-route')}}", data, function(response){
                if(response.success) {
                    showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                } else {

                    for (var property in response.data.errors) {
                        if (response.data.errors.hasOwnProperty(property)) {
                            showNotification('bg-red', response.data.errors[property], 'top', 'right', 'animated flipInX', 'animated flipOutX');
                            break;
                        }
                    }

                    showNotification('bg-red', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                }
            });

        })
    
    
        $("#add-new-point").on('click', function(){
            
            var elems = $(".trip-point-div");
            console.log(elems)
            var clonedElem = $(elems[1]).clone();
            clonedElem.removeAttr('id')
            clonedElem.hide();
            $(elems[elems.length - 2]).after(clonedElem)
            clonedElem.fadeIn();
    
            arrangePionts();

            hideLastBottomArrow();

            showNotification('bg-black', 'New intermediate point added', 'bottom', 'center', 'animated flipInX', 'animated flipOutX');
    
        })
    
     
    })


    function hideLastBottomArrow()
    {
        var elems = $(".point-bottom-arrow");
        elems.each(function(index, item){
            if(index == elems.length -1 ) {
                $(item).hide();
                return;
            }

            $(item).show();
        })  
    }

    
    function arrangePionts()
    {
        var tripPointElements = $(".trip-point-div");
        var pointsLength = tripPointElements.length;
    
        var addressElem;
        var latitudeElem;
        var longitudeElem;
        var distanceElem;
        var timeElem;
        var elem;
    
        for(var i = 1; i <= pointsLength; i++) {
    
            console.log(tripPointElements[i-1]);
            var elem = $(tripPointElements[i-1]);
            
            elem.attr('data-point-order', i);
            
            elem.find('.point-title').text('Point ' + i)
    
            var addressElem = elem.find('input[name$="[address]"]');
            var latitudeElem = elem.find('input[name$="[latitude]"]');
            var longitudeElem = elem.find('input[name$="[longitude]"]');
            var distanceElem = elem.find('input[name$="[distance]"]');
            var timeElem = elem.find('input[name$="[time]"]');
            var fareElem = elem.find('input[name$="[fare]"]');
    
            if(addressElem.length) {
                addressElem.attr('name', 'points['+(i-1)+'][address]')
            }
    
            if(latitudeElem.length) {
                latitudeElem.attr('name', 'points['+(i-1)+'][latitude]')
            }
    
            if(longitudeElem.length) {
                longitudeElem.attr('name', 'points['+(i-1)+'][longitude]')
            }
    
            if(distanceElem.length) {
                distanceElem.attr('name', 'points['+(i-1)+'][distance]')
            }
    
            if(timeElem.length) {
                timeElem.attr('name', 'points['+(i-1)+'][time]')
            }
    
            if(fareElem.length) {
                fareElem.attr('name', 'points['+(i-1)+'][fare]')
            }
    
        }
        
    
    }
    
</script>
@endsection