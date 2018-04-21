<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminTripPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_trip_points', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('admin_trip_id')->unsigned()->comment('foreignkey for admin tirp id');
            $table->tinyInteger('order')->default(0);
            $table->integer('distance')->default(0)->comment('used to store distance between previuos order point in km');
            $table->integer('time')->default(0)->comment('used to store travel time between previuos order point');
            $table->decimal('fare', 10, 2)->default(0.00)->comment('used to store travel fare including taxes and all between previuos order point');
            $table->string('status', 50)->default('');
            $table->string('address', 500);
            $table->decimal('latitude', 10, 7)->default(0.0);
            $table->decimal('longitude', 10, 7)->default(0.0);
            $table->string('city', 100);
            $table->string('country', 100);
            $table->string('zip_code', 100);
           
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
        Schema::dropIfExists('admin_trip_points');
    }
}
