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
                        @if(isset($promotion))
                        <ul class="header-dropdown m-r--5">
                            <li class="dropdown">
                                <a target="_blank" href="{{route('admin.promotion.email.preview', ['promotion_id' => $promotion->id])}}" data-toggle="tooltip" data-placement="left" title="Preview Email">
                                <i class="material-icons">visibility</i>
                                </a>
                            </li>
                        </ul>
                        @endif
                    </h2>
                </div>
                <div class="body">
                    <form id="promotion_form">
                        @if(isset($promotion))
                        <input type="hidden" name="id" value="{{$promotion->id}}">
                        @endif
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Title</b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" name="title" value="@if(isset($promotion)){{$promotion->title}}@endif" placeholder="Enter your promotion title">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Broadcast Type</b>
                                    <div class="form-line">
                                        <select class="form-control" name="broadcast_type">
                                        <option value="broadc_users" @if(isset($promotion) && $promotion->broadcast_type == 'broadc_users') selected @endif>Users</option>
                                        <option value="broadc_drivers" @if(isset($promotion) && $promotion->broadcast_type == 'broadc_drivers') selected @endif>Drivers</option>
                                        <option value="broadc_all" @if(isset($promotion) && $promotion->broadcast_type == 'broadc_all') selected @endif>All</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="switch">
                                    <label for="has_sms">Has Sms</label>
                                    <label>
                                    <input type="checkbox" @if(!isset($promotion) || $promotion->has_sms) checked @endif id="has_sms" name="has_sms">
                                    <span class="lever switch-col-deep-orange"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12" id="sms_text_div">
                                <div class="form-group">
                                    <b>Sms Text
                                    </b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="sms_text" value="@if(isset($promotion)){{$promotion->sms_text}}@endif">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="switch">
                                    <label for="has_pushnotification">Has Push Notificaiton</label>
                                    <label>
                                    <input type="checkbox" @if(!isset($promotion) || $promotion->has_pushnotification) checked @endif id="has_pushnotification" name="has_pushnotification">
                                    <span class="lever switch-col-deep-orange"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6" id="pushnotification_title_div">
                                <div class="form-group">
                                    <b>Push Notification Title
                                    </b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="pushnotification_title" value="@if(isset($promotion)){{$promotion->pushnotification_title}}@endif">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6" id="pushnotification_message_div">
                                <div class="form-group">
                                    <b>Push Notification Message
                                    </b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="pushnotification_message" value="@if(isset($promotion)){{$promotion->pushnotification_message}}@endif">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="switch">
                                        <label for="has_email">Has Email Content</label>
                                        <label>
                                        <input type="checkbox" @if(!isset($promotion) || $promotion->has_email) checked @endif id="has_email" name="has_email">
                                        <span class="lever switch-col-deep-orange"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12" id="email_subject_div">
                                <div class="form-group">
                                    <b>Email Subject
                                    </b>
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="email_subject" value="@if(isset($promotion)){{$promotion->email_subject}}@endif" placeholder="Email Subject">
                                    </div>
                                </div>
                                <label>Email Content</label>
                                <textarea name="email_content" id="email_content" style="height:500px">
                                    @if(isset($promotion))
                                        {!! $promotion->email_content !!}
                                    @else
                                        &lt;p&gt;Edit your promotion content here.&lt;/p&gt;
                                    @endif
                                </textarea>
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
    CKEDITOR.replace( 'email_content', { allowedContent: true, height: 500  });
    
    function loadDefualtTemplate()
    {
        $.get(sampletemplate, function(response){
            CKEDITOR.instances.email_content.setData(response)
        });
    }
    
    var sampletemplate = '{{route("admin.promotion.sample-template")}}';
    var savepromotionapi = '{{route("admin.save.promotion")}}'
    
    $(document).ready(function(){
    
    
    
        /** save promotion */
        $("#promotion_form").on('submit', function(event){
            event.preventDefault();
    
            var data = $(this).serializeArray();
    
            data.push({
                name : 'has_pushnotification',
                value : $("#has_pushnotification").is(':checked')?1:0
            })
            data.push({
                name : 'has_email',
                value : $("#has_email").is(':checked')?1:0
            })
            data.push({
                name : 'email_content',
                value : $("#has_email").is(':checked')?CKEDITOR.instances.email_content.getData():''
            })
            data.push({
                name : 'has_sms',
                value : $("#has_sms").is(':checked')?1:0
            })
    
            console.log(data)
    
            $.post(savepromotionapi, data, function(response){
                console.log(response)
    
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
    
    
    
        $("#has_pushnotification").on('change', function(){
    
            if($(this).is(':checked')) {
                $("#pushnotification_title_div").show()
                $("#pushnotification_message_div").show()
            } else {
                $("#pushnotification_title_div").hide()
                $("#pushnotification_message_div").hide()
            }
    
        }).change();
    
        $("#has_email").on('change', function(){
    
            if($(this).is(':checked')) {
                //$("#email_content").show()
                $("#email_subject_div").show()
            } else {
                //$("#email_content").hide()
                $("#email_subject_div").hide()
            }
    
        }).change();


        $("#has_sms").on('change', function(){
    
            if($(this).is(':checked')) {
                //$("#email_content").show()
                $("#sms_text_div").show()
            } else {
                //$("#email_content").hide()
                $("#sms_text_div").hide()
            }
    
        }).change();
    
    
        @if(!isset($promotion) || !$promotion->has_email)
        loadDefualtTemplate();
        @endif
        
    
    });
    
</script>
@endsection