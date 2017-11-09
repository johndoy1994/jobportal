<?php

namespace App\Helpers;

class Time {
	
	public static $default_tz = 'Asia/Kolkata';

	public static function now() {
		return \Carbon\Carbon::now(self::$default_tz);
	}

}

