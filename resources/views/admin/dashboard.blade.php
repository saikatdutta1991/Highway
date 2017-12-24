@extends('admin.layouts.master')
@section('title', 'Dashboard')
@section('dashboard_active', 'active')
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>DASHBOARD</h2>
    </div>
    <!-- Widgets -->
    <div class="row clearfix">
        
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-pink hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">people</i>
                </div>
                <div class="content">
                    <div class="text">TOTAL USERS</div>
                    <div class="number count-to" data-from="0" data-to="{{$totalUsers}}" data-speed="1000" data-fresh-interval="20"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">drive_eta</i>
                </div>
                <div class="content">
                    <div class="text">TOTAL DRIVERS</div>
                    <div class="number count-to" data-from="0" data-to="{{$totalDrivers}}" data-speed="1000" data-fresh-interval="20"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-purple hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">send</i>
                </div>
                <div class="content">
                    <div class="text">RIDE REQUESTS</div>
                    <div class="number count-to" data-from="0" data-to="{{$totalRideRequests}}" data-speed="1000" data-fresh-interval="20"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-green hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">account_circle</i>
                </div>
                <div class="content">
                    <div class="text">ONLINE USERS</div>
                    <div class="number count-to" data-from="0" data-to="{{$totalOnlineUsers}}" data-speed="1000" data-fresh-interval="20"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-green hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">drive_eta</i>
                </div>
                <div class="content">
                    <div class="text">ONLINE DRIVERS</div>
                    <div class="number count-to" data-from="0" data-to="{{$totalOnlineDrivers}}" data-speed="1000" data-fresh-interval="20"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-light-blue hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">format_textdirection_l_to_r</i>
                </div>
                <div class="content">
                    <div class="text">CASH PAYMENTS</div>
                    <div class="number count-to" data-from="0" data-to="{{$totalCashPayments}}" data-speed="1000" data-fresh-interval="20"></div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-teal hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">credit_card</i>
                </div>
                <div class="content">
                    <div class="text">PAYU PAYMENTS</div>
                    <div class="number count-to" data-from="0" data-to="{{$totalPayuPayments}}" data-speed="1000" data-fresh-interval="20"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-teal hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">monetization_on</i>
                </div>
                <div class="content">
                    <div class="text">TOTAL REVENUE</div>
                    <div class="number count-to" data-from="0" data-to="{{$totalRevenue}}" data-speed="1000" data-fresh-interval="20"></div>
                </div>
            </div>
        </div>
       
    </div>
    <!-- #END# Widgets -->
    
    <div class="row clearfix">
        
        <!-- Answered Tickets -->
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="body bg-deep-purple">
                    <div class="font-bold m-b--35">USERS</div>
                    <ul class="dashboard-stat-list">
                        <li>
                            TODAY
                            <span class="pull-right"><b>{{$todaysUsers}}</b> <small>USERS</small></span>
                        </li>
                        <li>
                            PAST 7 DAYS
                            <span class="pull-right"><b>{{$pastSevenDaysUsers}}</b> <small>USERS</small></span>
                        </li>
                        <li>
                            THIS MONTH
                            <span class="pull-right"><b>{{$thisMonthUsers}}</b> <small>USERS</small></span>
                        </li>
                        <li>
                            THIS YEAR
                            <span class="pull-right"><b>{{$thisYearUsers}}</b> <small>USERS</small></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- #END# Answered Tickets -->
        <!-- Answered Tickets -->
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="body bg-teal">
                    <div class="font-bold m-b--35">DRIVERS</div>
                    <ul class="dashboard-stat-list">
                        <li>
                            TODAY
                            <span class="pull-right"><b>{{$todaysDrivers}}</b> <small>DRIVERS</small></span>
                        </li>
                        <li>
                            PAST 7 DAYS
                            <span class="pull-right"><b>{{$pastSevenDaysDrivers}}</b> <small>DRIVERS</small></span>
                        </li>
                        <li>
                            THIS MONTH
                            <span class="pull-right"><b>{{$thisMonthDrivers}}</b> <small>DRIVERS</small></span>
                        </li>
                        <li>
                            THIS YEAR
                            <span class="pull-right"><b>{{$thisYearDrivers}}</b> <small>DRIVERS</small></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- #END# Answered Tickets -->
    </div>
    <div class="row clearfix">
        <!-- Task Info -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="header">
                    <h2>LATEST FIVE USERS</h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0);">See all users</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    @if($laterUsers->count() == 0)
                    <div class="alert bg-pink">
                        No users has registered yet
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover dashboard-task-infos">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Rating</th>
                                    <th>Registered On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($laterUsers as $user)
                                <tr>
                                    <td>{{$user->id}}</td>
                                    <td>{{$user->fname.' '.$user->lname}}</td>
                                    <!-- <td><span class="label bg-green">Doing</span></td> -->
                                    <td>{{$user->email}}</td>
                                    <td>{{$user->fullMobileNumber()}}</td>
                                    <td>{{$user->rating}}</td>
                                    <td>{{$user->registeredOn($default_timezone)}}</td>
                                </tr>
                                @endforeach
                                
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>


        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="header">
                    <h2>LATEST FIVE DRIVERS</h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0);">See all drivers</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    @if($laterDrivers->count() == 0)
                    <div class="alert bg-pink">
                        No drivers has registered yet
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover dashboard-task-infos">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Rating</th>
                                    <th>Approved</th>
                                    <th>Registered On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($laterDrivers as $driver)
                                <tr>
                                    <td>{{$driver->id}}</td>
                                    <td>{{$driver->fname.' '.$driver->lname}}</td>
                                    <td>{{$driver->email}}</td>
                                    <td>{{$driver->fullMobileNumber()}}</td>
                                    <td>{{$driver->rating}}</td>
                                    <td>
                                        @if($driver->is_approved == 1)
                                        <span class="label bg-green">Approved</span>
                                        @else
                                        <span class="label bg-blue">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{$driver->registeredOn($default_timezone)}}</td>
                                </tr>
                                @endforeach
                                
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- #END# Task Info -->
        <!-- Browser Usage -->
        <!-- <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
            <div class="card">
                <div class="header">
                    <h2>BROWSER USAGE</h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0);">Action</a></li>
                                <li><a href="javascript:void(0);">Another action</a></li>
                                <li><a href="javascript:void(0);">Something else here</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div id="donut_chart" class="dashboard-donut-chart"></div>
                </div>
            </div>
        </div> -->
        <!-- #END# Browser Usage -->
    </div>
</div>
@endsection