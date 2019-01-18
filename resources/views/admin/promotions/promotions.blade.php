@extends('admin.layouts.master')
@section('title', 'Promotions')
@section('promotions_active', 'active')
@section('promotions_list_active', 'active')
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>PROMOTIONS</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="header">
            <h2>
                LIST OF ALL PROMOTIONS
                <small>List of all promotions created and status</small>
            </h2>
        </div>
        <div class="body table-responsive">
            @if($promotions->count() == 0)
            <div class="alert bg-pink">
                No promotions found
            </div>
            @else
            <table class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>TITLE</th>
                        <th>EMAIL</th>
                        <th>PUSH NOTIFICATION</th>
                        <th>BROADCAST TYPE</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promotions as $promotion)
                    <tr>
                        <td><a href="{{route('admin.show.edit.promotion', ['promotion_id' => $promotion->id])}}" data-toggle="tooltip" data-placement="left" title="Click to edit promotion">{{$promotion->title}}</a></td>
                        <td>
                            <input type="checkbox" disabled class="filled-in chk-col-pink" @if($promotion->has_email) checked @endif/>
                            <label for=""></label>
                        </td>
                        <td>
                            <input type="checkbox" disabled class="filled-in chk-col-pink" @if($promotion->has_pushnotification) checked @endif/>
                            <label for=""></label>
                        </td>
                        <td>{{$promotion->broadcastTypeText()}}</td>
                        <td>{{$promotion->status}}</td>
                        <td>
                            <li class="dropdown" style="list-style: none;">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                                <i class="material-icons">more_vert</i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="javascript:void(0);" class="waves-effect waves-block delete-promotion-btn" data-delete-api="{{route('admin.promotion.delete', ['promotion_id' => $promotion->id])}}">Delete</a></li>
                                    <li><a href="javascript:void(0);" class="waves-effect waves-block preview-email-btn" data-email-url="{{route('admin.promotion.email.preview', ['promotion_id' => $promotion->id])}}">Preview Email</a></li>
                                </ul>
                            </li>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>
@endsection
@section('bottom')
<script>
var _token = '{{csrf_token()}}'
$(document).ready(function(){

    $(".preview-email-btn").on('click', function(){
        var emailurl = $(this).data('email-url')
        window.open(emailurl, '_blank');
    })



    $(".delete-promotion-btn").on('click', function(){
        var deleteapi = $(this).data('delete-api')
        $.post(deleteapi, {_token : _token}, function(response){

            if(response.success){
                showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                return;
            }

            showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');

        }).fail(function(){
            showNotification('bg-black', 'Internal server error. Try again !!', 'top', 'right', 'animated flipInX', 'animated flipOutX');
        })
        
    })


})
</script>
@endsection