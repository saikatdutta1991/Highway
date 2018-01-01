@extends('admin.layouts.master')
@section('title', 'Drivers')
@section('driver_active', 'active')
@section('driver_list_active', 'active')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
<style>
#driver-list-id-header-checkbox-label:after
{
    top : 6px;
}
#driver-list-id-header-checkbox-label:before
{
    margin-top: 8px;
}
.driver-image
{
    border-radius: 50%;
}
.edit-driver-btn
{
    text-decoration:none;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>DRIVERS</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="header">
            <h2>
                LIST OF ALL DRIVERS
                <small>You can see all drivers. You can sort by created, name, email etc. Filter drivers by Name, Email etc. Click on driver name to edit</small>
            </h2>
            <ul class="header-dropdown m-r--5">
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" data-toggle="tooltip" data-placement="left" title="Advance menu">
                    <i class="material-icons">more_vert</i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:void(0);"><i class="material-icons">sort</i>SORT BY</a></li>
                        <li role="seperator" class="divider"></li>
                        <li class="sort-by" data-order-by="created_at" data-order="asc">
                            <a href="javascript:void(0);">
                            <i class="material-icons">sort_by_alpha</i>Created (Asc)
                            @if($order_by=="created_at" && $order =='asc')<span class="glyphicon glyphicon-ok check-mark pull-right"></span>@endif
                            </a>
                        </li>
                        <li class="sort-by" data-order-by="created_at" data-order="desc">
                            <a href="javascript:void(0);"><i class="material-icons">filter_list</i>Created (Desc)
                            @if($order_by=="created_at" && $order =='desc')<span class="glyphicon glyphicon-ok check-mark pull-right"></span>@endif
                            </a>
                        </li>
                        <li class="sort-by" data-order-by="fname" data-order="asc">
                            <a href="javascript:void(0);">
                            <i class="material-icons">sort_by_alpha</i>Name (Asc)
                            @if($order_by=="fname" && $order =='asc')<span class="glyphicon glyphicon-ok check-mark pull-right"></span>@endif
                            </a>
                        </li>
                        <li class="sort-by" data-order-by="fname" data-order="desc">
                            <a href="javascript:void(0);">
                            <i class="material-icons">filter_list</i>Name (Desc)
                            @if($order_by=="fname" && $order =='desc')<span class="glyphicon glyphicon-ok check-mark pull-right"></span>@endif
                            </a>
                        </li>
                        <li class="sort-by" data-order-by="email" data-order="asc">
                            <a href="javascript:void(0);">
                            <i class="material-icons">sort_by_alpha</i>Email (Asc)
                            @if($order_by=="email" && $order =='asc')<span class="glyphicon glyphicon-ok check-mark pull-right"></span>@endif
                            </a>
                        </li>
                        <li class="sort-by" data-order-by="email" data-order="desc">
                            <a href="javascript:void(0);">
                            <i class="material-icons">filter_list</i> Email (Desc)
                            @if($order_by=="email" && $order =='desc')<span class="glyphicon glyphicon-ok check-mark pull-right"></span>@endif
                            </a>
                        </li>
                        <li class="sort-by" data-order-by="rating" data-order="asc">
                            <a href="javascript:void(0);">
                            <i class="material-icons">sort_by_alpha</i>Rating (Asc)
                            @if($order_by=="rating" && $order =='asc')<span class="glyphicon glyphicon-ok check-mark pull-right"></span>@endif
                            </a>
                        </li>
                        <li class="sort-by" data-order-by="rating" data-order="desc">
                            <a href="javascript:void(0);">
                            <i class="material-icons">filter_list</i>Rating (Desc)
                            @if($order_by=="rating" && $order =='desc')<span class="glyphicon glyphicon-ok check-mark pull-right"></span>@endif
                            </a>
                        </li>
                        <li role="seperator" class="divider"></li>
                        <li><a href="javascript:void(0);" id="send-pushnotification-menu-btn">Send pushnotification</a></li>
                        <!-- <li><a href="javascript:void(0);">Send email</a></li> -->
                        <li role="seperator" class="divider"></li>
                        <li data-toggle="collapse" 
                            data-target="#search-form" 
                            aria-expanded="false"
                            aria-controls="collapseExample"><a href="javascript:void(0);"><i class="material-icons">search</i>Search</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- Select -->
        <div class="row clearfix collapse @if($search_by != '' && $skwd != '' || ($search_by == 'location' && $location_name != '' ) ) in @endif" id="search-form">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <!-- <div class="header">
                        <h2>
                            SEARCH DRIVERS
                            <small>Enter your search keyword Eg. name, id, mobile etc. and select specific search type</small>
                        </h2>
                        </div> -->
                    <div class="body">
                        <form action="" method="GET" id="driver-search-form">
                            <div class="row clearfix">
                                <div class="col-sm-5">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="skwd" value="{{$skwd}}">
                                            <input type="text" class="form-control" autocomplete="off" name="location_name" value="{{$location_name}}">
                                            <input type="hidden" class="form-control" name="latitude" value="{{$latitude}}">
                                            <input type="hidden" class="form-control" name="longitude" value="{{$longitude}}">
                                            <input type="hidden" class="form-control" name="radius" value="500">
                                            <label class="form-label">Type your search keyword</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control show-tick" name="search_by">
                                        <!-- <option value="">-- Search by --</option> -->
                                        <option value="fname" @if($search_by == "fname") selected @endif>Name</option>
                                        <option value="email" @if($search_by == "email") selected @endif>Email</option>
                                        <option value="full_mobile_number" @if($search_by == "full_mobile_number") selected @endif>Mobile</option>
                                        <option value="vehicle_number" @if($search_by == "vehicle_number") selected @endif>Vehicle no.</option>
                                        <option value="created_at" @if($search_by == "created_at") selected @endif>Created time</option>
                                        <option value="id" @if($search_by == "id") selected @endif>Identification number</option>
                                        <option value="location" @if($search_by == "location") selected @endif>Location</option>
                                    </select>
                                </div>
                                <div class="col-sm-2 text-center">
                                    <button type="submit" class="btn bg-deep-orange waves-effect" data-toggle="tooltip" data-placement="left" title="Hit enter or click to search">
                                    <i class="material-icons">search</i>
                                    <span>SEARCH</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <small>Enter your search keyword Eg. name, id, mobile etc. and select specific search type</small>
                    </div>
                </div>
            </div>
        </div>
        <!-- #END# Select -->
        <small>
            <div class="body table-responsive">
                @if($drivers->count() == 0)
                <div class="alert bg-pink">
                    No drivers found
                </div>
                @else
                <table class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="filled-in chk-col-pink" id="checkbox-driver-id-header"/>
                                <label style="font-weight: 700;margin-bottom: 0px;line-height: 33px;" for="checkbox-driver-id-header" id="driver-list-id-header-checkbox-label">#ID</label>
                                <!-- #ID -->
                            </th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>MOBILE</th>
                            <th>VEHICLE NO.</th>
                            <th>RATING</th>
                            <th>REGISTERD</th>
                            <th>APPROVED</th>
                            <!-- <th></th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drivers as $driver)
                        <tr>
                            <th>
                                <input type="checkbox" id="checkbox-driver-id-{{$driver->id}}" class="filled-in chk-col-pink driver-list-id-checkbox" data-driver-id="{{$driver->id}}"/>
                                <label for="checkbox-driver-id-{{$driver->id}}">{{$driver->id}}</label>
                            </th>
                            <td><a data-toggle="tooltip" data-placement="left" title="Click to edit driver" href="javascript:void(0)" class="edit-driver-btn" data-driver-id="{{$driver->id}}">{{$driver->fname.' '.$driver->lname}}</a></td>
                            <td>{{$driver->email}}</td>
                            <td>{{$driver->full_mobile_number}}</td>
                            <td>{{$driver->vehicle_number}}</td>
                            <td>{{$driver->rating}}</td>
                            <td>{{$driver->registeredOn($default_timezone)}}</td>
                            <td>
                                <div class="switch approve-switch">
                                    <label><input type="checkbox" data-driver-id="{{$driver->id}}" @if($driver->is_approved==1) checked @endif><span class="lever switch-col-deep-orange"></span></label>
                                </div>                             
                            </td>
                            <!-- <td>
                                <li class="dropdown" style="list-style: none;">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                                    <i class="material-icons">more_vert</i>
                                    </a>
                                    <ul class="dropdown-menu pull-right">
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">Approve</a></li>
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">Edit</a></li>
                                    </ul>
                                </li>
                            </td> -->
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
                <div class="row pull-right">
                    {!! $drivers->appends(request()->all())->render() !!}
                    <div>
                    </div>
        </small>
        </div>
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>
<!-- For Material Design Colors -->
<div class="modal fade" id="send-pushnotification-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send push notification(s)</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="send-pushnotification-form">
                    <div class="row clearfix">
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                            <label for="email_address_2">Title</label>
                        </div>
                        <div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" required autofocus class="form-control" placeholder="Enter your push notification title" name="title">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                            <label for="email_address_2">Message</label>
                        </div>
                        <div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" required class="form-control" placeholder="Enter your push notification message" name="message">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix">
                        <br>
                        <div class="col-lg-offset-2 col-md-offset-2 col-sm-offset-4 col-xs-offset-5">
                            <input type="checkbox" id="all_drivers_push_check" name="send_all" class="filled-in chk-col-pink">
                            <label for="all_drivers_push_check">Send All Drivers (It may take long time)</label>
                        </div>
                    </div>
                </form>
                <div class=" m-t-30" id="push-notification-progressbar-div">
                    <div class="progress">
                        <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                            <span class="sr-only"></span>
                        </div>
                    </div>
                    <small>Sending push notification(s) in progress : <span id="push-notification-progress-status"></span><small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" id="pushnotification-send-btn">SEND</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('bottom')
<script>


    /**
        edit driver link click handler
     */
     $(".edit-driver-btn").on('click', function(){

        var driverId = $(this).data('driver-id');
        console.log(driverId)
        var url = "{{url('admin/drivers')}}/"+driverId;
        window.open(url, '_blank');

     });


    /**
        approve or disapprove swtich handler
    */

    $(".approve-switch input[type='checkbox']").on('change', function(){

        var csrf_token = "{{csrf_token()}}";
        var driverId = $(this).data('driver-id');
        var isApprove = $(this).is(":checked") ? 1 : 0;
        var url = "{{url('admin/drivers')}}/"+driverId+'/approve/'+isApprove;
        var curElem = this;
        console.log(url)

        if(isApprove) {        
            
            $.post(url, {_token:csrf_token}, function(response){
                console.log(response)
                if(response.success) {
                    
                    showNotification('bg-black', 'Driver approved successfully', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                   
                }                 
            }).fail(function(response) {
                $(curElem).prop('checked', false);
               
                showNotification('bg-black', 'Unknown server error. Failed to approve driver', 'top', 'right', 'animated flipInX', 'animated flipOutX');
               
            });
            
        } 
        //disapprove driver show alert message first
        else {

            swal({
                title: "Are you really want to disappove this driver",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, disapprve",
                cancelButtonText: "No, cancel please",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    
                    swal({
                        title: "Email Notification",
                        text: "Driver will get email notificaiton",
                        type: "input",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        animation: "slide-from-top",
                        inputPlaceholder: "Type reason for disapprove"
                    }, function (inputValue) {
                        if (inputValue === false) {
                            $(curElem).prop('checked', true);
                            return false;
                        } 

                        if (inputValue === "") {
                            swal.showInputError("You must enter reason"); return false
                        }
                        
                        $.post(url, {_token:csrf_token, message:inputValue}, function(response){
                            console.log(response)
                            if(response.success) {
                                showNotification('bg-black', 'Driver disapproved successfully', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                            }
                                        
                        }).fail(function(response) {
                            $(curElem).prop('checked', true);
                            showNotification('bg-black', 'Unknown server error. Failed to disapprove driver', 'top', 'right', 'animated flipInX', 'animated flipOutX');
                        });
                        console.log('disapprove clled')
                        swal.close();

                    });
                } else {
                    $(curElem).prop('checked', true);
                }  


            });



        }
 
       
        
    })







    var driverIds = [];
    var totalDriverIdCheckboxs = $(".driver-list-id-checkbox").length;

    //trigger checked or not checked all checkboxs
    $("#checkbox-driver-id-header").on('change', function(){
        $(".driver-list-id-checkbox").prop('checked', $(this).is(':checked')).change();
    });


    $(".driver-list-id-checkbox").on('change', function(){

        //driver id 
        var driverId = $(this).data('driver-id');
        var index = driverIds.indexOf(driverId);

        //if checked and not in array then push
        if($(this).is(':checked')) {

            if(index < 0) {
                driverIds.push(driverId)

                //check header if all checkboxes checked
                if(driverIds.length == totalDriverIdCheckboxs) {
                    $("#checkbox-driver-id-header").prop('checked', true)
                }

            }
            
        } else {

            if(index > -1) {
                driverIds.splice(index, 1)

                //uncheck header if any checkboxes unchecked
                if(driverIds.length < totalDriverIdCheckboxs) {
                    $("#checkbox-driver-id-header").prop('checked', false)
                }

            }
        }


        console.log(driverIds)
    });




    $('#send-pushnotification-form').validate({
        rules: {
            'title': {
                required: true,
                maxlength : 100
            },
            'message': {
                required: true,
                maxlength : 250
            }
        },
        highlight: function (input) {
            $(input).parents('.form-line').addClass('error');
        },
        unhighlight: function (input) {
            $(input).parents('.form-line').removeClass('error');
        },
        errorPlacement: function (error, element) {
            $(element).parents('.form-group').append(error);
        }
    });

    
    $("#send-pushnotification-menu-btn").on('click', function(){
        $("#push-notification-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
        $("#push-notification-progressbar-div").hide();
        $("#send-pushnotification-form").find("input[name='title']").val('')
        $("#send-pushnotification-form").find("input[name='message']").val('')
        $("#send-pushnotification-form").find("input[name='send_all']").prop('checked', false)
        $("#send-pushnotification-modal").modal("show");
    
    });
    //$("#send-pushnotification-menu-btn").click();
    var pushnotificationSSE = null;
    $("#pushnotification-send-btn").on('click', function(){

        if(!$('#send-pushnotification-form').valid()) {
            return;
        }


        if(!driverIds.length && !$("#all_drivers_push_check").is(':checked')) {
            $("#send-pushnotification-modal").modal("hide");
            showNotification('bg-black', 'Please Select atleast one driver from drivers list', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            return;
        }


        var formDataArray = $("#send-pushnotification-form").serializeArray();
        var finalObj = {ids:driverIds.join('|')};
        formDataArray.map(function(obj){
            var temp = {};
            temp[obj.name] =  obj.value;
            finalObj = Object.assign(finalObj, temp);
        });

        console.log(finalObj)

        if(pushnotificationSSE) {
            pushnotificationSSE.close();
        }
    
    
        $("#push-notification-progressbar-div").find('.progress > .progress-bar').css('width', '0%')
        $("#push-notification-progressbar-div").fadeIn();
    

        var sseUrl = "{{url('admin/drivers/send-pushnotification')}}"+objectToQueryString(finalObj);

        pushnotificationSSE = new EventSource(sseUrl);
        pushnotificationSSE.onmessage = function(event) {
            console.log(event.data);
            var data = JSON.parse(event.data)
            if(data.done == data.total) {
                pushnotificationSSE.close();
    
                setTimeout(function(){
                    $("#send-pushnotification-modal").modal("hide");
                    swal("Push notification(s) sent", "", "success");
                }, 2000)
                
            }
            $("#push-notification-progress-status").text(data.done+' out of ' + data.total)
            $("#push-notification-progressbar-div").find('.progress > .progress-bar').css('width', data.percent+'%')
            
        };
    });
    
    
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
    

    function initAutocomplete()
    {
        console.log('initAutocomplete')
        autocomplete = new google.maps.places.Autocomplete(document.querySelector('input[name="location_name"'));
        autocomplete.addListener('place_changed', function(){
            var place = autocomplete.getPlace();
            try {
                
                currLatitude = place.geometry.location.lat();
                currLongitude = place.geometry.location.lng();
                document.querySelector('input[name="latitude"').value = currLatitude;
                document.querySelector('input[name="longitude"').value = currLongitude;

            } catch(e) {
                document.querySelector('input[name="location_name"').value=''
                showNotification('bg-black', 'Select loction from dropdow', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            }
            
            console.log('place chnged', place)
        });
    }
    
    
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{$google_maps_api_key}}&libraries=places&callback=initAutocomplete"></script>
@endsection