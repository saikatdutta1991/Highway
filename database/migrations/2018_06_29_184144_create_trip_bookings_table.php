<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('trip_id')->unsigned()->comment('Driver created trips table id');
            $table->bigInteger('boarding_point_id')->unsigned()->comment('Trip points table id');
            $table->bigInteger('dest_point_id')->unsigned()->comment('Trip points table id');
            $table->tinyInteger('booked_seats');
            $table->string('booking_status', 50);
            $table->bigInteger('invoice_id')->unsigned()->comment('ride requests invoices table id');
            $table->integer('user_rating')->default(0);
            $table->integer('driver_rating')->default(0);
            $table->string('payment_mode', 50)->default('ONLINE');
            $table->string('payment_status', 50)->default('NOT_PAID');
            $table->dateTime('boarding_time')->nullable()->default(null);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_bookings');
    }
}
