<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('employer_id');

            $table->string('title');
            $table->integer('vacancies');
            // job_addresses - done
            $table->integer('job_title_id');
            // job_skills - done
            $table->integer('education_id');
            $table->integer('experience_id');
            // job_certificates - done
            $table->integer('job_type_id');
            $table->integer('experience_level_id');
            $table->dateTime('starting_date')->nullable();
            $table->dateTime('ending_date')->nullable();
            // job_weekdays - done
            $table->time('work_schedule_from');
            $table->time('work_schedule_to');
            $table->integer('salary_type_id');
            $table->double('salary');
            $table->integer('pay_by_id');
            $table->integer('pay_period_id');
            $table->string('benefits');
            $table->text('description');
            $table->dateTime('expiration_date')->nullable();

            $table->string('status');
            // job_keywords - done


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
        Schema::drop('jobs');
    }
}
