<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTripBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_trip_bookings', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('trip_id')->unsigned();
            $table->bigInteger('trip_route_id')->unsigned();
            $table->tinyInteger('no_of_seats_booked')->default(0);
            $table->tinyInteger('is_boarded')->default(0);

            $table->string('status', 100);
            
            $table->string('payment_mode', 50)->default('CASH');
            $table->string('payment_status', 50)->default('NOT_PAID');
            $table->bigInteger('trip_invoice_id')->default(0);

            $table->integer('user_rating')->default(0);
            $table->integer('driver_rating')->default(0);

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
        Schema::dropIfExists('users_trip_bookings');
    }
}
