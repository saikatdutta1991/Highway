@extends('admin.layouts.master')
@section('title', 'Trip Points')
@section('trips_points_active', 'active')
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
    <h2>TRIP POINTS</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    LIST OF ALL TRIP POINTS
                    <small>All trip pickup and drop points driver can use.</small>
                </h2>
            </div>
            <small>
                <div class="body table-responsive">
                    @if($points->count() == 0)
                    <div class="alert bg-pink">
                        No points found
                    </div>
                    @else
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>ADDRESS</th>
                                <th>CITY</th>
                                <th>COUNTRY</th>
                                <th>LATITUDE-LONGITUDE</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($points as $point)
                            <tr id="point-row-{{$point->id}}">
                            <td>{{$point->id}}</td>
                            <td>{{$point->address}}</td>
                            <td>{{$point->city}}</td>
                            <td>{{$point->country}}</td>
                            <td>{{$point->latitude}}, {{$point->longitude}}</td>
                            <td>
                                <i class="material-icons col-red delete-point-btn" data-point-id="{{$point->id}}" title="Delete point">delete_forever</i>
                            </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                    <div class="row pull-right">
                        {!! $points->appends(request()->all())->render() !!}
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

    var delete_point_url = "{{url('admin/trips/points')}}/";
    var csrf_token = "{{csrf_token()}}";

    $(document).ready(function(){
        
        
        $(".delete-point-btn").on('click', function(){

            var deleteBtnElem = $(this);
        
            swal({
                title: "Are you sure to delete this point?",
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

                var point_id = deleteBtnElem.data('point-id');

                var data = {
                    point_id : point_id,
                    _token : csrf_token
                };
                
                $.post(delete_point_url+point_id+'/delete', data, function(response){
    
                    if(response.success) {
                        
                        swal("Point deleted successfully", "", "success"); 
        
                        $("#point-row-"+point_id).fadeOut();
                        
                    }
                })
                .fail(function(){
                    swal("Internal server error. Try later.", "", "success");
                });
            
            });

        });

        
    });
        
    
</script>
@endsection