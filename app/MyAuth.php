<?php

namespace App;

use Illuminate\Support\Facades\Auth;


class MyAuth {

	const GUARD_EMPLOYER = "recruiter";
	const GUARD_SEEKER = "web";
	const GUARD_ADMIN = "admin";

	public static function guest($guard="web") {
		return Auth::guard($guard)->guest();
	}

	public static function check($guard="web") {
		return Auth::guard($guard)->check();
	}

	public static function attempt($credentials, $guard="web") { 
		return Auth::guard($guard)->attempt($credentials);
	}

	public static function user($guard="web") {
		return Auth::guard($guard)->user();
	}

	public static function logout($guard="web") {
		return Auth::guard($guard)->logout();
	}

	public static function loginUsingId($id, $guard="web") {
		return Auth::guard($guard)->loginUsingId($id);
	}

	// specific 

	public static function recruiterCheck() {
		return self::check(MyAuth::GUARD_EMPLOYER);
	}	

	public static function recruiter() {
		return self::user(MyAuth::GUARD_EMPLOYER);
	}

	public static function jobseekerCheck() {
		return self::check(MyAuth::GUARD_SEEKER);
	}

	public static function jobseeker() {
		return self::user(MyAuth::GUARD_SEEKER);
	}

	public static function adminCheck() {
		return self::check(MyAuth::GUARD_ADMIN);
	}

	public static function admin() {
		return self::user(MyAuth::GUARD_ADMIN);
	}

	///////////////////

	public static function getUser($number) {
		switch ($number) {
			case 1: return self::jobseeker();
			case 2: return self::recruiter();
			case 3: return self::admin();
		}
		return null;
	}

}
