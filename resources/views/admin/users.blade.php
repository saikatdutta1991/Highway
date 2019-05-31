@extends('admin.layouts.master')
@section('title', 'Users')
@section('users_active', 'active')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
<style>
#user-list-id-header-checkbox-label:after
{
    top : 6px;
}
#user-list-id-header-checkbox-label:before
{
    margin-top: 8px;
}
.user-image
{
    border-radius: 50%;
}
.edit-user-btn
{
    text-decoration:none;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>USERS</h2>
</div>
<!-- Widgets -->
<div class="row clearfix">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-pink hover-expand-effect">
            <div class="icon">
                <i class="material-icons">timeline</i>
            </div>
            <div class="content">
                <div class="text">JOINED TODAY</div>
                <div class="number count-to" data-from="0" data-to="{{$todaysUsers}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-red hover-expand-effect">
            <div class="icon">
                <i class="material-icons">timeline</i>
            </div>
            <div class="content">
                <div class="text">THIS MONTH</div>
                <div class="number count-to" data-from="0" data-to="{{$thisMonthUsers}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-purple hover-expand-effect">
            <div class="icon">
                <i class="material-icons">timeline</i>
            </div>
            <div class="content">
                <div class="text">THIS YEAR</div>
                <div class="number count-to" data-from="0" data-to="{{$thisYearUsers}}" data-speed="1000" data-fresh-interval="20"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green hover-expand-effect">
            <div class="icon">
                <i class="material-icons">timeline</i>
            </div>
            <div class="content">
                <div class="text">TOTAL</div>
                <div class="number count-to" data-from="0" data-to="{{$totalUsers}}" data-speed="1000" data-fresh-interval="20"></div>
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
                LIST OF ALL USERS
                <small>You can see all users. You can sort by created, name, email etc. Filter users by Name, Email etc. Click on user name to edit</small>
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
        <div class="row clearfix collapse @if($search_by != '' && $skwd != '' ) in @endif" id="search-form">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="body">
                        <form action="" method="GET" id="user-search-form">
                            <div class="row clearfix">
                                <div class="col-sm-5">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="skwd" value="{{$skwd}}">
                                            <label class="form-label">Type your search keyword</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control show-tick" name="search_by">
                                    <option value="name" @if($search_by == "name") selected @endif>Name</option>
                                    <option value="email" @if($search_by == "email") selected @endif>Email</option>
                                    <option value="full_mobile_number" @if($search_by == "full_mobile_number") selected @endif>Mobile</option>
                                    <option value="created_at" @if($search_by == "created_at") selected @endif>Joined Date(Y-m-d)</option>
                                    <option value="id" @if($search_by == "id") selected @endif>Identification number</option>
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
                @if($users->count() == 0)
                <div class="alert bg-pink">
                    No users found
                </div>
                @else
                <table class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="filled-in chk-col-pink" id="checkbox-user-id-header"/>
                                <label style="font-weight: 700;margin-bottom: 0px;line-height: 33px;" for="checkbox-user-id-header" id="user-list-id-header-checkbox-label"></label>
                                <!-- #ID -->
                            </th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>MOBILE</th>
                            <th>RATING</th>
                            <th>RIDES</th>
                            <th>REGISTERD</th>
                            <!-- <th></th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <th>
                                <input type="checkbox" id="checkbox-user-id-{{$user->id}}" class="filled-in chk-col-pink user-list-id-checkbox" data-user-id="{{$user->id}}"/>
                                <label for="checkbox-user-id-{{$user->id}}"></label>
                            </th>
                            <td><a data-toggle="tooltip" data-placement="left" title="Click to edit user" href="javascript:void(0)" class="edit-user-btn" data-user-id="{{$user->id}}">{{$user->fname.' '.$user->lname}}</a></td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->full_mobile_number}}</td>
                            <td>{{$user->rating}}</td>
                            <td>{{$user->rideRequests()->where('ride_status', "COMPLETED")->count()}}</td>
                            <td>{{$user->registeredOn($default_timezone)}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
                <div class="row pull-right">
                    {!! $users->appends(request()->all())->render() !!}
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
                            <input type="checkbox" id="all_users_push_check" name="send_all" class="filled-in chk-col-pink">
                            <label for="all_users_push_check">Send All Users (It may take long time)</label>
                        </div>
                    </div>
                </form>
                <div class=" m-t-30" id="push-notification-progressbar-div">
                    <div class="progress">
                        <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                            <span class="sr-only"></span>
                        </div>
                    </div>
                    <small>Sending push notification(s) in progress : <span id="push-notification-progress-status"></span></small>
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

$(document).ready(function(){


    /**
        edit user link click handler
     */
     $(".edit-user-btn").on('click', function(){

        var userId = $(this).data('user-id');
        let showUserApi = "{{route('admin.show.user', ['user_id' => '*'])}}";
        var url = showUserApi.replace('*', userId);
        window.open(url, '_blank');

     });


    


    var userIds = [];
    var totalUserIdCheckboxs = $(".user-list-id-checkbox").length;

    //trigger checked or not checked all checkboxs
    $("#checkbox-user-id-header").on('change', function(){
        $(".user-list-id-checkbox").prop('checked', $(this).is(':checked')).change();
    });


    $(".user-list-id-checkbox").on('change', function(){

        //user id 
        var userId = $(this).data('user-id');
        var index = userIds.indexOf(userId);

        //if checked and not in array then push
        if($(this).is(':checked')) {

            if(index < 0) {
                userIds.push(userId)

                //check header if all checkboxes checked
                if(userIds.length == totalUserIdCheckboxs) {
                    $("#checkbox-user-id-header").prop('checked', true)
                }

            }
            
        } else {

            if(index > -1) {
                userIds.splice(index, 1)

                //uncheck header if any checkboxes unchecked
                if(userIds.length < totalUserIdCheckboxs) {
                    $("#checkbox-user-id-header").prop('checked', false)
                }

            }
        }


        console.log(userIds)
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


        if(!userIds.length && !$("#all_users_push_check").is(':checked')) {
            $("#send-pushnotification-modal").modal("hide");
            showNotification('bg-black', 'Please Select atleast one user from users list', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            return;
        }


        var formDataArray = $("#send-pushnotification-form").serializeArray();
        var finalObj = {ids:userIds.join('|')};
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
    

        var sseUrl = "{{route('admin.users.pushnotification.send')}}"+objectToQueryString(finalObj);

        pushnotificationSSE = new EventSource(sseUrl);
        pushnotificationSSE.addEventListener('error', function(e) {
            $("#send-pushnotification-modal").modal("hide");
            showNotification('bg-black', 'Internal server error try later', 'top', 'right', 'animated flipInX', 'animated flipOutX');
        }, false);
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
    
    
    $("#user-search-form").on('submit', function(event){
    
        event.preventDefault()
        
        var formDataArray = $(this).serializeArray();
        var finalObj = getUrlVars(); 
        formDataArray.map(function(obj){
            var temp = {};
            temp[obj.name] =  obj.value;
            finalObj = Object.assign(finalObj, temp);
        });
        
        var url = '{{route("admin-users")}}' + objectToQueryString(finalObj);
        console.log(url)
        window.location.href = url;
    
    })


    $(".sort-by").on('click', function(){
        
        var order_by = $(this).data('order-by');
        var order = $(this).data('order');
        var urlVars = getUrlVars();
        urlVars.order_by=order_by;
        urlVars.order=order;
    
        var url = '{{route("admin-users")}}' + objectToQueryString(urlVars);
        
        console.log(url)
        window.location.href = url;
       
    
    });
   
});
    

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