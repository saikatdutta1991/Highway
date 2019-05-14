<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAcFlagToRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_trip_routes', function (Blueprint $table) {
            $table->boolean('is_ac_enabled')->default(false);
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->boolean('is_ac_enabled')->default(false);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_trip_routes', function (Blueprint $table) {
            //
        });
    }
}
