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
            $table->string('status_reason', 128);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_connected_to_socket')->default(false);
            $table->decimal('rating', 1, 1)->default(0.0);
            $table->timestamp('last_access_time')->useCurrent();
            $table->ipAddress('last_accessed_ip');
            $table->string('profile_photo_path', 256);
            $table->string('profile_photo_name', 128);
            $table->string('vehicle_type', 128);
            $table->string('vehicle_number', 128);


            $table->decimal('latitude', 10, 7)->default(0.0);
            $table->decimal('longitude', 10, 7)->default(0.0);


            $table->string('vehicle_rc_photo_path', 256);
            $table->string('vehicle_rc_photo_name', 128);

            $table->string('vehicle_contract_permit_photo_path', 256);
            $table->string('vehicle_contract_permit_photo_name', 128);

            $table->string('vehicle_insurance_certificate_photo_path', 256);
            $table->string('vehicle_insurance_certificate_photo_name', 128);

            $table->string('vehicle_fitness_certificate_photo_path', 256);
            $table->string('vehicle_fitness_certificate_photo_name', 128);

            $table->string('vehicle_lease_agreement_photo_path', 256);
            $table->string('vehicle_lease_agreement_photo_name', 128);

            $table->string('vehicle_photo_1_path', 256);
            $table->string('vehicle_photo_1_name', 128);

            $table->string('vehicle_photo_2_path', 256);
            $table->string('vehicle_photo_2_name', 128);

            $table->string('vehicle_photo_3_path', 256);
            $table->string('vehicle_photo_3_name', 128);

            $table->string('vehicle_photo_4_path', 256);
            $table->string('vehicle_photo_4_name', 128);

            $table->string('vehicle_commercial_driving_license_photo_path', 256);
            $table->string('vehicle_commercial_driving_license_photo_name', 128);

            $table->string('vehicle_police_verification_certificate_photo_path', 256);
            $table->string('vehicle_police_verification_certificate_name', 128);

            $table->string('bank_passbook_photo_path', 256);
            $table->string('bank_passbook_photo_name', 128);

            $table->string('aadhaar_card_photo_path', 256);
            $table->string('aadhaar_card_photo_name', 128);


            $table->tinyInteger('is_approved')->default(0);

            $table->string('timezone', 50)->default();

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
