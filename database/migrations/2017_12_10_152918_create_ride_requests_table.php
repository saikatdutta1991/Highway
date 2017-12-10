<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRideRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ride_requests', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('driver_id')->unsigned();
            $table->string('ride_vehicle_type');

            $table->string('source_address', 256);
            $table->decimal('source_latitude', 10, 7)->default(0.0);
            $table->decimal('source_longitude', 10, 7)->default(0.0);
            $table->string('destination_address', 256);
            $table->decimal('destination_latitude', 10, 7)->default(0.0);
            $table->decimal('destination_longitude', 10, 7)->default(0.0);

            $table->integer('ride_distance')->default(0);
            $table->integer('ride_time')->default(0);
            $table->decimal('estimated_fare', 10, 2)->default(0.00);

            $table->timestamp('ride_start_time')->nullable();
            $table->timestamp('ride_end_time')->nullable();

            $table->string('ride_status', 100)->default('CASH');
            
            $table->string('payment_mode', 50)->default('CASH');
            $table->string('payment_status', 50)->default('NOT_PAID');

            $table->bigInteger('ride_invoice_id')->default(0);

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
        Schema::table('ride_requests', function (Blueprint $table) {
            //
        });
    }
}
