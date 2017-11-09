<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class FunctionCalcDistance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 3951 earth radius for Miles
        // 6371 earth radius for KiloMeters
        $sql = <<<SQL
CREATE FUNCTION `geo_distance` (lat1 FLOAT, lng1 FLOAT, lat2 FLOAT, lng2 FLOAT)
RETURNS FLOAT
DETERMINISTIC
BEGIN
    RETURN DEGREES(acos(sin(RADIANS(lat1)) * sin(RADIANS(lat2)) +  cos(RADIANS(lat1)) * cos(RADIANS(lat2)) * cos(RADIANS(lng1 - lng2)))) * 60 * 1.1515;
END
SQL;
        DB::unprepared($sql);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = "drop function if exists geo_distance";
        DB::unprepared($sql);
    }
}
