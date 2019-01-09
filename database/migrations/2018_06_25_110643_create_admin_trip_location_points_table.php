<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminTripLocationPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_trip_location_points', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('admin_trip_location_id')->unsigned();
            $table->string('label', 256)->comment('point short name');
            $table->string('address', 500);
            $table->decimal('latitude', 10, 8)->default(0.0);
            $table->decimal('longitude', 10, 8)->default(0.0);

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
        Schema::dropIfExists('admin_trip_location_points');
    }
}
