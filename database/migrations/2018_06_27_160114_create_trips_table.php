<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('driver_id')->unsigned();
            $table->string('name', 256);
            $table->string('from', 256);
            $table->string('to', 256);
            $table->tinyInteger('seats')->default(1);
            $table->tinyInteger('seats_available')->default(1);
            $table->dateTime('trip_datetime');
            $table->dateTime('start_time')->default('0000-00-00 00:00:00');
            $table->dateTime('end_time')->default('0000-00-00 00:00:00');
            $table->string('status', 50)->default('CREATED');
            $table->bigInteger('admin_route_ref_id')->unsigned()->comment('Stores the admin routes table id for reference');
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
        Schema::dropIfExists('trips');
    }
}
