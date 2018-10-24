<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_codes', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('code', 128);
            $table->string('name', 256)->default('');
            $table->string('description', 500)->default('');
           /*  $table->integer('uses')->default(0); */
            $table->integer('max_uses')->default(0); // max_uses 0 means infinite use
            $table->integer('max_uses_user')->default(0); // max_uses_user 0 means infinite use
            $table->string('type', 50)->default(''); // coupon type
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->string('discount_type', 50)->default('');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
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
        Schema::dropIfExists('coupon_codes');
    }
}
