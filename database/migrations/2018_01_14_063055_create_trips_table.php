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
            $table->string('trip_name', 256);
            $table->integer('seats');
            $table->integer('seats_available');

            $table->string('source_address', 256);
            $table->decimal('source_latitude', 10, 7)->default(0.0);
            $table->decimal('source_longitude', 10, 7)->default(0.0);
            $table->string('destination_address', 256);
            $table->decimal('destination_latitude', 10, 7)->default(0.0);
            $table->decimal('destination_longitude', 10, 7)->default(0.0);

            $table->timestamp('trip_date_time');
            $table->string('trip_status', 128);

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
