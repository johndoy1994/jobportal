<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetaToJobApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("job_applications", function($table) {
            $table->text("meta");
        });
        Schema::table("jobs", function($table) {
            $table->text("meta");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("job_applications", function($table) {
            $table->dropColumn("meta");
        });
        Schema::table("jobs", function($table) {
            $table->dropColumn("meta");
        });
    }
}
