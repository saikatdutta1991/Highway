@extends('admin.layouts.master')
@section('settings_active', 'active')
@section('settings_seo_active', 'active')
@section('title', 'SEO Management')
@section('top-header')
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>SEO MANAGEMENT</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        HOME PAGE SEO SETTINGS
                        <small>Set your welcome page seo description and keywords</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="seo-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>Website Title
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Website welcome page title" data-content="This title will be shown in home page title">help_outline</i>
                                    </b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="" name="seo_title" value="{{$seo_title}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>Website SEO Meta Keywords
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="Website SEO Meta Keywords" data-content="This keywords will help users to search by for specific keywords">help_outline</i>
                                    </b>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="" name="seo_keywords" value="{{$seo_keywords}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>Website SEO Description
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" title="SEO Description" data-content="This should be meaningful sentence that will be shown in search engine">help_outline</i>
                                    </b>
                                    <div class="form-line">
                                        <textarea rows="1" required class="form-control no-resize auto-growth" 
                                            name="seo_description">{{$seo_description}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="seo-save-btn" class="btn bg-pink waves-effect">
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
<!-- Autosize Plugin Js -->
<script src="{{url('admin_assets/admin_bsb')}}/plugins/autosize/autosize.js"></script>
<script>
    $(document).ready(function(){
        autosize($('textarea'));


        var seo_save_url = '{{route("admin.save.seo")}}'
        $("#seo-form").on('submit', function(event){
            event.preventDefault();
    
            var data = $(this).serializeArray();
            console.log(data)
    
            $.post(seo_save_url, data, function(response){
                console.log(response)
    
                if(response.success){
                    showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    return;
                }
    
                showNotification('bg-black', 'Internal server error. Try again !!', 'top', 'right', 'animated flipInX', 'animated flipOutX');
    
            }).fail(function(){
                showNotification('bg-black', 'Internal server error. Try again !!', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            })
    
        })
        
    })
</script>
@endsection