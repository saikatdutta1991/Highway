<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSupportTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_support_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('number', 50);
            $table->string('type', 256);
            $table->text('description')->nullable();
            $table->string('status', 50);
            $table->string('remarks', 256)->nullable();
            $table->string('photo1', 512)->nullable();
            $table->string('photo2', 512)->nullable();
            $table->string('photo3', 512)->nullable();
            
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
        Schema::dropIfExists('user_support_tickets');
    }
}
