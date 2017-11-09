<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMilesToUserAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_addresses', function($table) {
            $table->double('miles')->default(0)->after('type');
            $table->double('longitude')->default(0)->after('miles');
            $table->double('latitude')->default(0)->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_addresses', function($table) {
            $table->dropColumn('miles');
            $table->dropColumn('longitude');
            $table->dropColumn('latitude');
        });
    }
}
