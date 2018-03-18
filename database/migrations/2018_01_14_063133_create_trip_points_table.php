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
            $table->tinyInteger('order')->default(0);
            $table->integer('distance')->default(0)->comment('used to store distance between previuos order point in km');
            $table->integer('time')->default(0)->comment('used to store travel time between previuos order point');
            $table->string('status', 50)->default('');
            $table->string('address', 500);
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
