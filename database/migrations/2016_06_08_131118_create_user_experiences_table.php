<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_experiences', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id');
            $table->integer('education_id');
            //$table->integer('current_job_type_id');
            $table->integer('current_salary_range_id');
            $table->integer('experinece_id');
            $table->integer('experinece_level_id');
            $table->integer('desired_job_title_id');
            $table->integer('desired_salary_range_id');

            $table->string('recent_job_title');

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
        Schema::drop('user_experiences');
    }
}
