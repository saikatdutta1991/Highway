<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminRoutePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_route_points', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->bigInteger('admin_route_id')->unsigned()->comment('foreignkey for admin route id');
            
            $table->tinyInteger('order')->default(0)->comment('maintain point order');
            
            $table->string('address', 500);
            $table->decimal('latitude', 10, 7)->default(0.0);
            $table->decimal('longitude', 10, 7)->default(0.0);
            $table->string('city', 100);
            $table->string('country', 100);
            $table->string('zip_code', 100);
            
            $table->string('tag', 100)->comment('wheather source destination or intermediate points');
           
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
