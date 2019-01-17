@extends('admin.layouts.master')
@section('promotions_active', 'active')
@section('promotion_add_active', 'active')
@section('title', 'Add Promotion')
@section('top-header')
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>ADD NEW PROMOTION</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        FILL ALL PROMOTION DETAILS
                        <small>Create or update your website promotion here.</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Title</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" name="title" value="" placeholder="Enter your promotion title">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Broadcast Type</b>
                                    <div class="form-line">
                                        <select class="form-control" name="broadcast_type">
                                            <option value="users">Users</option>
                                            <option value="drivers">Drivers</option>
                                            <option value="all">All</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <!-- <div class="form-group"> -->
                                    <input checked type="checkbox" id="has_push_notification_content" class="filled-in chk-col-pink"/>
                                    <label for="has_push_notification_content">Has Push Notificaiton</label>
                                <!-- </div> -->
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Push Notification Title
                                    </b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" name="push_notification_title" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Push Notification Message
                                    </b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" name="push_notification_message" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input checked type="checkbox" id="has_email_content" class="filled-in chk-col-pink"/>
                                    <label for="has_email_content">Has Email Content</label>
                                    
                                    <textarea name="email_content" id="email_content">
                                        &lt;p&gt;Edit your promotion content here.&lt;/p&gt;
                                    </textarea>

                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="" class="btn bg-pink waves-effect">
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
@endsection
@section('bottom')
<script src="https://cdn.ckeditor.com/4.11.2/standard/ckeditor.js"></script>
<!-- <script src="{{url('admin_assets/admin_bsb')}}/plugins/autosize/autosize.js"></script> -->
<script>
 CKEDITOR.replace( 'email_content', {
    allowedContent: true
 });
</script>
@endsection