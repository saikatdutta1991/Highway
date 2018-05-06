@extends('admin.layouts.master')
@section('title', 'Routes')
@section('trips_all_routes_active', 'active')
@section('trips_active', 'active')
@section('top-header')
<style>
.delete-point-btn
{
    cursor:pointer;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>TRIP ROUTES</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    <button title="Add new route"  id="add-new-route" onclick="window.location.href='{{route('admin.show-add-new-route')}}'" type="button" class="btn bg-cyan btn-xs waves-effect pull-right">
                        +Add New Route
                    </button>
                    LIST OF ALL ROUTES
                    <small>All routes with pickup and drop points driver can use.</small>                    
                </h2>
            </div>
            <small>
                <div class="body table-responsive">
                    @if(!count($routes))
                    <div class="alert bg-pink">
                        No points found
                    </div>
                    @else     
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Points Count</th>
                                <th>Created</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($routes as $route)
                            <tr id="route-row-{{$route->id}}">
                            <td>{{$route->id}}</td>
                            <td>{{$route->name}}</td>
                            <td>{{$route->status}}</td>
                            <td>{{count($route->points)}}</td>
                            <td>{{$route->formatedCreatedAt($default_timezone)}}</td>
                            <td>
                                <i class="material-icons col-red delete-route-btn" style="cursor:pointer" data-route-id="{{$route->id}}" title="Delete route">delete_forever</i>
                                <i data-toggle="collapse" data-target="#points-list-of-route-{{$route->id}}" class="material-icons show-all-points" style="cursor:pointer" title="show all points">arrow_downward</i>
                            </td>
                            </tr>
                            <tr>
                            <td colspan="6">
                                <div class="collapse" id="points-list-of-route-{{$route->id}}">
                                    <table class="table table-condensed table-hover">
                                        <thead>
                                            <tr>
                                                <th>ADDRESS</th>
                                                <th>CITY</th>
                                                <th>COUNTRY</th>
                                                <th>LATITUDE-LONGITUDE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($route->points as $point)
                                            <tr id="point-row-{{$point->id}}">
                                            <td>{{$point->address}}</td>
                                            <td>{{$point->city}}</td>
                                            <td>{{$point->country}}</td>
                                            <td>{{$point->latitude}}, {{$point->longitude}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                    <div class="row pull-right">
                    {!! $routes->appends(request()->all())->render() !!}
                    </div>
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

    var delete_route_url = "{{url('admin/routes')}}/";
    var csrf_token = "{{csrf_token()}}";

    $(document).ready(function(){
        

        $(".show-all-points").hover(function(){
            $(this).click();
        }, function(){})
        
        $(".delete-route-btn").on('click', function(){

            var deleteBtnElem = $(this);
        
            swal({
                title: "Are you sure to delete this route?",
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

                var route_id = deleteBtnElem.data('route-id');

                var data = {
                    route_id : route_id,
                    _token : csrf_token
                };
                
                $.post(delete_route_url+route_id+'/delete', data, function(response){

                    if(response.success) {
                        
                        swal("Route deleted successfully", "", "success"); 
        
                        $("#route-row-"+route_id).fadeOut();
                        
                    }
                })
                .fail(function(){
                    swal("Internal server error. Try later.", "", "error");
                });
            
            });

        });

        
    });
        
    
</script>
@endsection