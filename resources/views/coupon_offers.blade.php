<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
<head>
    <title>Coupon Offers @ {{$website_name}}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        .coupon {
        border: 3px dashed #bcbcbc;
        border-radius: 10px;
        font-family: "HelveticaNeue-Light", "Helvetica Neue Light", 
        "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
        font-weight: 300;
        }
        .coupon #head {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        min-height: 56px;
        }
        .coupon #footer {
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        }
        #title .visible-xs {
        font-size: 12px;
        }
        .coupon #title img {
        font-size: 30px;
        height: 30px;
        margin-top: 5px;
        }
        @media screen and (max-width: 500px) {
        .coupon #title img {
        height: 15px;
        }
        }
        .coupon #title span {
        /* float: right; */
        margin-top: 5px;
        font-weight: 700;
        text-transform: uppercase;
        }
        .coupon-img {
        width: 100%;
        margin-bottom: 15px;
        padding: 0;
        }
        .items {
        margin: 15px 0;
        }
        .usd, .cents {
        font-size: 20px;
        }
        .number {
        font-size: 40px;
        font-weight: 700;
        }
        sup {
        top: -15px;
        }
        #business-info ul {
        margin: 0;
        padding: 0;
        list-style-type: none;
        text-align: center;
        }
        #business-info ul li { 
        display: inline;
        text-align: center;
        }
        #business-info ul li span {
        text-decoration: none;
        padding: .2em 1em;
        }
        #business-info ul li span i {
        padding-right: 5px;
        }
        .disclosure {
        padding-top: 15px;
        font-size: 11px;
        color: #bcbcbc;
        text-align: center;
        }
        .coupon-code {
        color: #333333;
        font-size: 15px;
        font-weight:700;
        }
        .exp {
        color: #f34235;
        }
        .print {
        font-size: 14px;
        float: right;
        }
        /*------------------dont copy these lines----------------------*/
        body {
        font-family: "HelveticaNeue-Light", "Helvetica Neue Light", 
        "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; 
        font-weight: 300;
        background: url('{{url('admin_assets')}}/background.jpg');
        }
        .row {
        margin: 30px 0;
        }
        #quicknav ul {
        margin: 0;
        padding: 0;
        list-style-type: none;
        text-align: center;
        }
        #quicknav ul li { 
        display: inline; 
        }
        #quicknav ul li a {
        text-decoration: none;
        padding: .2em 1em;
        }
        .btn-danger, 
        .btn-success, 
        .btn-info, 
        .btn-warning, 
        .btn-primary {
        width: 105px;
        }
        .btn-default {
        margin-bottom: 40px;
        }
        .row.display-flex {
        display: flex;
        flex-wrap: wrap;
        }
        .row.display-flex > [class*='col-'] {
        display: flex;
        flex-direction: column;
        }
        /*-------------------------------------------------------------*/
    </style>
</head>
<div class="container-fluid">
    <div class="row">
        <h1 class="text-center" style="color:white">Coupon Offers @ {{$website_name}}</h1>
    </div>
    @if(!$coupons->count())
    <h1 class="text-center" style="color: red;">No Offers</h1>
    @else
    <div class="row display-flex">
    
        @foreach($coupons as $coupon)
        <div class="col-md-4">
            <div class="panel panel-default coupon">
                <div class="panel-heading" id="head">
                    <div class="panel-title" id="title">
                        <span class="hidden-xs">{{$coupon->name}}</span>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <p class="">{{$coupon->description}}</p>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="coupon-code">
                        Code: {{$coupon->code}}
                        <span class="print">
                        <a href="#" class="btn btn-link"><i class="fa fa-lg fa-print"></i> Print Coupon</a>
                        </span>
                    </div>
                    <div class="exp">Expires: {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $coupon->expires_at)->setTimezone($default_timezone)->format('d-m-Y')}}</div>
                </div>
            </div>
        </div>
        @endforeach

    </div>
    <p class="text-center"><a href="#" class="btn btn-default">Back to top <i class="fa fa-chevron-up"></i></a></p>
    @endif
</div>