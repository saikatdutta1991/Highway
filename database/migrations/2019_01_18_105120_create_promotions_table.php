<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 128);
            $table->string('broadcast_type', 50);
            $table->boolean('has_sms')->default(true);
            $table->string('sms_text', 1600)->default('');
            $table->boolean('has_pushnotification')->default(true);
            $table->string('pushnotification_title', 256)->default('');
            $table->string('pushnotification_message', 500)->default('');
            $table->boolean('has_email')->default(true);
            $table->longText('email_subject')->default('');
            $table->longText('email_content')->default('');
            $table->string('email_file', 256)->default('');
            $table->string('status', 50);
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
        Schema::dropIfExists('promotions');
    }
}
