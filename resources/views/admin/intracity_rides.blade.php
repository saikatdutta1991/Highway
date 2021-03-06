@extends('admin.layouts.master')
@section('title', 'City Rides')
@section('rides_active', 'active')
@section('intracity_rides_active', 'active')
@section('top-header')
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>CITY RIDES</h2>

    @if(request()->has('user_id')) 
    <small>City Rides of user : {{request()->name}}</small>
    @endif

    @if(request()->has('driver_id')) 
    <small>City Rides of driver : {{request()->name}}</small>
    @endif
</div>
<!-- Widgets -->
<div class="row clearfix">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-pink hover-expand-effect">
            <div class="icon">
                <i class="material-icons">send</i>
            </div>
            <div class="content">
                <div class="text">TOTAL</div>
                <div class="number count-to" data-from="0" data-to="{{$totalRides}}" data-speed="1000" data-fresh-interval="20"></div>
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
                <div class="number count-to" data-from="0" data-to="{{$completedRides}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-purple hover-expand-effect">
            <div class="icon">
                <i class="material-icons">cancel</i>
            </div>
            <div class="content">
                <div class="text">CANCELED</div>
                <div class="number count-to" data-from="0" data-to="{{$canceledRides}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green hover-expand-effect">
            <div class="icon">
                <i class="material-icons">autorenew</i>
            </div>
            <div class="content">
                <div class="text">ONGOING</div>
                <div class="number count-to" data-from="0" data-to="{{$ongointRides}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-pink hover-expand-effect">
            <div class="icon">
                <i class="material-icons">attach_money</i>
            </div>
            <div class="content">
                <div class="text">CASH RIDES</div>
                <div class="number count-to" data-from="0" data-to="{{$cashRides}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-pink hover-expand-effect">
            <div class="icon">
                <i class="material-icons">credit_card</i>
            </div>
            <div class="content">
                <div class="text">ONLINE RIDES</div>
                <div class="number count-to" data-from="0" data-to="{{$onlineRides}}" data-speed="1000" data-fresh-interval="20"></div>
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
                LIST OF ALL CITY RIDES
                <small>You can see all intracity rides, fitler and many more.</small>
            </h2>
        </div>
        <!-- #END# Select -->
        <div class="body table-responsive">
            @if($rides->count() == 0)
            <div class="alert bg-pink">
                No rides found
            </div>
            @else
            <table class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>USER</th>
                        <th>DRIVER</th>
                        <th>SERVICE</th>
                        <th>PICKUP</th>
                        <th>DROP</th>
                        <th>DATE</th>
                        <th>AMOUNT</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rides as $ride)
                    <tr>
                        <!-- <td>{{$ride->id}}</td> -->
                        <td>{{$ride->user->fullname()}}</td>
                        <td>{{$ride->driver->fullname()}}</td>
                        <td>{{$serviceTypes->where('code', $ride->ride_vehicle_type)->first()['name']}}</td>
                        <td style="max-width: 200px;">
                            <i class="material-icons col-green" style="font-size:10px;vertical-align: middle;">fiber_manual_record</i>
                            {{$ride->source_address}} <br>{{$ride->getStartTime($default_timezone)}}
                        </td>
                        <td style="max-width: 200px;">
                            <i class="material-icons col-red" style="font-size:10px;vertical-align: middle;">fiber_manual_record</i>
                            {{$ride->destination_address}} @if($ride->ride_end_time)<br>{{$ride->getEndTime($default_timezone)}}@endif
                        </td>
                        <td>
                            @if($ride->ride_end_time)
                            {{date('d M, Y', strtotime($ride->ride_end_time))}}
                            @else
                            {{date('d M, Y', strtotime($ride->created_at))}}
                            @endif
                        </td>
                        <td data-toggle="tooltip" data-placement="left"
                            @if($ride->invoice && $ride->invoice->payment_status == 'PAID') 
                                class="col-green" title="Paid by @if($ride->payment_mode=="CASH") cash @else online @endif"
                            @else 
                                class="col-red" title="Not paid"
                            @endif>
                            @if($ride->invoice){{$currency_symbol}}{{$ride->invoice->total}}@else {{$currency_symbol}}{{$ride->estimated_fare}} @endif
                        </td>
                        <td>{{$ride->ride_status}}</td>
                        <td>
                            <li class="dropdown" style="list-style: none;">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                                <i class="material-icons">more_vert</i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block view-driver-menu-item" data-driver-id="{{$ride->driver->id}}">View driver</a></li>
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block view-user-menu-item" data-user-id="{{$ride->user->id}}">View user</a></li>
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block user-rides-menu-item" data-user-id="{{$ride->user->id}}">Only this user rides</a></li>
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block driver-rides-menu-item" data-driver-id="{{$ride->driver->id}}">Only this driver rides</a></li>
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block view-in-details" data-ride-request-id="{{$ride->id}}">View in detail</a></li>
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block cancel-ride-menu-btn" data-ride-request-id="{{$ride->id}}">Cancel</a></li>
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block complete-ride-menu-btn" data-ride-request-id="{{$ride->id}}">Complete</a></li>
                                </ul>
                            </li>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            <div class="row pull-right">
                {!! $rides->appends(request()->all())->render() !!}
                
            </div>
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>

<!-- cancel ride modal -->
<div class="modal fade" id="cancel_ride_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">CANCEL RIDE REQUEST</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <form id="cance_ride_form">
                        <input type="hidden" value="{{csrf_token()}}" name="_token">
                        <input type="hidden" name="ride_reqeust_id" id="cancel_ride_reqeust_id">
                        <div class="col-sm-12">
                            <label for="cancel_ride_check_for_user">Cancel ride on behalf</label>
                            <div class="form-group">
                                <input name="on_behalf" class="with-gap radio-col-green" type="radio" id="cancel_ride_check_for_user" value="user" checked/>
                                <label for="cancel_ride_check_for_user">User</label>

                                <input name="on_behalf" class="with-gap radio-col-green" type="radio" id="cancel_ride_check_for_driver" value="driver"/>
                                <label for="cancel_ride_check_for_driver">Driver</label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <b>Remarks/Comment</b>
                                <div class="form-line">
                                    <input type="text" required class="form-control" placeholder="Type you remarks or comment here" name="cancel_remarks" id="cancel_ride_remarks">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" id="cancel_ride_reqeust_btn">CANCEL</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<!-- cancel ride modal end -->


@endsection
@section('bottom')
<script>


    var completerideapi = "{{route('admin.rides.city.complete', ['ride_reqeust_id' => '*'])}}"
    $('.complete-ride-menu-btn').on('click', function(){
        let rideId = $(this).data('ride-request-id');
        console.log('ride request id', rideId);


        swal({
            title: `City ride id ${rideId} will be completed`,
            text: "",
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "green",
            confirmButtonText: "Yes, complete",
            cancelButtonText: "No, cancel",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function (isConfirm) {
            
            if (!isConfirm) {
                return false;
            } 

            let apiurl = completerideapi.replace('*', rideId);
            $.post(apiurl, {_token : "{{csrf_token()}}"}, function(response){
                
                console.log(response)

                if(response.success){
                    showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    return;
                }

                showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');

            }).fail(function(){
                showNotification('bg-black', 'Internal server error. Try again !!', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            })
                
            swal.close();        
            return true;

        });



    });


    var cancelRideRequestApi = "{{route('admin.rides.city.cancel', ['ride_reqeust_id' => '*'])}}";
    $("#cancel_ride_reqeust_btn").on('click', function(event){
        event.preventDefault();

        var data = $('#cance_ride_form').serializeArray();
        console.log(data)

        let rideRequestId = $("#cancel_ride_reqeust_id").val();
        let apiurl = cancelRideRequestApi.replace('*', rideRequestId)

        console.log(apiurl)

        $.post(apiurl, data, function(response){
            console.log(response)

            if(response.success){
                showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                $("#cancel_ride_modal").modal('hide');
                return;
            }

            showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');

        }).fail(function(){
            showNotification('bg-black', 'Internal server error. Try again !!', 'top', 'right', 'animated flipInX', 'animated flipOutX');
        })

    })


    $(".cancel-ride-menu-btn").on('click', function(){
        $("#cancel_ride_modal").modal('show');
        
        let rideRequestId = $(this).data('ride-request-id');
        $("#cancel_ride_reqeust_id").val(rideRequestId);

        $("#cancel_ride_remarks").val('');

    })

    
    function openInNewTab(url) {
        var win = window.open(url, '_blank');
        win.focus();
    }

    $(".view-in-details").on('click', function(){
        var ride_request_id = $(this).data('ride-request-id')
        let cityRideDetailsApi = "{{route('admin.rides.city.details', ['ride_request_id' => '*'])}}";
        var url = cityRideDetailsApi.replace('*', ride_request_id);
        openInNewTab(url);
    });

    $(".driver-rides-menu-item").on('click', function(){
        var driverId = $(this).data('driver-id')
        var data = {'driver_id' : driverId};
        var url = "{{route('admin.rides.city')}}"+objectToQueryString(data);
        console.log(url)
        window.location.href = url;
    })

    $(".user-rides-menu-item").on('click', function(){
        var userId = $(this).data('user-id')
        var data = {'user_id' : userId};
        var url = "{{route('admin.rides.city')}}"+objectToQueryString(data);
        console.log(url)
        window.location.href = url;
    })

    $(".view-user-menu-item").on('click', function(){
        var userId = $(this).data('user-id');
        let userDetailsApi = "{{route('admin.show.user', ['user_id' => '*'])}}";
        var url = userDetailsApi.replace('*', userId);
        openInNewTab(url);
    })
    
    
    $(".view-driver-menu-item").on('click', function(){
        var driverId = $(this).data('driver-id');
        let driverDetailsApi = "{{route('admin.show.driver', ['driver_id' => '*'])}}";
        var url = driverDetailsApi.replace('*', driverId);
        openInNewTab(url);
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
    


    // Read a page's GET URL variables and return them as an associative array.
    function getUrlVars()
    {
        var url = location.search;
        var qs = url.substring(url.indexOf('?') + 1).split('&');
        for(var i = 0, result = {}; i < qs.length; i++){
            qs[i] = qs[i].split('=');
            duric = decodeURIComponent(qs[i][1]);
            if(qs[i][0] == undefined || qs[i][0] == '' || duric == undefined || duric == '')
            continue;
            result[qs[i][0]] = decodeURIComponent(qs[i][1]);
        }
        return result;
    }
    
    
    function objectToQueryString(obj) 
    {
       var query = Object.keys(obj)
           .filter(key => obj[key] !== '' && obj[key] !== null)
           .map(key => key + '=' + obj[key])
           .join('&');
       return query.length > 0 ? '?' + query : null;
    }
    
    
</script>
@endsection