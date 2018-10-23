@extends('admin.layouts.master')
@section('coupons_active', 'active')
@section('coupons_add_active', 'active')
@section('title', 'Add Coupon')
@section('top-header')
<!-- Bootstrap Material Datetime Picker Css -->
<link href="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>NEW COUPON</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        NEW OR EDIT COUPON
                        <small>Here you can add new coupon or edit existing coupon</small>
                    </h2>
                </div>
                <div class="body">
                    <form id="add-coupon-form">
                        {!! csrf_field() !!}
                        <div class="row clearfix">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Code</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Coupon code that will be entered by user before taking trip. Better not to use space">help_outline</i>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: BLR20" name="code" value="@if(isset($coupon)){{$coupon->code}}@endif" onkeyup="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Name</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Contains human readable name for coupon">help_outline</i>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: Dussehra Offer" name="name" value="@if(isset($coupon)){{$coupon->name}}@endif">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>Description</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Full description of coupon">help_outline</i>
                                    <div class="form-line">
                                        <textarea required class="form-control" placeholder="Ex: Dussehra Offer: get Rs.250 off on your first Ola Outstation ride" name="description">@if(isset($coupon)){{$coupon->description}}@endif</textarea>
                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Maximum Uses</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="How many times can this coupon be used by all">help_outline</i>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" placeholder="Ex: 10" name="max_uses" value="@if(isset($coupon)){{$coupon->max_uses}}@endif">
                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Maximum Uses-per-User</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="How many times can this coupon be used by each user">help_outline</i>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" placeholder="Ex: 1" name="max_uses_user" value="@if(isset($coupon)){{$coupon->max_uses_user}}@endif">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Coupon Type</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Choose the type of the coupon. Means for city ride only or intercity ride or both">help_outline</i>
                                    <div class="form-line">
                                        <select class="form-control show-tick" name="type">
                                            <option value = "city_ride" @if(isset($coupon) && $coupon->type == 'city_ride') selected @endif>City Ride Only</option>
                                            <option value = "intracity_trip" @if(isset($coupon) && $coupon->type == 'intracity_trip') selected @endif>Intracity Trip Only</option>
                                            <option value = "all" @if(isset($coupon) && $coupon->type == 'all') selected @endif>All</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Discount Amount</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Discount amount depends on discount type if flat discount or percentage. Better to enter rounded integer">help_outline</i>
                                    <div class="form-line">
                                        <input type="number" step="1" pattern="\d*" required class="form-control" placeholder="Ex: 200" name="discount_amount" value="@if(isset($coupon)){{intval($coupon->discount_amount)}}@endif">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Discount Type</b>
                                    <div class="form-line">
                                        <select class="form-control show-tick" name="discount_type">
                                            <option value="flat" @if(isset($coupon) && $coupon->discount_type == 'flat') selected @endif>Flat Discount</option>
                                            <option value="percentage" @if(isset($coupon) && $coupon->discount_type == 'percentage') selected @endif>Percentage Discount</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Starts At</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Must be 12 hour time">help_outline</i>
                                    <div class="form-line">
                                        <input type="text" required class="form-control datetimepicker" placeholder="Ex: 10-12-1991 12:30 AM" name="starts_at" value="@if(isset($coupon)){{$coupon->formatedStartsAt($default_timezone)}}@endif">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Expires At</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Must be 12 hour time">help_outline</i>
                                    <div class="form-line">
                                        <input type="text" required class="form-control datetimepicker" placeholder="Ex: 10-12-1991 12:30 AM" name="expires_at" value="@if(isset($coupon)){{$coupon->formatedExpiresAt($default_timezone)}}@endif">
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 text-right">
                                <button type="submit" id="referral-bonus-amount-save-btn" class="btn bg-pink waves-effect">
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
<!-- Moment Plugin Js -->
<script src="{{url('admin_assets/admin_bsb')}}/plugins/momentjs/moment.js"></script>
<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="{{url('admin_assets/admin_bsb')}}/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
<script>

    var add_coupon_url = @if(isset($coupon)) '{{route("admin.coupons.update", ['coupon_id' => $coupon->id])}}' @else  '{{route('admin.coupons.add-new')}}' @endif

    $(document).ready(function(){

        $('.datetimepicker').bootstrapMaterialDatePicker({
            format: 'DD-MM-YYYY hh:mm A',
            shortTime : true
        });




        $("#add-coupon-form").on('submit', function(event){
            event.preventDefault();

            let data = $(this).serializeArray()            

            $.post(add_coupon_url, data, function(response){
                console.log(response)
                if(response.success) {
                    showNotification('bg-black', response.text+" , You will be redirected to coupon list", 'top', 'right', 'animated flipInX', 'animated flipOutX');
                    setTimeout(function(){

                        window.location.href = '{{route("admin.coupons.show")}}'

                    }, 1500)
                } else {
                    showNotification('bg-red', response.data.errors[Object.keys(response.data.errors)[0]], 'top', 'right', 'animated flipInX', 'animated flipOutX');
                }
            })
            .fail(function(response) {
                showNotification('bg-black', 'Unknown server error. Failed to approve driver', 'top', 'right', 'animated flipInX', 'animated flipOutX');
            });

        })


    })
</script>
@endsection