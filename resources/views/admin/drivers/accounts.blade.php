@extends('admin.layouts.master')
@section('title', 'Driver Accounts')
@section('driver_active', 'active')
@section('driver_accounts_active', 'active')
@section('top-header')
<style>
#find-filter {
    position: absolute;
    right: 15px;
    top: 15px;
}
.balance {
    font-size: 24px;
}

.mark-red {
    /* font-weight:bold; */
    /* font-size: 20px; */
    color:red;
}

</style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>DRIVER ACCOUNTS</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    ACCOUNT BALANCES
                </h2>
                
                <div id="find-filter">
                    <div class="input-group" style="width: 250px;">
                        <span class="input-group-addon">
                        Find balance less than {{$currency_symbol}}
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" placeholder="50" value="{{request()->findlessbalance}}">
                        </div>
                        <span class="input-group-addon">
                            <i class="material-icons" style="cursor:pointer" id="find-btn">send</i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="body table-responsive">
                <table class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>DRIVER ID</th>
                            <th>EMAIL</th>
                            <th>MOBILE</th>
                            <th>BALANCE</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drivers as $driver)
                        <tr>
                            <td>{{$driver->id}}</td>
                            <td>{{$driver->email}}</td>
                            <td>{{$driver->fullMobileNumber()}}</td>
                            <td class="balance @if(request()->findlessbalance && $driver->balance < request()->findlessbalance) mark-red @endif">
                                {{$currency_symbol}} {{$driver->balance}}
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a type="button" class="btn bg-orange waves-effect" href="{{route('admin.drivers.accounts.recharge')}}?driverid={{$driver->id}}#recharge_div">Recharge</a>
                                    <a type="button" class="btn bg-orange waves-effect" href="{{route('admin.drivers.accounts.recharge')}}?driverid={{$driver->id}}#transactions_div">Transactions</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- #END# With Material Design Colors -->
    </div>
</div>

@endsection
@section('bottom')
<script>    
$(document).ready(function(){

    var accounturl = "{{route('admin.drivers.accounts')}}?findlessbalance="

    $("#find-btn").on('click', function(){
        balance = $("#find-filter").find('input[type=text]').val();        
        window.location.href =  accounturl + balance;
    });


});
</script>
@endsection