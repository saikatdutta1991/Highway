<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCarTypeAndOutstationToDriverBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_bookings', function (Blueprint $table) {
            $table->boolean("is_outstation")->default(true);
            $table->string("car_type", 128)->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_bookings', function (Blueprint $table) {
            //
        });
    }
}
