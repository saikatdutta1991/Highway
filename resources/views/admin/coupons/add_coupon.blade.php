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
                                        <input type="text" required class="form-control" placeholder="Ex: BLR20" name="code" value="" onkeyup="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <b>Name</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Contains human readable name for coupon">help_outline</i>
                                    <div class="form-line">
                                        <input type="text" required class="form-control" placeholder="Ex: Dussehra Offer" name="name" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <b>Description</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Full description of coupon">help_outline</i>
                                    <div class="form-line">
                                        <textarea required class="form-control" placeholder="Ex: Dussehra Offer: get Rs.250 off on your first Ola Outstation ride" name="description"></textarea>
                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Maximum Uses</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="How many times can this coupon be used by all">help_outline</i>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" placeholder="Ex: 10" name="max_uses" value="">
                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Maximum Uses-per-User</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="How many times can this coupon be used by each user">help_outline</i>
                                    <div class="form-line">
                                        <input type="number" required class="form-control" placeholder="Ex: 1" name="max_uses_user" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <b>Coupon Type</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Choose the type of the coupon. Means for city ride only or intercity ride or both">help_outline</i>
                                    <div class="form-line">
                                        <select class="form-control show-tick" name="type">
                                            <option value = "city_ride" >City Ride Only</option>
                                            <option value = "intracity_trip">Intracity Trip Only</option>
                                            <option value = "all">All</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Discount Amount</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Discount amount depends on discount type if flat discount or percentage">help_outline</i>
                                    <div class="form-line">
                                        <input type="number" step="1" pattern="\d*" required class="form-control" placeholder="Ex: 200" name="discount_amount" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Discount Type</b>
                                    <div class="form-line">
                                        <select class="form-control show-tick" name="discount_type">
                                            <option value="flat" >Flat Discount</option>
                                            <option value="percentage">Percentage Discount</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Starts At</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Must be 24 hour time">help_outline</i>
                                    <div class="form-line">
                                        <input type="text" required class="form-control datetimepicker" placeholder="Ex: YYYY-MM-DD HH:MM" name="starts_at" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <b>Expires At</b>
                                    <i class="material-icons font-14 col-grey" style="cursor:pointer" data-trigger="hover" data-container="body" data-toggle="popover" data-placement="right" data-content="Must be 24 hour time">help_outline</i>
                                    <div class="form-line">
                                        <input type="text" required class="form-control datetimepicker" placeholder="Ex: YYYY-MM-DD HH:MM" name="expires_at" value="">
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

    var add_coupon_url = '{{route('admin.coupons.add-new')}}'

    $(document).ready(function(){

        $('.datetimepicker').bootstrapMaterialDatePicker({
            format: 'YYYY-MM-DD HH:mm',
            clearButton: true,
            weekStart: 1
        });




        $("#add-coupon-form").on('submit', function(event){
            event.preventDefault();

            let data = $(this).serializeArray()            

            $.post(add_coupon_url, data, function(response){
                console.log(response)
                if(response.success) {
                    showNotification('bg-black', response.text, 'top', 'right', 'animated flipInX', 'animated flipOutX');
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