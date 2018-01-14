<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRideRequestInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ride_request_invoices', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->string('invoice_reference');
            $table->string('payment_mode', 50)->default('CASH');
            $table->string('payment_status', 50)->default('NOT_PAID');
            $table->bigInteger('transaction_table_id')->unsigned()->default(0);
            $table->string('currency_type', 50)->default('USD');

            $table->decimal('ride_fare', 10, 2)->default(0.00);
            $table->decimal('access_fee', 10, 2)->default(0.00);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);

            $table->string('invoice_map_image_path', 256);
            $table->string('invoice_map_image_name', 128);

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
        Schema::dropIfExists('ride_request_invoices');
    }
}
