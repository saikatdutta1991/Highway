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
            $table->bigInteger('from_location')->unsigned()->comment('admin trip location id');
            $table->bigInteger('to_location')->unsigned()->comment('admin trip location id');
            $table->time('time')->default('00:00')->comment('optional time');

            $table->decimal('base_fare', 10, 2)->default(0.00);
            $table->decimal('tax_fee', 10, 2)->default(0.00);
            $table->decimal('access_fee', 10, 2)->default(0.00);
            $table->decimal('total_fare', 10, 2)->default(0.00);

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
