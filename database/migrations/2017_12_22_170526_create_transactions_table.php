<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('trans_parent_id')->unsigned()->default(0);
            $table->string('trans_id', 256);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('currency_type', 50)->default('USD');
            $table->string('gateway',50)->default();
            $table->text('extra_info')->nullable();
            $table->string('status', 100);

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
        Schema::dropIfExists('transactions');
    }
}
