<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminRoutePathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_route_paths', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('admin_route_id')->unsigned()->comment('foreignkey for admin route id');
            
            $table->string('s_address', 500);
            $table->decimal('s_latitude', 10, 7)->default(0.0);
            $table->decimal('s_longitude', 10, 7)->default(0.0);
            $table->string('s_city', 100);
            $table->string('s_country', 100);
            $table->string('s_zip_code', 100);

            $table->string('d_address', 500);
            $table->decimal('d_latitude', 10, 7)->default(0.0);
            $table->decimal('d_longitude', 10, 7)->default(0.0);
            $table->string('d_city', 100);
            $table->string('d_country', 100);
            $table->string('d_zip_code', 100);
           
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
        Schema::dropIfExists('admin_route_paths');
    }
}
