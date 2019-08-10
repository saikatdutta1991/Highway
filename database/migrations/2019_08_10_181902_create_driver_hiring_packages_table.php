<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriverHiringPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_hiring_packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('hours')->default(0)->comment("Pakcge default duration time in hours");
            $table->decimal('charge', 10, 2)->default(0.00)->comment("Default amount for default hours time duration");
            $table->decimal('per_hour_charge', 10, 2)->default(0.00)->comment("Per hour charge after default time exceeded");
            $table->string("night_hours", 15)->default('')->comment("Night charge will be count between this time gap");
            $table->decimal('night_charge', 10, 2)->default(0.00);
            $table->integer('grace_time')->default(0)->comment("After default package time, user will be given grace time in minutes");
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
        Schema::dropIfExists('driver_hiring_packages');
    }
}
