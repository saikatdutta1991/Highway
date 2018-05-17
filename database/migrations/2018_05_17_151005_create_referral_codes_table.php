<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferralCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_codes', function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('code', 128)->default('')->comment('referral code');
            $table->string('e_type', 50)->comment('entity type(user/driver');
            $table->bigInteger('e_id')->unsigned()->comment('entity id');
            $table->integer('bonus_amount')->default(0)->comment('referral bonus amount');
            $table->string('status', 128)->default('ENABLED')->comment('user can user or disabled');
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
        Schema::dropIfExists('referral_codes');
    }
}
