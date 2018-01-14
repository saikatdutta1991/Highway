<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_points', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->bigInteger('trip_id')->unsigned();
            $table->bigInteger('trip_points_parent_id')->unsigned()->default(0);
            $table->integer('seats');

            $table->string('source_address', 256);
            $table->decimal('source_latitude', 10, 7)->default(0.0);
            $table->decimal('source_longitude', 10, 7)->default(0.0);
            $table->string('destination_address', 256);
            $table->decimal('destination_latitude', 10, 7)->default(0.0);
            $table->decimal('destination_longitude', 10, 7)->default(0.0);

            $table->decimal('estimated_trip_distance', 10, 1)->default(0.0);
            $table->integer('estimated_trip_time')->default(0);
            $table->timestamp('trip_start_time')->nullable();
            $table->timestamp('trip_end_time')->nullable();
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
        Schema::dropIfExists('trip_points');
    }
}
