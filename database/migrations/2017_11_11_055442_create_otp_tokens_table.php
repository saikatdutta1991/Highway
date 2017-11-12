<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtpTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp_tokens', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->string('token', 50);
            $table->string('country_code', 20);
            $table->string('mobile_number', 20);
            $table->string('full_mobile_number', 20);
            $table->timestamp('expired_at');
            
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
        Schema::dropIfExists('otp_tokens');
    }
}
