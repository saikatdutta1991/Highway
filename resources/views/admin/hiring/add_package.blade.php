@extends('admin.layouts.master')
@section('hiring_active', 'active')
@section('hiring_package_add_active', 'active')
@section('title', 'Add new hiring package')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>HIRING PACKAGE MANAGEMENT</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        ADD OR EDIT PACKAGE
                        <small>Add or update hiring package details here.</small>
                    </h2>
                </div>
                <div class="body">
                    @if(request()->success == 1)
                    <div class="alert alert-success">
                        <strong>Success!</strong> Package save successfully.
                    </div>
                    @endif
                    <form id="package-form" method="POST" action="{{route('admin.hiring.package.add')}}">
                        <input type="hidden" value="@if($package){{$package->id}}@endif" name="id">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Duration(Hours)</b>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" name="hours" value="@if($package){{$package->hours}}@else{{0}}@endif">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Charge amount ({{$currency_symbol}})</b>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" name="charge" value="@if($package){{$package->charge}}@else{{'0.00'}}@endif" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Hourly charge amount after duration ({{$currency_symbol}})</b>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" name="per_hour_charge"  value="@if($package){{$package->per_hour_charge}}@else{{'0.00'}}@endif" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Night hour duration</b>
                                    <div class="form-line" style="display:flex;">
                                        <select class="form-control show-tick" name="night_from">
                                            <option value="">-- From --</option>
                                            @foreach($hours as $key => $value) 
                                            <option value="{{$key}}" @if($package && isset(explode("-", $package->night_hours)[0]) && explode("-", $package->night_hours)[0] == $key && $package->night_hours != '') selected @endif>{{$value}}</option>
                                            @endforeach
                                        </select>
                                        <select class="form-control show-tick" name="night_to">
                                            <option value="">-- To --</option>
                                            @foreach($hours as $key => $value) 
                                            <option value="{{$key}}" @if($package && isset(explode("-", $package->night_hours)[1]) && explode("-", $package->night_hours)[1] == $key) selected @endif>{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Night charge amount ({{$currency_symbol}})</b>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" name="night_charge" value="@if($package){{$package->night_charge}}@else{{'0.00'}}@endif" min="0" onblur="this.value=parseFloat(this.value).toFixed(2)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Grace time (Minutes.)</b>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" name="grace_time" value="@if($package){{$package->grace_time}}@else{{0}}@endif">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="general-website-save-btn" class="btn bg-pink waves-effect">
                                <i class="material-icons">save</i>
                                <span>SAVE</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('bottom')
<script>
</script>
@endsection