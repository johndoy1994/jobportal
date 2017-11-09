<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_addresses', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('job_id');
            $table->integer('city_id');
            $table->string('street');
            $table->string('postal_code');

            $table->double('longitude');
            $table->double('latitude');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('job_addresses');
    }
}
