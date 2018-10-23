@extends('admin.layouts.master')
@section('title', 'Coupons')
@section('coupons_active', 'active')
@section('coupons_view_active', 'active')
@section('top-header')
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
.copy-btn {
    font-size : 20px;
    cursor: pointer;
    display:none;
    vertical-align:middle;
}
.code-cell:hover > .copy-btn{
    display:inline-block;
}
.copy-code {
    display:none;
    opacity:0;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>ALL COUPONS</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <!-- <div class="header">
            <h2>
                LIST OF ALL USERS
                <small>You can see all users. You can sort by created, name, email etc. Filter users by Name, Email etc. Click on user name to edit</small>
            </h2>
        </div> -->
        <small>
        <div class="body table-responsive">
            @if($coupons->count() == 0)
            <div class="alert bg-pink">
                No users found
            </div>
            @else
            <table class="table table-condensed table-hover">
                <thead>
                    <tr>                        
                        <th>CODE</th>
                        <th>USES</th>
                        <th>USES PER USER</th>
                        <th>TYPE</th>
                        <th>DISCOUNT</th>
                        <th>STARTS</th>
                        <th>EXPIRES</th>
                       <!--  <th>ACTIONS</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($coupons as $coupon)
                    <tr>                        
                        <td>
                            <a data-toggle="tooltip" data-placement="left" title="Click to edit" href="javascript:void(0)">{{$coupon->code}}</a>
                            <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Name : {{$coupon->name}} | {{$coupon->description}}">help_outline</i>
                        </td>
                        <td>{{$coupon->user_coupons_count}}/{{$coupon->max_uses}}</td>   
                        <td>{{$coupon->max_uses_user}}</td>   
                        <td>{{$coupon->type}}</td>   
                        <td>{{intval($coupon->discount_amount)}}({{$coupon->discount_type}})</td>   
                        <td>{{$coupon->starts_at}}</td>   
                        <td>{{$coupon->expires_at}}</td>   
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

@endsection
@section('bottom')
<script>

$(document).ready(function(){


    /**
        edit user link click handler
     */
     $(".edit-user-btn").on('click', function(){

        var userId = $(this).data('user-id');
        console.log(userId)
        var url = "{{url('admin/users')}}/"+userId;
        window.open(url, '_blank');

     });


   
});
    

    
</script>
    
@endsection