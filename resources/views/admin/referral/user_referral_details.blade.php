@extends('admin.layouts.master')
@section('title', 'User Referrals')
@section('referral_active', 'active')
@section('user_referrals_active', 'active')
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
    <h2>USER REFERRALS</h2>
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
            @if($users->count() == 0)
            <div class="alert bg-pink">
                No users found
            </div>
            @else
            <table class="table table-condensed table-hover">
                <thead>
                    <tr>                        
                        <th>NAME</th>
                        <th>REFERRAL CODE</th>
                        <th>REFFERED COUNT</th>
                        <th>CURRENT AMOUNT</th>
                        <th>REFERRED AMOUNT</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>                        
                        <td><a data-toggle="tooltip" data-placement="left" title="Click to edit user" href="javascript:void(0)" class="edit-user-btn" data-user-id="{{$user->id}}">{{$user->fname.' '.$user->lname}}</a></td>
                        <td class="code-cell">
                            {{$user->referral_code->code}}
                            <input type="text" value="{{$user->referral_code->code}}" class="copy-code"> 
                            <i class="material-icons copy-btn" title="click to copy">
                                file_copy
                            </i>
                        </td>
                        <td>{{$user->referred_count}}</td>    
                        <td>{{$user->referral_code->bonus_amount}}</td>    
                        <td>{{$user->referral_histories->sum('referrer_bonus_amount')}}</td>    
                        <td>
                            <i class="material-icons" @if($user->referral_code->status == 'ENABLED') style="color:green;cursor:pointer" title="Enabled" @else style="color:red;cursor:pointer" title="Disabled" @endif>
                            fiber_manual_record
                            </i>
                        </td> 
                        <td>
                            <i data-toggle="collapse" data-target="#referred-users-list-{{$user->id}}" class="material-icons show-all-points" style="cursor:pointer" title="Referred users list">arrow_downward</i>
                        </td> 
                    </tr>
                    <tr class="collapse" id="referred-users-list-{{$user->id}}">
                            <td colspan="7">
                                <div >
                                    <table class="table table-condensed table-hover">
                                        <thead>
                                            <tr>
                                                <th>NAME</th>
                                                <th>EMAIL</th>
                                                <th>AMOUNT EARNED ON REGISTER</th>
                                                <th>REFERRER EARNED({{$user->fname}})</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->referral_histories as $rhistory)
                                            <tr>
                                                <td>{{$rhistory->referredUser->fname.' '.$rhistory->referredUser->lname}}</td>
                                                <td>{{$rhistory->referredUser->email}}</td>
                                                <td>{{$rhistory->referred_bonus_amount}}</td>
                                                <td>{{$rhistory->referrer_bonus_amount}}</td>
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
                {!! $users->appends(request()->all())->render() !!}
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

$(document).ready(function(){


    $(".copy-btn").on('click', function(){
        
        let code = $(this).parent().find('.copy-code');
        console.log(code.val())
        code.show().select();
        document.execCommand("copy");
        code.hide();
        showNotification('bg-black', 'Referral Code <br>'+code.val()+' <br>has been copied to clipboard', 'top', 'right', 'animated flipInX', 'animated flipOutX');
    })

    
    /**
        edit user link click handler
     */
     $(".edit-user-btn").on('click', function(){

        var userId = $(this).data('user-id');
        let showUserApi = "{{route('admin.show.user', ['user_id' => '*'])}}";
        var url = showUserApi.replace('*', userId);
        window.open(url, '_blank');

     });


   
});
    

    
</script>
    
@endsection