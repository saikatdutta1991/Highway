@extends('home.layouts.master')
@section('title', "Track Booking : {$booking->booking_id}")
@section('top-header')
<style>
    h4 {
    color:white
    }
    .acard {
    margin-bottom:20px;
    border-radius:0px;
    /* box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24); */
    transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    }
    .acard:hover {
    box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
    }
    .acard .card-header {
    background: #f56091;
    }
    .acard .card-header h5 .btn {
    color:white;
    font-size: 15px;
    }
    .btn-link:hover {
    cursor:pointer;
    }
    .btn-link:hover {
    text-decoration: none;
    }
    .s-section {
    padding : 40px 0;
    }
    /* background: #f56091; */
</style>
@endsection
@section('content')
<header class="bg-gradient" style="padding-bottom: 3rem">
    <div class="container">
        <h4>Track Your Booking</h4>
    </div>
</header>
<div class="section s-section">
    <div class="container">
        <div class="card acard">
            <div class="collapse show">
                <div class="card-body">
                    <small>STATUS</small>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">Booked</div>
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card acard">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#booking-details" aria-expanded="true" aria-controls="booking-details">
                            Booking Details
                            </button>
                        </h5>
                    </div>
                    <div id="booking-details" class="collapse show" aria-labelledby="headingOne">
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr>
                                        <td>Booking ID</td>
                                        <th scope="row">{{$booking->booking_id}}</th>
                                    </tr>
                                    <tr>
                                        <td>Trip</td>
                                        <td>{{$trip->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Pickup Address</td>
                                        <td>{{$pickupPoint->address}}</td>
                                    </tr>
                                    <tr>
                                        <td>Drop Address</td>
                                        <td>{{$dropPoint->address}}</td>
                                    </tr>
                                    <tr>
                                        <td>Seats</td>
                                        <td>{{$booking->booked_seats}}</td>
                                    </tr>
                                    <tr>
                                        <td>Booking Date</td>
                                        <td>{{$booking->tripFormatedCratedTimestamp()}}</td>
                                    </tr>
                                    <tr>
                                        <td>Journey Date</td>
                                        <td>{{$trip->tripFormatedTimestampString($user->timezone)}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card acard">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#driver-details" aria-expanded="false" aria-controls="driver-details">
                            Driver Details
                            </button>
                        </h5>
                    </div>
                    <div id="driver-details" class="collapse show">
                        <div class="card-body row">

								<div class="col-md-4 col-sm-4">
									<img class="card-img-top" src="{{$driver->profilePhotoUrl()}}" alt="Card image">
								</div>
								<div class="col-md-8 col-sm-8">
									<table class="table table-bordered table-sm">
										<tbody>
											<tr>
												<td>Name</td>
												<th scope="row">{{$driver->fname}}{{$driver->lname}}</th>
											</tr>
											<tr>
												<td>Contact</td>
												<td>{{$driver->fullMobileNumber()}}</td>
											</tr>
											<tr>
												<td>Car</td>
												<td>{{$driver->vehicle_type}}</td>
											</tr>
											<tr>
												<td>Car Number</td>
												<td>{{$driver->vehicle_number}}</td>
											</tr>
										</tbody>
									</table>
								</div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card acard">
            <div class="card-header">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#invoice-details" aria-expanded="false" aria-controls="invoice-details">
                    Invoice
                    </button>
                </h5>
            </div>
            <div id="invoice-details" class="collapse">
                <div class="card-body">
                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                </div>
            </div>
        </div>
        <div class="card acard">
            <div class="card-header">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#map-tracking" aria-expanded="false" aria-controls="map-tracking">
                    Map Tracking
                    </button>
                </h5>
            </div>
            <div id="map-tracking" class="collapse">
                <div class="card-body">
                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                </div>
            </div>
        </div>
    </div>
</div>
@include('home.layouts.address')
@endsection