<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_routes', function (Blueprint $table) {
            
            $table->increments('id');

            $table->bigInteger('trip_id')->unsigned();

            $table->string('start_point_address', 500);
            $table->decimal('start_point_latitude', 10, 7)->default(0.0);
            $table->decimal('start_point_longitude', 10, 7)->default(0.0);
            $table->tinyInteger('start_point_order')->default(0);

            $table->string('end_point_address', 500);
            $table->decimal('end_point_latitude', 10, 7)->default(0.0);
            $table->decimal('end_point_longitude', 10, 7)->default(0.0);
            $table->tinyInteger('end_point_order')->default(0);

            $table->string('seat_affects', 128)->default('');
            $table->integer('seats_available')->default(0);

            $table->decimal('estimated_distance', 10, 1)->default(0.0);
            $table->integer('estimated_time')->default(0);
            $table->decimal('estimated_fare', 10, 2)->default(0.00);

            $table->timestamp('reached_timestamp')->nullable();
            $table->timestamp('start_timestamp')->nullable();
            $table->timestamp('end_timestamp')->nullable();

            $table->string('status', 128);


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
        Schema::dropIfExists('trip_routes');
    }
}
