<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_alerts', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id');
            $table->integer('job_categories_id');
            $table->integer('job_title_id');
            $table->string('keywords');
            $table->integer('radius');
            $table->integer('city_id');
            $table->integer('salary_type_id');
            $table->double('salary_range_from');
            $table->integer('job_type_id');
            $table->integer('industries_id');

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
        Schema::drop('job_alerts');
    }
}
