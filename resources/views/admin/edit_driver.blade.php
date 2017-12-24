@extends('admin.layouts.master')
@section('driver_list_active', 'active')
@section('driver_active', 'active')
@section('title', 'Edit '.$driver->fname)
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
<div class="block-header">
    <h2>EDIT DRIVER</h2>
</div>
<!-- With Material Design Colors -->
<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="header">
            <h2>
                View, edit and update driver
            </h2>
        </div>
    


        <div class="body">
                    
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Username">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="password" class="form-control" placeholder="Password">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


    </div>
    <!-- #END# With Material Design Colors -->
</div>

@endsection
@section('bottom')
<script>
</script>
@endsection