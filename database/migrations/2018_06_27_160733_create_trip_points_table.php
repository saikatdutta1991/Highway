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
            $table->string('address', 500);
            $table->string('label', 256);
            $table->string('tag', 50);
            $table->string('status', 50);
            $table->dateTime('reached_time')->default('0000-00-00 00:00:00');
            $table->decimal('latitude', 10, 7)->default(0.0);
            $table->decimal('longitude', 10, 7)->default(0.0);
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
