<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColomnCountryStateCity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function($table) {
            $table->integer('status')->default(0);
        });

        Schema::table('cities', function($table) {
            $table->integer('status')->default(0);
        });

        Schema::table('states', function($table) {
            $table->integer('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('countries', function($table) {
            $table->dropColumn('status');
        });

        Schema::table('cities', function($table) {
            $table->dropColumn('status');
        });

        Schema::table('states', function($table) {
            $table->dropColumn('status');
        });
    }
}
