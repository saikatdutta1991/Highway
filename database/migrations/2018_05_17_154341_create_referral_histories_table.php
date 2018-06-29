<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferralHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_histories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('referrer_type', 50)->comment('referrer entity type(user/driver');
            $table->bigInteger('referrer_id')->unsigned()->comment('who is referring');
            $table->integer('referrer_bonus_amount')->default(0)->comment('referral bonus amount got');

            $table->string('referred_type', 50)->comment('referred entity type(user/driver');
            $table->bigInteger('referred_id')->unsigned()->comment('who is being referred, new user');
            $table->integer('referred_bonus_amount')->default(0)->comment('referred bonus amount got');
            
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
        Schema::dropIfExists('referral_histories');
    }
}
