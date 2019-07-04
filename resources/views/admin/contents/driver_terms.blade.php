@extends('admin.layouts.master')
@section('content_management_active', 'active')
@section('driver_terms_active', 'active')
@section('title', 'Driver Terms Content Management')
@section('top-header')
<!-- Bootstrap Select Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>TERMS & CONDITIONS</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        DRIVER TERMS & CONDITIONS CONTENT MANAGEMENT
                        <small>Add your driver TERMS & CONDITIONS content here</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="terms_form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <div class="form-line">
                                    <textarea name="terms" id="terms" style="height:500px">
                                        {!! $terms !!}
                                    </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn bg-pink waves-effect">
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
<script src="https://cdn.ckeditor.com/4.11.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace( 'terms', { allowedContent: true, height: 500  });

    var  termssaveapi = "{{route('admin.save.content.driver.terms')}}"

    $(document).ready(function(){

        $("#terms_form").on('submit', function(event){

            event.preventDefault();

            var data = $(this).serializeArray();

            data.push({
                name : 'terms',
                value : CKEDITOR.instances.terms.getData()
            })

            console.log(data)
    
            $.post(termssaveapi, data, function(response){
                console.log(response)
    
                if(response.success){
                    showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    return;
                }
    
                showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
    
            }).fail(function(){
                showNotification('bg-black', 'Internal server error. Try again !!', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            })
    
        })
    })
</script>
@endsection