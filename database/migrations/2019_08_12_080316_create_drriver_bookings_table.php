<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrriverBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('driver_id')->unsigned();
            $table->bigInteger('package_id')->unsigned();
            $table->string('pickup_address', 500);
            $table->decimal('pickup_latitude', 10, 8)->default(0.0);
            $table->decimal('pickup_longitude', 10, 8)->default(0.0);
            $table->dateTime('datetime')->default('0000-00-00 00:00:00');
            $table->dateTime('driver_started')->default('0000-00-00 00:00:00');
            $table->dateTime('driver_reached')->default('0000-00-00 00:00:00');
            $table->dateTime('trip_started')->default('0000-00-00 00:00:00');
            $table->dateTime('trip_ended')->default('0000-00-00 00:00:00');
            $table->string('car_transmission', 2)->default('01'); //01 -> automatic, 10 -> manual
            $table->string('status', 50);
            $table->bigInteger('invoice_id')->unsigned()->comment('ride requests invoices table id');
            $table->string('payment_mode', 50)->default('CASH');
            $table->string('payment_status', 50)->default('NOT_PAID');
            $table->integer('user_rating')->default(0);
            $table->integer('driver_rating')->default(0);
            $table->string('start_otp', 6);

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
        Schema::dropIfExists('drriver_bookings');
    }
}
