@extends('admin.layouts.master')
@section('title', 'Intracity Rides')
@section('rides_active', 'active')
@section('intracity_rides_active', 'active')
@section('top-header')
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>INTRACITY RIDES</h2>
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
                <div class="text">PAYU RIDES</div>
                <div class="number count-to" data-from="0" data-to="{{$payuRides}}" data-speed="1000" data-fresh-interval="20"></div>
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
                LIST OF ALL INTRACITY RIDES
                <small>You can see all intracity rides, fitler and many more.</small>
            </h2>
        </div>
        
        <!-- #END# Select -->
        <small>
            <div class="body table-responsive">
                @if($rides->count() == 0)
                <div class="alert bg-pink">
                    No rides found
                </div>
                @else
                <table class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>USER</th>
                            <th>DRIVER</th>
                            <th>SERVICE</th>
                            <th>PICKUP-DROP</th>
                            <th>AMOUNT</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rides as $ride)
                        <tr>
                            <td>{{$ride->id}}</td>
                            <td><a data-toggle="tooltip" data-placement="left" title="" href="javascript:void(0)" class="" >{{$ride->user->fname.' '.$ride->user->lname}}</a></td>
                            <td><a data-toggle="tooltip" data-placement="left" title="" href="javascript:void(0)" class="" >{{$ride->user->fname.' '.$ride->user->lname}}</a></td>
                            <td>{{$ride->ride_vehicle_type}}</td>
                            <td><i class="material-icons col-green" style="font-size:10px;vertical-align: middle;">fiber_manual_record</i>{{$ride->source_address}} @if($ride->ride_start_time)<br>{{$ride->getStartTime($default_timezone)}} {{date('d M Y', strtotime($ride->ride_start_time))}}@endif<br><span>|</span><br><i class="material-icons col-red" style="font-size:10px;vertical-align: middle;">fiber_manual_record</i>{{$ride->destination_address}} @if($ride->ride_end_time)<br>{{$ride->getStartTime($default_timezone)}} {{date('d M Y', strtotime($ride->ride_end_time))}}@endif</td>
                            <td><i data-toggle="tooltip" data-placement="left" title="@if($ride->invoice->payment_status == 'PAID') Paid @else Not paid @endif" class="material-icons @if($ride->invoice->payment_status == 'PAID') col-green @else col-red @endif" style="font-size:10px;vertical-align:middle;">fiber_manual_record</i>{{$currency_symbol}}{{$ride->invoice->total}}</td>
                            <td>{{$ride->ride_status}}</td>
                            <td>
                                <li class="dropdown" style="list-style: none;">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                                    <i class="material-icons">more_vert</i>
                                    </a>
                                    <ul class="dropdown-menu pull-right">
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">View Driver</a></li>
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">View User</a></li>
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">Only this user rides</a></li>
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">Only this driver rides</a></li>
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">View invoice</a></li>
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
                    <div>
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
    
    $("#driver-search-form").on('submit', function(event){
    
        event.preventDefault()
        
        var formDataArray = $(this).serializeArray();
        var finalObj = getUrlVars(); 
        formDataArray.map(function(obj){
            var temp = {};
            temp[obj.name] =  obj.value;
            finalObj = Object.assign(finalObj, temp);
        });
        
        var url = '{{url("admin/drivers")}}' + objectToQueryString(finalObj);
        console.log(url)
        window.location.href = url;
    
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
    
    
    $(".sort-by").on('click', function(){
        
        var order_by = $(this).data('order-by');
        var order = $(this).data('order');
        var urlVars = getUrlVars();
        urlVars.order_by=order_by;
        urlVars.order=order;
    
        var url = '{{url("admin/drivers")}}' + objectToQueryString(urlVars);
        
        console.log(url)
        window.location.href = url;
       
    
    });


    $('#driver-search-form select[name="search_by"]').on('change', function(){

        //if search by location show location autocomplete or skwd
        console.log('searby changed', $(this).val())
        var skwdElem = $('#driver-search-form input[name="skwd"]');
        var lnElem = $('#driver-search-form input[name="location_name"]');
        if($(this).val() == 'location') {
            skwdElem.hide();
            skwdElem.val('');
            lnElem.show();
            lnElem.focus().select();
            lnElem.attr('placeholder', '');            
        } else {
            skwdElem.show().focus().select();            
            lnElem.hide();
            lnElem.val('')
            $('#driver-search-form input[name="longitude"]').val('')
            $('#driver-search-form input[name="location_name"]').val('');
        }

    }).change();
    

    
</script>
@endsection