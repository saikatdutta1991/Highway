@extends('admin.layouts.master')
@section('title', 'Services')
@section('services_active', 'active')
@section('service_types_active', 'active')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
<style></style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>SERVICES</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        ADD OR EDIT SERVICE
                        <small>Here you can add or update service</small>
                    </h2>
                </div>
                <div class="body">

                    <div class="row clearfix">
                                <div class="col-md-3">
                                   
                                    <div class="form-group">
                                  
                                        <b>Service Name</b>
                             
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Ex: Prime">
                                        </div>
                                    </div>

                                </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- With Material Design Colors -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        LIST OF ALL SERVICES
                        <small>Here you can see all services, add, edit, update services</small>
                    </h2>
                </div>
                <div class="body table-responsive">
                    @if(!count($services))
                    <div class="alert bg-pink">
                        No services found
                    </div>
                    @else
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>SERVICE NAME</th>
                                <th>CREATED</th>
                                <th>NO. DRIVERS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                            <tr>
                                <td>{{$service['id']}}</td>
                                <td>{{$service['name']}}</td>
                                <td>{{date('d M, Y', strtotime($services[0]['created_at']))}}</td>
                                <td>{{$service['used_by_driver']}}</td>
                                <td style="">

                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn bg-pink btn-xs waves-effect dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                           <i class="material-icons">view_list</i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            <li><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-green">mode_edit</i>Edit</a></li>
                                            <li><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-red">delete</i>Delete</a></li>
                                        </ul>
                                    </div>

                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- #END# With Material Design Colors -->
</div>
@endsection
@section('bottom')
<script></script>
@endsection