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
    <h2>ROUTES</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    <button title="Add new route"  id="add-new-route" onclick="window.location.href='{{route('admin.show-new-route')}}'" type="button" class="btn bg-cyan btn-xs waves-effect pull-right">
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
                                <th>From&nbsp;<i style="vertical-align:middle" class="material-icons">flight_takeoff</i></th>
                                <th>To&nbsp;<i style="vertical-align:middle" class="material-icons">flight_land</i></th>
                                <th>AC Fare</th>
                                <th>Non-AC Fare</th>
                                <th>Aprox. Duration</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($routes as $route)
                            <tr id="route-row-{{$route->id}}">
                            <td data-toggle="tooltip" data-placement="left" title="Click to edit location" onclick="window.location.href='{{route("admin.routes.locations.points.show", ["location_id" => $route->from_location])}}'" style="text-decoration:underline;cursor:pointer">{{$route->from->name}}</td>
                            <td data-toggle="tooltip" data-placement="left" title="Click to edit location" onclick="window.location.href='{{route("admin.routes.locations.points.show", ["location_id" => $route->to_location])}}'" style="text-decoration:underline;cursor:pointer">{{$route->to->name}}</td>
                            <td>{{$route->total_fare}}
                                <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Fare Breakdown" data-content="Base Fare: {{$route->base_fare}} | Tax Fee: {{$route->tax_fee}} | Access Fee: {{$route->access_fee}}">help_outline</i>
                            </td>
                            <td>{{$route['non_ac_route']->total_fare}}
                                <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Fare Breakdown" data-content="Base Fare: {{$route['non_ac_route']->base_fare}} | Tax Fee: {{$route['non_ac_route']->tax_fee}} | Access Fee: {{$route['non_ac_route']->access_fee}}">help_outline</i>
                            </td>
                            <td>{{explode(':', $route->time)[0]}}h-{{explode(':', $route->time)[0]}}min</td>
                            <td>{{$route->status}}</td>
                            <td>{{$route->createdOn($default_timezone)}}</td>
                            <td>
                                <i class="material-icons" style="cursor:pointer" title="Edit route" onclick="window.location.href='{{route('admin.show-edit-route', ['route_id' => $route->id])}}'">edit</i>
                                <!-- <i class="material-icons col-red delete-route-btn" style="cursor:pointer" data-route-id="{{$route->id}}" title="Delete route">delete_forever</i> -->
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
@endsection