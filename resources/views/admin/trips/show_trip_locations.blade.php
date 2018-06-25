@extends('admin.layouts.master')
@section('title', 'Route Locations')
@section('trips_active', 'active')
@section('trips_route_locations', 'active')
@section('top-header')
<style>
.delete-point-btn
{
    cursor:pointer;
}
.bootstrap-notify-container {
    z-index:99999 !important;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>TRIP LOCATIONS</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    <button title="Add new location"  id="add-new-location" type="button" class="btn bg-cyan btn-xs waves-effect pull-right">
                        +Add New Location
                    </button>
                    LIST OF ALL ROUTE LOCATIONS
                    <small>All routes locations eg. Banglore, Chennai etc.</small>                    
                </h2>
            </div>
            <small>
                <div class="body table-responsive">
                    @if(!count($locations))
                    <div class="alert bg-pink">
                        No Locations Found
                    </div>
                    @else     
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Name</th>
                                <th>Points Count</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($locations as $location)
                            <tr>
                            <td>{{$location->id}}</td>
                            <td>{{$location->name}}</td>
                            <td>{{$location->points->count()}}</td>
                            <td>{{$location->createdOn($default_timezone)}}</td>
                            <td>
                                <i class="material-icons edit-location-btn" data-location-id="{{$location->id}}" style="cursor:pointer" title="Edit Location">edit</i>
                            </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </small>
            </div>
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>
<!-- intracity tax percentage -->
<div class="modal fade" id="add-new-location-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">ADD NEW LOCATION</h4>
                <small>Add new location. Location name must be unique.</small>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <form id="add-new-location-form">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label class="form-label">Location Name</label>
                                    <input type="text" class="form-control" name="name">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" id="add-new-location-create-btn">CREATE</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<!-- intracity tax percentage -->
@endsection
@section('bottom')
<script>

    var delete_route_url = "{{url('admin/routes')}}/";
    var csrf_token = "{{csrf_token()}}";

    $(document).ready(function(){


        $(".edit-location-btn").on('click', function(){
            var location_id = $(this).data('location-id')
            console.log(location_id)
            window.location.href="{{url('admin/routes/locations')}}/"+location_id+"/points"
        })



        $("#add-new-location").on('click', function(){
            $("#add-new-location-modal").modal('show')
        })


        $("#add-new-location-create-btn").on('click', function(){
            var data = $("#add-new-location-form").serializeArray();
            console.log(data)

            $.post("{{route('admin.create_location')}}", data, function(response){

                if(response.success) {
                    $("#add-new-location-modal").modal('hide')
                    showNotification('bg-black', "location created successfully. Wait while we will refresh the page.", 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    
                    setTimeout(function(){
                        window.location.reload()
                    }, 1000)
                    
                } else  {
                    showNotification('bg-red', response.text, 'bottom', 'right', 'animated flipInX', 'animated flipOutX');
                }
            })
            .fail(function(){
                swal("Internal server error. Try later.", "", "error");
            });


        })


        
    });
        
    
</script>
@endsection