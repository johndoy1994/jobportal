<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstantMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instant_matches', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id');
            $table->integer('status');
            $table->integer('email_frequency');
            $table->integer('push_frequency');
            $table->datetime('pause');

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
        Schema::drop('instant_matches');
    }
}
