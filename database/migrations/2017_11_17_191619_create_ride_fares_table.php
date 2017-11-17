<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRideFaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ride_fares', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->bigInteger('vehicle_type_id')->unsigned();
            
            $table->decimal('minimun_price', 10, 2)->default(0.00);
            $table->decimal('access_fee', 10, 2)->default(0.00);
            
            $table->decimal('base_price', 10, 2)->default(0.00);
            
            $table->integer('first_distance')->default(0);
            $table->decimal('first_distance_price', 10, 2)->default(0.00);

            $table->decimal('after_first_distance_price', 10, 2)->default(0.00);
            $table->decimal('wait_time_price', 10, 2)->default(0.00);

            $table->decimal('cancellation_fee', 10, 2)->default(0.00);
            
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
        Schema::dropIfExists('ride_fares');
    }
}
