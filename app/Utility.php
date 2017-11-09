<?php

namespace App;

class Utility {

	public static function hash_hmac($data, $key = "admin@provalue.dev", $algo = "sha1") {
		return hash_hmac($algo, $data, $key);
	}

	public static function generate_alpha_numeric_token() {
		return rand(10000,99999);
	}

	public static function startsWith($haystack, $needle) {
	    // search backwards starting from haystack length characters from the end
	    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}

	public static function endsWith($haystack, $needle) {
	    // search forward starting from end minus needle length characters
	    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
	}
	
	public static function parseCarbonDate($date, $date_format=null) {
		try {
			if($date_format) {
				return \Carbon\Carbon::createFromFormat($date_format, $date);
			} else {
				return \Carbon\Carbon::parse($date);
			}
		} catch(\Exception $e) {}
		return false;
	}

	public static function diffInDates($_date1, $_date2, $format="Y-m-d", $calcFunc = "diffInDays") {
		try {
			$date1 = \Carbon\Carbon::createFromFormat($format, $_date1);
			$date2 = \Carbon\Carbon::createFromFormat($format, $_date2);
			return $date1->{$calcFunc}($date2, false);
		} catch(\Exception $e) {

		}
		return false;
	}
	
    public static function sortDates(&$dateArray, $format="Y-m-d") {
    	try {
    		$carbonDates = [];
    		foreach($dateArray as $date) {
    			if($carbonDate = self::parseCarbonDate($date,$format)) {
    				$carbonDates[] = $carbonDate;
    			}
    		}
    		for($i=0;$i<count($carbonDates);$i++) {
    			for($j=0;$j<count($carbonDates);$j++) {
    				if($carbonDates[$i] < $carbonDates[$j]) {
    					$backup = $carbonDates[$i];
    					$carbonDates[$i] = $carbonDates[$j];
    					$carbonDates[$j] = $backup;
    				}
    			}
    		}
			for($i=0;$i<count($carbonDates);$i++) {
				$dateArray[$i] = $carbonDates[$i]->format($format);
			}
    	} catch(\Exception $e) {}
    }

    public static function date_greaterThanToday($date, $format="Y-m-d",$equalToToday=false) {
		$today = \Carbon\Carbon::createFromFormat($format, \Carbon\Carbon::now()->format($format));
		if(!is_string($date)) {
			$today->hour = $date->hour = 0;
			$today->minute = $date->minute = 0;
			$today->second = $date->second = 0;
		}
		//$date = \Carbon\Carbon::createFromFormat($format, $date);
		//echo "TD:".$today."<br/>D:".$date;exit;
		$res = false;
		if($equalToToday) {
			$res = $today<=$date;
		} else {
	        $res = $today<$date;
    	}
        return $res;
    }

	public static function validateDatesInArray($date_array,$format="Y-m-d",$callback=null, $jobDays = null) {
		foreach($date_array as $date) {
			try {
				$_date = \Carbon\Carbon::createFromFormat($format, $date);
				if($_date->format($format) == $date) {
					if($callback) {
						if(self::{$callback}($_date, $format)) {

						} else {
							return false;
						}
					}
				} else {
					return false;
				}
			} catch(\Exception $e) {
				return false;
			}
		}
		return true;
	}

	public static function geo_distance($lat1, $lon1, $lat2, $lon2, $unit="") {
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);

		if ($unit == "K") {
		  return ($miles * 1.609344);
		} else if ($unit == "N") {
		  return ($miles * 0.8684);
		} else {
		  return $miles;
		}
	}

	public static function alpha_numeric_space($value){
		return preg_match("/^[A-Za-z0-9+\- ]+$/u", $value);
	}

	public static function alpha_numeric_other($value){
		return preg_match("/^[A-Za-z0-9'+\-&@\/,. ]+$/u", $value);
	}

	public static function email_validation($value){
		return preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#', $value);
	}

	public static function mobile_validation($value){
		return preg_match('#^[0-9]{10}$#', $value);
	}

	public static function postalCode_validation($value){
		return preg_match('/^[A-Za-z0-9 ]+$/u', $value);
	}

	public static function country_state_city_validation($value){
		return preg_match('/^[A-Za-z0-9+#(). ]+$/u', $value);
	}	
	
	public static function industry_validation($value){
		return preg_match('/^[A-Za-z0-9-+\&\/ ]+$/u', $value);
	}

	public static function education_validation($value){
		return preg_match('/^[A-Za-z0-9\/ ]+$/u', $value);
	}

	public static function degree_validation($value){
		return preg_match('/^[A-Za-z0-9-. ]+$/u', $value);
	}

	public static function tag_validation($value){
		return preg_match('/^[A-Za-z0-9-+#. ]+$/u', $value);
	}
}