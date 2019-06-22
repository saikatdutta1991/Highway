<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriverInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('driver_id')->unsigned();
            $table->bigInteger('ride_id')->unsigned()->comment('can store city ride or highway ride trip id');
            $table->string('ride_type', 10);
            $table->decimal('ride_cost', 10, 2)->default(0.00);
            $table->decimal('tax', 10, 2)->default(0.00)->commect('duduct tax from ride cost');
            $table->decimal('admin_commission', 10, 2)->default(0.00)->comment('Admin commission percentage caculated on ride cost');
            $table->decimal('driver_earnings', 10, 2)->default(0.00)->comment('ride_cost - (tax + admin commisson)');
            $table->decimal('cancellation_charge', 10, 2)->default(0.00)->comment('cancellation charge debited from driver account');
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
        Schema::dropIfExists('driver_invoices');
    }
}
