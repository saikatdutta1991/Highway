@extends('admin.layouts.master')
@section('title', 'Services')
@section('services_active', 'active')
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
    <!-- <div class="row clearfix">
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
    </div> -->
    <!-- With Material Design Colors -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        LIST OF ALL SERVICES
                        <small>Here you can see all services, add, edit, update services</small>
                    </h2>
                    <ul class="header-dropdown m-r--5">
                                <li>
                                    <a href="javascript:void(0);" data-toggle="tooltip" data-placement="left" title="Add new service" id="add-service-btn">
                                        <i class="material-icons col-pink">add</i>
                                    </a>
                                </li>
                                <!-- <li class="dropdown">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <ul class="dropdown-menu pull-right">
                                        <li><a href="javascript:void(0);">Action</a></li>
                                        <li><a href="javascript:void(0);">Another action</a></li>
                                        <li><a href="javascript:void(0);">Something else here</a></li>
                                    </ul>
                                </li> -->
                            </ul>
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
                            <tr id="service_row_{{$service['id']}}" data-service-name="{{$service['name']}}">
                                <td>{{$service['id']}}</td>
                                <td>{{$service['name']}}</td>
                                <td>{{date('d M, Y', strtotime($service['created_at']))}}</td>
                                <td>{{$service['used_by_driver']}}</td>
                                <td style="">

                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn bg-pink btn-xs waves-effect dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                           <i class="material-icons">view_list</i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            <li class="service-edit-btn" data-service-id="{{$service['id']}}"><a href="javascript:void(0);" class=" waves-effect waves-block"><i class="material-icons col-green">mode_edit</i>Edit</a></li>
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



<div class="modal fade" id="service_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="smallModalLabel">ADD OR UPDATE SERVICE</h4>
            </div>
            <div class="modal-body">
                <div class="row clearfix">
                    <form id="service_add_form">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="form-line">
                                <b>Service Name</b>
                                <input type="hidden" class="form-control" name="_token" value="{{csrf_token()}}">
                                <input type="hidden" class="form-control" name="service_id" value="">
                                <input type="hidden" class="form-control" name="_action" value="">
                                <input type="text" class="form-control" placeholder="Ex: Prime" name="service_name" onkeyup="this.value=this.value.charAt(0).toUpperCase() + this.value.slice(1)">
                            </div>
                        </div>
                    </div>
                    </form>
                    <div class="col-sm-12" id="add-service-error-div" style="display:none">
                        <div class="preloader pl-size-xs" style="float:left;margin-right: 5px;display:none">
                            <div class="spinner-layer pl-red-grey">
                                <div class="circle-clipper left">
                                    <div class="circle"></div>
                                </div>
                                <div class="circle-clipper right">
                                    <div class="circle"></div>
                                </div>
                            </div>
                        </div>
                        <h6 class="res-text" style="float:left;display:none">Sending ...</h6>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" id="service-save-btn">SAVE</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('bottom')
<script>

$("#add-service-btn").on('click', function(){
    hideMessageErrorResDiv()
    $("#service_modal input[name='service_id']").val('')
    $("#service_modal input[name='service_name']").val('')
    $("#service_modal input[name='_action']").val('add')
    $("#service_modal").modal('show');

    setTimeout(function(){
        $("#service_modal input[name='service_name']").focus()
    },500)

})


$(".service-edit-btn").on('click',function(){
    hideMessageErrorResDiv()
    var service_id = $(this).data('service-id');
    var service_name = $('#service_row_'+service_id).data('service-name')
    $("#service_modal input[name='service_id']").val(service_id)
    $("#service_modal input[name='service_name']").val(service_name)
    $("#service_modal input[name='_action']").val('update')
    $("#service_modal").modal('show');
    setTimeout(function(){
        $("#service_modal input[name='service_name']").focus().select()
    },500)
    
});

var service_add_url = "{{url('admin/services/add')}}";

$("#service-save-btn").on('click', function(){

    var data = $("#service_add_form").serializeArray();

    console.log(data)

    showServiceAddLoader('Saving ...')

    $.post(service_add_url, data, function(response){

        if(response.success) {
            $("#service_modal").modal('hide');
            swal("Service saved. Wait till services refresh", "", "success"); 

            setTimeout(function(){
                window.location.reload()
            },1000)
            
        } else {
            showServiceAddError(response.text);
        }


    }).fail(function(){
        showServiceAddError('Internal server error. Try later.')
    });

})



function hideMessageErrorResDiv()
{
    $("#add-service-error-div").hide();
    $("#add-service-error-div > .preloader").hide()
    $("#add-service-error-div > .res-text").hide()
}

function showServiceAddLoader(message)
{
    $("#add-service-error-div").show();
    $("#add-service-error-div > .preloader").show()
    $("#add-service-error-div > .res-text").show().text(message).removeClass('col-red').addClass('col-black');
}

function showServiceAddError(message)
{
    $("#add-service-error-div").show();
    $("#add-service-error-div > .preloader").hide()
    $("#add-service-error-div > .res-text").show().text(message).addClass('col-red').removeClass('col-black');
}

</script>
@endsection