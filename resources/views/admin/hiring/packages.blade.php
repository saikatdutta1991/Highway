@extends('admin.layouts.master')
@section('title', 'Packages')
@section('hiring_active', 'active')
@section('hiring_packages_active', 'active')
@section('top-header')
<style></style>
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>DRIVER HIRING PACKAGES</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="header">
            <h2>
                LIST OF ALL PACKAGES
                <small>These are all driver hiriing pacakges</small>
            </h2>
        </div>
        <small>
        <div class="body table-responsive">
            <table class="table table-condensed table-hover">
                <thead>
                    <tr>                        
                        <th>NAME</th>
                        <th>DETAILS</th>
                        <th>CHARGE ({{$currency_symbol}})</th>
                        <th>GRACE TIME</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($packages as $package)
                    <tr>                        
                        <td><a data-toggle="tooltip" data-placement="left" title="Click to edit" href="{{route('admin.hiring.package.add.show', ['id' => $package->id])}}">{{$package->name}}</a></td> 
                        <td>{{$package->hours}} Hours - {{$currency_symbol}} {{$package->charge}} After {{$package->per_hour_charge}}/Hour</td>
                        <td>
                            @if($package->night_hours == '')
                            N/A
                            @else
                            {{$currency_symbol}} {{$package->night_charge}} Between {{$hours[explode("-", $package->night_hours)[0]]}} - {{$hours[explode("-", $package->night_hours)[1]]}}
                            @endif
                        </td>
                        <td>{{$package->grace_time}} Min.</td>
                    </tr>
                
                    @endforeach
                </tbody>
            </table>
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