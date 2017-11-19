<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVotesToAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->string('name', 128);
            $table->string('email', 128)->unique();
            $table->string('password', 1000);
            $table->string('last_ip', 20);
            $table->timestamp('last_login_time');
            $table->string('role', 50)->default('ROOT');
            $table->string('purpose', 128)->default('');

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
        Schema::create('admins', function (Blueprint $table) {
            //
        });
    }
}
