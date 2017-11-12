<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->string('fname', 128);
            $table->string('lname', 128);
            $table->string('email', 128);
            $table->tinyInteger('is_email_verified')->default(0);
            $table->string('password', 1000);
            $table->string('country_code', 20);
            $table->string('mobile_number', 20);
            $table->string('full_mobile_number', 20);
            $table->tinyInteger('is_mobile_number_verified')->default(0);
            $table->string('status', 50)->default('ACTIVATED');
            $table->timestamp('last_access_time')->useCurrent();
            $table->ipAddress('last_accessed_ip');
            $table->string('profile_photo_path', 256);
            $table->string('profile_photo_name', 128);
            $table->string('vehicle_type', 128);
            $table->string('vehicle_number', 128);

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
        Schema::dropIfExists('drivers');
    }
}
