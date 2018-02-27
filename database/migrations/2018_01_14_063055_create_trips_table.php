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
            $table->string('name', 128);
            $table->integer('no_of_seats')->default(0);
            $table->timestamp('date_time')->nullable();
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
        Schema::dropIfExists('trips');
    }
}
