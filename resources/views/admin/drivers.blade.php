@extends('admin.layouts.master')
@section('title', 'Drivers')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>DRIVERS</h2>
    </div>
    <!-- With Material Design Colors -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        LIST OF ALL DRIVERS
                        <small>You can see all drivers. You can sort by created, name, email etc. Filter drivers by Name, Email etc.</small>
                    </h2>
                    
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0);"><i class="material-icons">sort</i>SORT BY</a></li>
                                <li role="seperator" class="divider"></li>
                                <li class="sort-by" data-order-by="created_at" data-order="asc"><a href="javascript:void(0);"><i class="material-icons">sort_by_alpha</i>Created(Asc)</a></li>
                                <li class="sort-by" data-order-by="created_at" data-order="desc"><a href="javascript:void(0);"><i class="material-icons">filter_list</i>Created(Desc)</a></li>
                                <li class="sort-by" data-order-by="fname" data-order="asc"><a href="javascript:void(0);"><i class="material-icons">sort_by_alpha</i>Name(Asc)</a></li>
                                <li class="sort-by" data-order-by="fname" data-order="desc"><a href="javascript:void(0);"><i class="material-icons">filter_list</i>Name(Desc)</a></li>
                                <li class="sort-by" data-order-by="email" data-order="asc"><a href="javascript:void(0);"><i class="material-icons">sort_by_alpha</i>Email(Asc)</a></li>
                                <li class="sort-by" data-order-by="email" data-order="desc"><a href="javascript:void(0);"><i class="material-icons">filter_list</i> Email(Desc)</a></li>
                                <li role="seperator" class="divider"></li>
                                <li><a href="javascript:void(0);">Send pushnotification</a></li>
                                <li><a href="javascript:void(0);">Send email</a></li>
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
                <div class="row clearfix collapse" id="search-form">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    SEARCH DRIVERS
                                    <small>Enter your search keyword Eg. name, id, mobile etc. and select specific search type</small>
                                </h2>
                            </div>
                            <div class="body">
                                <div class="row clearfix">
                                    <div class="col-sm-6">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="text" class="form-control">
                                                <label class="form-label">Type your search keyword</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="form-control show-tick">
                                            <option value="">-- Search by --</option>
                                            <option value="fname">Name</option>
                                            <option value="email">Email</option>
                                            <option value="full_mobile_number">Mobile</option>
                                            <option value="vehicle_number">Vehicle no.</option>
                                            <option value="created_at">Created time</option>
                                            <option value="id">Identification number</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="button" class="btn pull-right bg-purple btn-circle waves-effect waves-circle waves-float">
                                <i class="material-icons">search</i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- #END# Select --><small>
                <div class="body table-responsive">
                    @if($drivers->count() == 0)
                    <div class="alert bg-pink">
                        No drivers found
                    </div>
                    @else
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>NAME</th>
                                <th>EMAIL</th>
                                <th>MOBILE</th>
                                <!-- <th>VEHICLE NO.</th> -->
                                <th>RATING</th>
                                <th>REGISTERD</th>
                                <th>APPROVED</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($drivers as $driver)
                            <tr>
                                <th>
                                    <input type="checkbox" id="md_checkbox_22" class="filled-in chk-col-pink" checked />
                                    <label for="md_checkbox_22">{{$driver->id}}</label>
                                </th>
                                <td>{{$driver->fname.' '.$driver->lname}}</td>
                                <td>{{$driver->email}}</td>
                                <td>{{$driver->full_mobile_number}}</td>
                                <!-- <td>{{$driver->vehicle_number}}</td> -->
                                <td>{{$driver->rating}}</td>
                                <td>{{$driver->registeredOn($default_timezone)}}</td>
                                <td>
                                    @if($driver->is_approved == 1)
                                    <span class="label bg-green">Approved</span>
                                    @else
                                    <span class="label bg-blue">Pending</span>
                                    @endif
                                </td>
                                <td>
                                 
                                    <li class="dropdown" style="list-style: none;">
                                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                                            <i class="material-icons">more_vert</i>
                                        </a>
                                        <ul class="dropdown-menu pull-right">
                                            <li><a href="javascript:void(0);" class=" waves-effect waves-block">Approve</a></li>
                                            <li><a href="javascript:void(0);" class=" waves-effect waves-block">Edit</a></li>
                                        </ul>
                                    </li>
                                    
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                    <div class="row pull-right">
                    {!! $drivers->appends(request()->all())->render() !!}
                    <div>
                </div></small>
            </div>
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>
@endsection
@section('bottom')
<script src="{{url('admin_assets/admin_bsb')}}/js/pages/forms/basic-form-elements.js"></script>
<script>

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


$(".sort-by").on('click', function(){
    
    var order_by = $(this).data('order-by');
    var order = $(this).data('order');
    var urlVars = getUrlVars();
    urlVars.order_by=order_by;
    urlVars.order=order;

    var url = '{{url("admin/drivers")}}' + objectToQueryString(urlVars);
    
    console.log(url)
    window.location.href = url;
   

});

</script>
@endsection