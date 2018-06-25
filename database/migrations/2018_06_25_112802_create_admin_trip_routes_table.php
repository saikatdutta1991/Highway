<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminTripRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_trip_routes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('from_location')->comment('admin trip location id');
            $table->bigInteger('to_location')->comment('admin trip location id');
            $table->time('time')->default('00:00')->comment('optional time');
            $table->string('status', 50)->default('ENABLED');
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
        Schema::dropIfExists('admin_trip_routes');
    }
}
