<?php

namespace App\Repos\API;

use App\Helpers\MailHelper;
use App\Helpers\Notifier;
use App\Models\City;
use App\Models\CmsPage;
use App\Models\Country;
use App\Models\Degree;
use App\Models\Education;
use App\Models\Employer;
use App\Models\Experience;
use App\Models\ExperienceLevel;
use App\Models\Industry;
use App\Models\InstantMatch;
use App\Models\Job;
use App\Models\JobAlert;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobCertificate;
use App\Models\JobKeyword;
use App\Models\JobTitle;
use App\Models\JobType;
use App\Models\JobWeekday;
use App\Models\PasswordReset;
use App\Models\PayBy;
use App\Models\PayPeriod;
use App\Models\PersonTitle;
use App\Models\PublicSearch;
use App\Models\ReadedJob;
use App\Models\RecruiterType;
use App\Models\SalaryRange;
use App\Models\SalaryType;
use App\Models\SavedJob;
use App\Models\SearchDayAgo;
use App\Models\SearchMile;
use App\Models\State;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserCertificate;
use App\Models\UserContacts;
use App\Models\UserExperience;
use App\Models\UserJobType;
use App\Models\UserSkill;
use App\MyAuth;
use App\Repos\ResumeRepo;
use App\Repos\UserRepo;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
class PublicRepo {

	///////////////////////////////
	/// Adding/Updating ///////////
	///////////////////////////////

	public static function updatePublicSearch($request, $results) {


			$keywords = $request->has("keywords") ? $request->keywords : "";
			$location = $request->has("location") ? $request->location : "";

			if(strlen($keywords) == 0 && strlen($location) == 0) {
				return false;
			}

			$term = [
				"keywords" => $request->has("keywords") ? $request->keywords : "",
				"location" => $request->has("location") ? $request->location : ""
			];

			$jsonString = json_encode($term);

			$search = PublicSearch::where('term', $jsonString)->first();
			if($search === null) {
				$search = PublicSearch::create(["term"=>$jsonString]);
			}
			$search->results = $results;
			return $search->update();


	}

	public static function setNewPassword($code, $new_password) {
		$passwordReset = self::passwordResetOf($code);
		if($passwordReset) {
			$user_id = $passwordReset->user_id;
			$user = User::find($user_id);
			if($user) {
				$user->password = bcrypt($new_password);
				if($user->save()) {
					$passwordReset->delete();
					return [true, "Password reset successfully, now you can login with your new password"];
				} else {
					return [false, "There was an error while setting new password, try again."];
				}
			} else {
				return [false, "User not found, please try again."];
			}
		}
		return [false, "Code verification failed, please try again."];
	}

	public static function addSalaryRange($salary_type_id, $from, $to) {
		$salaryRange = new SalaryRange();
		$salaryRange->salary_type_id = $salary_type_id;
		$salaryRange->range_from = $from;
		$salaryRange->range_to = $to;
		if($salaryRange->save()) {
			return [true , "Saved", $salaryRange];
		} else {
			return [false, "Unable to save, try again.", null];
		}
	}

	public static function updateInstantMatch($user, $fields) {
		if($user) {
			$im = $user->instant_match;

			if(!$im) {
				$im = new InstantMatch();
				$user->instant_match()->save($im);
			}

			foreach($fields as $k=>$v) {
				$value = $v;
				if($k == "pause") {
					$newValue = $value;
					switch($value) {
						case 0:
							$newValue = \Carbon\Carbon::now()->addDays(-1);
						break;
						case 1: 
							$newValue = \Carbon\Carbon::now()->addMinutes(60);
						break;
						case 2: 
							$newValue = \Carbon\Carbon::now()->addMinutes(60*4);
						break;
						case 3: 
							$newValue = \Carbon\Carbon::now()->addDays(1);
						break;
						case 4: 
							$newValue = \Carbon\Carbon::now()->addDays(2);
						break;
						case 5: 
							$newValue = \Carbon\Carbon::now()->addWeeks(1);
						break;
						case 6: 
							$newValue = \Carbon\Carbon::now()->addWeeks(2);
						break;
					}
					$value = $newValue;
				}

				$im->{$k} = $value;
			}

			if($im->update()) {
				return [true, "Saved"];
			} else {
				return [false, "Not saved"];
			}
		}

		return [false, "User not found"];
	}

	public static function generateResetPasswordToken($user, $notification_type) {

		$passwordReset = new PasswordReset();
		$passwordReset->user_id = $user->id;
		$passwordReset->token = Utility::generate_alpha_numeric_token();
		$passwordReset->code = Utility::hash_hmac($passwordReset->token);
		if($passwordReset->save()){
			Notifier::resetPasswordTokenGenerated($user, $passwordReset, $notification_type);
			return true;
		} else {
			return false;
		}
	}

	///////////////////////////////
	/// Process ///////////////////
	///////////////////////////////

	public static function getJobApplications($employer, $perPage=10) {
		$q = JobApplication::select(
			"job_applications.*"
		);

		$q->join("jobs", function($join) use($employer) {
			$join->on('jobs.id','=','job_applications.job_id');
			$join->where('jobs.employer_id', '=', $employer->id);
		});

		$q->where('job_applications.status','=','in-process');

		return $q->paginate($perPage);
	}

	public static function processResetPasswordRequest(Request $request) {
		$json = [false, "Invalid request, try again."];

		if($request->has("email_address") || $request->has("mobile_number")) {
			$user = null;
			$notification_type = "";
			if($request->has("email_address")) {
				$user = PublicRepo::findUser($request->email_address);
				$notification_type = "mail";
			} else if($request->has("mobile_number")) {
				$user = PublicRepo::findUser("NOEMAIL", $request->mobile_number);
				$notification_type = "sms";
			} else {
				$json[1] = "There was an error while resetting your password, please try again.";
			}

			if($user) {	
				if(PublicRepo::generateResetPasswordToken($user, $notification_type)) {
					$json[0] = true;
					if($notification_type == "mail") {
						$json[1] = "In a very short time, you will receive mail from us with link to reset your password, simply follow the link to reset your password.";
					} else if($notification_type == "sms") {
						$json[1] = "In a very short time, you will receive PIN to reset your password.";
					} else {
						$json[1] = "In a very short time, you will receive link to reset your password";
					}
				} else {
					$json[1] = "Sorry, failed to send you an reset password link, please try again.";
				}
			} else {
				$json[1] = "User not found, please try again.";
			}
		}

		return $json;
	}

	public static function findUser($email_address, $mobile_number = null) {
		if(isset($mobile_number)) {
			if(!is_numeric($mobile_number) || $mobile_number <= 0) {
				return null;
			}
		}
		return User::where("email_address", $email_address)->orWhere("mobile_number", $mobile_number)->first();
	}

	public static function addReadedJob($job_id, $user_id) {
		if(self::getReadedJob($job_id, $user_id)) {
			
		} else {
			ReadedJob::create(['job_id'=>$job_id, 'user_id'=>$user_id]);
		}
	}

	public static function getReadedJob($job_id, $user_id) {
		return ReadedJob::where('job_id', $job_id)->where('user_id', $user_id)->first();
	}

	public static function searchJobAlerts($job_categories_id, $job_title_id, $keywords, $radius, $city_id, $salary_type_id, $salary_range_from, $job_type_id, $industries_id, $posted_title, $jobPoint) {
		
		Log::info('searchJobAlerts:', func_get_args());

		//$q = JobAlert::select(DB::raw("distinct job_alerts.user_id"));
		$q = JobAlert::select("job_alerts.*","users.jobAlerted","users.email_address","users.mobile_number","users.name");

		$q->join('users', 'job_alerts.user_id','=','users.id');
		$q->whereNull('users.deleted_at');
		// job categories
		$q->where(function($q) use($job_categories_id) {
			$q->orWhere('job_alerts.job_categories_id', 0);
			$q->orWhere(function($q) use($job_categories_id) {
				$q->where('job_alerts.job_categories_id', '>', 0);
				$q->where('job_alerts.job_categories_id', $job_categories_id);
			});
		});

		// job title
		$q->where(function($q) use($job_title_id) {
			$q->orWhere('job_alerts.job_title_id', 0);
			$q->orWhere(function($q) use($job_title_id) {
				$q->where('job_alerts.job_title_id', '>', 0);
				$q->where('job_alerts.job_title_id', $job_title_id);
			});
		});

		// city
		$q->where(function($q) use($city_id) {
			$q->orWhere('job_alerts.city_id', 0);
			$q->orWhere(function($q) use($city_id) {
				$q->where('job_alerts.city_id', '>', 0);
				$q->where('job_alerts.city_id', $city_id);
			});
		});		

		// salary type
		$q->where(function($q) use($salary_type_id) {
			$q->orWhere('job_alerts.salary_type_id', 0);
			$q->orWhere(function($q) use($salary_type_id) {
				$q->where('job_alerts.salary_type_id', '>', 0);
				$q->where('job_alerts.salary_type_id', $salary_type_id);
			});
		});

		// salary range from
		$q->where(function($q) use($salary_range_from) {
			$q->orWhere('job_alerts.salary_range_from', 0);
			$q->orWhere(function($q) use($salary_range_from) {
				$q->where('job_alerts.salary_range_from', '>=', $salary_range_from);
			});
		});

		// job type
		$q->where(function($q) use($job_type_id) {
			$q->orWhere('job_alerts.job_type_id', 0);
			$q->orWhere(function($q) use($job_type_id) {
				$q->where('job_alerts.job_type_id', '>', 0);
				$q->where('job_alerts.job_type_id', $job_type_id);
			});
		});

		// industry
		$q->where(function($q) use($industries_id) {
			$q->orWhere('job_alerts.industries_id', 0);
			$q->orWhere(function($q) use($industries_id) {
				$q->where('job_alerts.industries_id', '>', 0);
				$q->where('job_alerts.industries_id', $industries_id);
			});
		});

		$jobAlerts = $q->get();
		$jobCategory = self::getJobCategory($job_categories_id);
		$jobTitle = self::getJobTitle($job_title_id);

		// keywords & radius
		foreach($jobAlerts as $index => $jobAlert) {
			//radius
			$jobAlertRadius = $jobAlert->radius;
			if($jobAlertRadius > 0) {
				if($jobAlert->city_id > 0 && $jobPoint[0] > 0 && $jobPoint[1] > 0) {
					$cityPoint = self::getCityPoint($jobAlert->city_id);
					if($cityPoint[0] > 0 && $cityPoint[1] > 0) {
						$distance = self::distanceBetweenCoord($jobPoint, $cityPoint);
						if($distance > $jobAlertRadius) {
							unset($jobAlerts[$index]);
							continue;
						}
					}
				}
			}

			// keywords
			$jobAlertKeyword = $jobAlert->keywords;
			if(!empty($jobAlertKeyword)) {
				$jobCategoryMatched = true;
				$jobTitleMatched = true;
				$postedTitleMatched = true;

				if($jobCategory) {
					if(stripos($jobCategory->getName(), $jobAlertKeyword)===false) {
						$jobCategoryMatched=false;
					}
				}
				if($jobTitle) {
					if(stripos($jobTitle->getTitle(), $jobAlertKeyword)===false) {
						$jobTitleMatched=false;
					}	
				}
				if(stripos($posted_title, $jobAlertKeyword)===false) {
					$postedTitleMatched = false;
				}

				if(!$jobCategoryMatched && !$jobTitleMatched && !$postedTitleMatched) {
					unset($jobAlerts[$index]);
				}

			}
		}

		return $jobAlerts;

	}

	public static function getCityPoint($city_id) {
		Log::info("getCityPoint called :", ["city_id"=>$city_id]);
		$city = self::getCity($city_id);
		if($city) {
			Log::info("getCityPoint city :", ["city"=>$city]);
			if($city->longitude == 0 && $city->latitude == 0) {
				$address = $city->fetchFullAddress();
				Log::info("getCityPoint city :", ["full_address"=>$address]);
				list($status, $point, $clearAddress) = self::getGeoLocationPoint($address);
				Log::info("getGeoLocationPoint returned :", ["status"=>$status, "point"=>$point, "clearAddress"=>$clearAddress]);
				if($status) {
					$city->latitude = $point[0];
					$city->longitude = $point[1];
					$city->update();
				}
				Log::info("getCityPoint called :", ["city_id"=>$city_id]);
				return $point;
			}
		}
		Log::info("getCityPoint failed :", ["city_id"=>$city_id]);
		return [0,0];
	}

	public static function getSubscribedJobs($user = null, $alertId = 0) {
		if(!isset($user) && MyAuth::check()) {
			$user = MyAuth::user();
		}

		if($user) {

			$jobAlerts = [];

			if($alertId > 0) {
				$jobAlerts = $user->job_alerts()->where('id', $alertId)->get();
			} else {
				$jobAlerts = $user->job_alerts;
			}

			/// This logic doens't have orderBy date as it has different partition in query : moving to have this feature in Logic #23842
			// $allJobs = [];
			// foreach($user->job_alerts as $jobAlert) {
			// 	list($jobs) = self::getJobsFromAlert($jobAlert);
			// 	foreach($jobs as $job) {
			// 		$allJobs[$job->id] = $job;
			// 	}
			// }
			// return $allJobs;

			/// Logic#23842
			$allJobsIds = [];
			foreach($jobAlerts as $jobAlert) {
				list($jobs) = self::getJobsFromAlert($jobAlert);
				foreach($jobs as $job) {
					$allJobsIds[$job->id] = $job->id;
				}
			}

			return Job::whereIn('id', $allJobsIds)->orderBy('renew_date','desc')->get();
			/// Logic#23842 End
		}

		return [];
	}

	public static function getJobsFromAlert($jobAlert) {
		if($jobAlert) {
			$jobCategoryId = $jobAlert->job_categories_id;
			$jobTitleId = $jobAlert->job_title_id;
			$keywords = $jobAlert->keywords;
			$radius = $jobAlert->radius;
			$location = $jobAlert->city ? $jobAlert->city->fetchFullAddress() : "";
			$salary_type_id = $jobAlert->salary_type_id;
			$salary_range_from = $jobAlert->salary_range_from;
			$job_type_id = $jobAlert->job_type_id;
			$industries_id = $jobAlert->industries_id;
			$filters = [];
			if($jobCategoryId > 0) {
				$filters["jobCategoryId"] = $jobCategoryId;
			}
			if($jobTitleId > 0) {
				$filters["jobTitleId"] = $jobTitleId;
			}

			return self::searchJobs($keywords, $location, $radius, $salary_type_id, $salary_range_from, 0, 0, $job_type_id, "", null, $filters);
		}
		return [];
	}

	public static function getStatesWithJobCount() {
		$q = Job::select(
			"states.*", 
			DB::raw("count(jobs.id) as jobCount"),
			DB::raw("group_concat(job_addresses.city_id SEPARATOR ',') as city_ids")
		);
		$q->join('job_addresses', 'job_addresses.job_id','=','jobs.id');
		$q->join('cities', 'cities.id', '=', 'job_addresses.city_id');
		$q->join('states','states.id','=','cities.state_id');
		$q->whereNull('cities.deleted_at');
		$q->whereNull('states.deleted_at');
		$q->notExpired();
		$q->notEnded();
		$q->activeOnly();
		$q->groupBy('states.id');
		$q->orderBy('jobCount');
		$q = State::join(DB::raw("({$q->toSql()}) as a"), 'states.id','=','a.id')->mergeBindings($q->getQuery());
		$q->where('a.jobCount','>',0);
		$q->orderBy('a.jobCount','desc');
		return $q->get();
	}

	public static function getCompaniesWithJobCount() {
		$q = Job::select(
			"employers.*", 
			DB::raw("count(jobs.id) as jobCount"),
			DB::raw("group_concat(jobs.id SEPARATOR ',') as job_ids")
		);
		$q->join('job_addresses', 'job_addresses.job_id','=','jobs.id');
		$q->join('employers','jobs.employer_id','=','employers.id');
		$q->whereNull('employers.deleted_at');
		$q->notExpired();
		$q->notEnded();
		$q->groupBy('employers.id');
		$q = Employer::join(DB::raw("({$q->toSql()}) as je"), 'employers.id','=','je.id')->mergeBindings($q->getQuery());
		$q->where('je.jobCount','>',0);
		$q->orderBy('je.jobCount','desc');
		return $q->get();
	}

	public static function jobCountOf($id, $kind) {
		switch ($kind) {
			case 'job_categories':
				
				$q=Job::join('job_titles', function($join) {
					$join->on('job_titles.id','=','jobs.job_title_id');
					$join->whereNull('job_titles.deleted_at');
				})
				->join('job_addresses','jobs.id','=','job_addresses.job_id')
				->join('job_categories', function($join) use($id) {
					$join->on('job_categories.id','=','job_titles.job_category_id');
					$join->where('job_categories.id','=', $id);
					$join->whereNull('job_categories.deleted_at');
				})
				->notExpired()
				->notEnded();
				// ->where(function($query) {
				// 	$currentDate = \Carbon\Carbon::now();
				// 	$query->whereNull('jobs.expiration_date');
				// 	$query->orWhere(function($query) use($currentDate) {
				// 		$query->whereNotNull('jobs.expiration_date');
				// 		$query->whereDate('jobs.expiration_date', '>=', $currentDate);
				// 	});
				// });

				return $q->count();

				break;
			
			default:
				# code...
				break;
		}
		return 0;
	}

	public static function findJobAlert($user, $fields) {
		$q = JobAlert::where('user_id', $user->id);
		foreach ($fields as $col => $value) {
			$q->where($col, '=', $value);
		}
		return $q->first();
	}

	public static function getMatchCount($user, $job) {
		$basicTotal = 7;
		$basicCount = 0;
//echo '<pre>';print_r($user);
//echo '<pre>';print_r($job);
		if($job) {

		} else {
			return [7,0,0,0];
		}
		
		$job_title_matched = $job->fieldMatch("job_title_id", null, $user);

		//sagar comment///
		// if(!$job_title_matched) {
		// 	return [0,0,0,0];
		// }

		$basicCount += $job_title_matched ? 1 : 0;
		$basicCount += $job->fieldMatch("experience_level_id", null, $user) ? 1 : 0;
		$basicCount += $job->fieldMatch("experience_id", null, $user) ? 1 : 0;
		$basicCount += $job->fieldMatch("education_id", null, $user) ? 1 : 0;
		$basicCount += $job->fieldMatch("city_id", null, $user) ? 1 : 0;
		$basicCount += $job->fieldMatch("job_title_id", null, $user) ? 1 : 0;
		$basicCount += $job->fieldMatch("salary", null, $user) ? 1 : 0;
		foreach($job->certificates as $jobCertificate) {
			$basicTotal++;
			$basicCount += $job->fieldMatch("certificate", $jobCertificate->certificate, $user) ? 1 : 0;
		}

		$skillTotal = $job->skills()->count();
		$skillCount = 0;
		foreach($job->skills as $jobSkill) {
			$skillCount += $job->fieldMatch("tag", $jobSkill->tag_id, $user) ? 1 : 0;
		}

		return [$basicTotal, $basicCount, $skillTotal, $skillCount];
	}

	public static function matchProfile($user, $field, $job, $data = null) {
		if($user) {
			if($job) {
				switch($field) {
					case "tag":
						if($user->skills()->where('tag_id', $data)->first()) {
							return [true, "Already have!!"];
						} else {
							if(self::addUserSkill($user, $data)) {
								return [true, "Skill added"];
							}
						}
					break;

					case "certificate":
						if($user->certificates()->where('certificate', $data)->first()) {
							return [true, "Already have!!"];
						} else {
							if(self::addCertificateToUser($user, $data)) {
								return [true, "Added"];
							}
						}
					break;

					case "experience_level_id":
						if($experience = $user->experiences()->first()) {
							$experience->experinece_level_id = $job->experience_level_id;
							if($experience->update()) {
								return [true, "Updated"];
							}
						} else {
							$userExp = new UserExperience();
							$userExp->experinece_level_id = $job->experience_level_id;
							if($user->experiences()->save($userExp)) {
								return [true, "Added"];
							}
						}
					break;

					case "experience_id":
						if($experience = $user->experiences()->first()) {
							$experience->experinece_id = $job->experience_id;
							if($experience->update()) {
								return [true, "Updated"];
							}
						} else {
							$userExp = new UserExperience();
							$userExp->experinece_id = $job->experience_id;
							if($user->experiences()->save($userExp)) {
								return [true, "Added"];
							}
						}
					break;

					case "education_id":
						if($experience = $user->experiences()->first()) {
							$experience->education_id = $job->education_id;
							if($experience->update()) {
								return [true, "Updated"];
							}
						} else {
							$userExp = new UserExperience();
							$userExp->education_id = $job->education_id;
							if($user->experiences()->save($userExp)) {
								return [true, "Added"];
							}
						}
					break;

					case "salary":

						/// Going for older logic - so commenting this logic : New Logic #85468

						// if($experience = $user->experiences()->first()) {
						// 	$experience->desired_salary_range_id = $job->salary_range_id;
						// 	if($experience->update()) {
						// 		return [true, "Updated"];
						// 	}
						// } else {
						// 	$userExp = new UserExperience();
						// 	$userExp->desired_salary_range_id = $job->salary_range_id;
						// 	if($user->experiences()->save($userExp)) {
						// 		[true, "Added"];
						// 	}
						// }

						/// Logic : #85468

						if($experience = $user->experiences()->first()) {
							$jobSalary = $job->salary;
							$jobSalaryType = $job->salary_type_id;
							$salaryRanges = self::salaryRangeOf($jobSalaryType);
							foreach($salaryRanges as $salaryRange) {
								if($salaryRange->range_from<=$jobSalary && $salaryRange->range_to>=$jobSalary) {
									$experience->desired_salary_range_id = $salaryRange->id;
									if($experience->update()) {
										return [true , "Updated"];
									}
									break;
								}
							}
							$newRangeRatio = $jobSalary * 10 / 100;
							$newRangeFrom = $jobSalary - $newRangeRatio;
							$newRangeTo = $jobSalary + $newRangeRatio;
							list($rangeAdded, $msg, $newRange) = self::addSalaryRange($jobSalaryType, $newRangeFrom, $newRangeTo);
							if($rangeAdded) {
								$experience->desired_salary_range_id = $newRange->id;
								if($experience->update()) {
									return [true , "Added"];
								}
							}
							return [false, "Salary range not found, please try again."];
						} else {
							$userExp = new UserExperience();
							$jobSalary = $job->salary;
							$jobSalaryType = $job->salary_type_id;
							$salaryRanges = self::salaryRangeOf($jobSalaryType);
							foreach($salaryRanges as $salaryRange) {
								if($salaryRange->range_from<=$jobSalary && $salaryRange->range_to>=$jobSalary) {
									$userExp->desired_salary_range_id = $salaryRange->id;
									if($user->experiences()->save($userExp)) {
										return [true , "Updated"];
									}
									break;
								}
							}
							$newRangeRatio = $jobSalary * 10 / 100;
							$newRangeFrom = $jobSalary - $newRangeRatio;
							$newRangeTo = $jobSalary + $newRangeRatio;
							list($rangeAdded, $msg, $newRange) = self::addSalaryRange($jobSalaryType, $newRangeFrom, $newRangeTo);
							if($rangeAdded) {
								$experience->desired_salary_range_id = $newRange->id;
								if($user->experiences()->save($userExp)) {
									return [true , "Added"];
								}
							}
							return [false, "Salary range not found, please try again."];
						}

						/// Logic End
					break;

					case "city_id":
						list($success, $message) = UserRepo::addUserAddress($user, 'desired', [
							"city_id" => $job->getAddressField("city_id"),
							"street" => $job->getAddressField("street"),
							"postal_code" => $job->getAddressField("postal_code")
						]);
						if($success) {
							return [true, "Updated"];
						}
					break;

					case "job_title_id":
						if($experience = $user->experiences()->first()) {
							$experience->desired_job_title_id = $job->job_title_id;
							if($experience->update()) {
								return [true, "Updated"];
							}
						} else {
							$userExp = new UserExperience();
							$userExp->desired_job_title_id = $job->job_title_id;
							if($user->experiences()->save($userExp)) {
								return [true, "Added"];
							}
						}
					break;

					case "job_type_id":
						if($userJobType = $user->job_types()->where('job_type_id', $job->job_type_id)->first()) {
							return [true, "Already have!"];
						} else {
							$userJobType = new UserJobType();
							$userJobType->job_type_id = $job->job_type_id;
							if($user->job_types()->save($userJobType)) {
								return [true, "Added"];
							}
						}
					break;
				}
			} else {
				return [false, "Job not found"];
			}
		} else {
			return [false, "User not found"];
		}

		return [false, "There was an error while catching your profile, please try again."];
	}

	public static function jobFieldMatch($field, $job, $user, $data = null) {

		if($user) {

			switch($field) {

				case "tag":
					if($user->skills()->where('tag_id', $data)->first()) {
						return true;
					} else {
						return false;
					}
				break;

				case "certificate":
					if($user->certificates()->where('certificate', $data)->first()) {
						return true;
					} else {
						return false;
					}
				break;

				case "experience_level_id":
					if($experience = $user->experiences()->first()) {
						$expLevel=self::getExperienceLevel($job->experience_level_id);
						if($expLevel && ((strtolower(trim($expLevel->level))=="n/a") || trim($expLevel->level)=="N/A")){
							return true;
						}else{
							return $experience->experinece_level_id == $job->experience_level_id;
						}
					}else{
						$expLevel=self::getExperienceLevel($job->experience_level_id);
						if($expLevel && ((strtolower(trim($expLevel->level))=="n/a") || trim($expLevel->level)=="N/A")){
							return true;
						}
					}
				break;

				case "experience_id":
					if($experience = $user->experiences()->first()) {
						if($experience->experience && $job->experience) {
							$exp=self::getExperience($job->experience_id);
							if($exp && ((strtolower(trim($exp->exp_name))=="n/a") || trim($exp->exp_name)=="N/A")){
								return true;
							}else{
								return $job->experience->order <= $experience->experience->order;
							}
						}
						//return $experience->experinece_id == $job->experience_id;
					}else{
						$exp=self::getExperience($job->experience_id);
						if($exp && ((strtolower(trim($exp->exp_name))=="n/a") || trim($exp->exp_name)=="N/A")){
							return true;
						}
					}
				break;

				case "education_id":
					if($experience = $user->experiences()->first()) {
						if($experience->education && $job->education) {
							return $job->education->order <= $experience->education->order;
						}
						//return $experience->education_id == $job->education_id;
					}
				break;

				case "salary":
					if($experience = $user->experiences()->first()) {
						/// Logic#43573 removed to #437878
						// return $experience->desired_salary_range_id == $job->salary_range_id;
						/// Logic#43573 End

						/// Logic#437878
						if($experience->desired_salary_range) {
							$rangeFrom = $experience->desired_salary_range->range_from;
							$rangeTo = $experience->desired_salary_range->range_to;
							$salaryTypeId = $experience->desired_salary_range->salary_type_id;
							$jobSalary = $job->salary;
							// Job salary will be matched when It is between applicant's selected range or job salary is set to negotiable or to zero.
							return ($job->salary_type_id == $salaryTypeId && $jobSalary >= $rangeFrom && $jobSalary <= $rangeTo) || ($jobSalary <= 0);
						}
						/// Logic#437878 End
					}
				break;

				case "city_id":
					$jobPoint = $job->getJobCoordinates();
					$userAddresses = $user->addresses()->where('city_id', $job->getCityId())->get();
					foreach($userAddresses as $userAddress) {
						if($userAddress->miles == 0) {
							return true;
						} else {
							$distance = PublicRepo::distanceBetweenCoord($jobPoint, $userAddress->getCoordinates());
							if($distance <= $userAddress->miles) {
								return true;
							}
						}
					}
				break;

				case "job_title_id":
					if($experience = $user->experiences()->first()) {
						return $experience->desired_job_title_id == $job->{$field};
					}
				break;

				case "job_type_id":
					if($userJobType = $user->job_types()->where('job_type_id', $job->job_type_id)->first()) {
						return true;
					}
				break;
			}

		}

		return false;
	}

	public static function distanceBetweenCoord($point1, $point2, $unit="") {
		$lat1 = $point1[0];
		$lon1 = $point1[1];
		$lat2 = $point2[0]; 
		$lon2 = $point2[1];

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

	public static function uploadResume($user, $file) {
		$filename = uniqid(rand(10,100)).'.'.$file->getClientOriginalExtension();
		$uploaded=Storage::put('resumes/'.$filename, file_get_contents($file->getRealPath()));
		if($uploaded) {
			ResumeRepo::createOrUpdateResume($user->id, $filename);
			return [true, "Resume uploaded"];
		} else {
			return [false, "Not uploaded"];
		}
	}

	public static function findJobApplication($user, $job) {
		return $user->applications()->where('job_id', $job->id)->where('status', 'in-process')->first();
	}

	public static function createJobApplication($user, $job, $meta) {
		
		$jobApp = new JobApplication();
		$jobApp->job_id = $job->id;
		$jobApp->status = 'in-process';
		$jobApp->meta = json_encode($meta);

		if($user->applications()->save($jobApp)) {
			Notifier::applicationSubmitted($jobApp);
			return [true, "Successfully applied", $jobApp];
		} else {
			return [false, "Job application failed, try again.", null];
		}

	}

	public static function saveJobAlertContentType($alert_content_type, $user) {

		if($user && $user->profile) {
			$user->profile->alert_content_type = $alert_content_type;
			if($user->profile->update()) {
				return [true, "Alert content type updated."];
			} else {
				return [false, "Unable to update content type, try again."];
			}
		}

		return [false, "Failed to save content type."];
	}

	public static function updateJobAlertStatus($jobAlert, $action) {
		if(in_array($action, ['on','off','delete'])) {
			if($action == "on") {
				$jobAlert->restore();
				return [true, "Alert turned on!"];
			} elseif($action == "off") {
				$jobAlert->delete();
				return [true, "Alert turned off!"];
			} elseif($action == "delete") {
				$jobAlert->forceDelete();
				return [true, "Alert deleted!"];
			}
		}
		return [false, "Invalid request, try again"];
	}

	public static function createAlert($email_address, $fields) {
		$user = self::findOrCreateUser($email_address);

		$oldJobAlert = self::findJobAlert($user, $fields);

		if($oldJobAlert) {
			return [true, "You have already set alert on this kind of jobs!"];
		}

		$jobAlert = new JobAlert();
		$jobAlert->user_id = $user->id;
		foreach($fields as $key => $value) {
			if(!in_array($key, ['user_id','id'])) {
				$jobAlert->{$key} = $value;
			}
		}
		if($jobAlert->save()) {
			return [true, "Alert created successfully."];
		} else {
			return [false, "Failed to create alert, try again."];
		}
	}

	public static function checkUserEmailIsExits($email_address) {
		$user = User::where('email_address', $email_address)->first();
		if($user) {
			return $user;
		} else {
			return null;
		}
	}

	public static function findOrCreateUser($email_address, $name = "No Name") {
		$user = User::where('email_address', $email_address)->first();
		if($user) {
			return $user;
		} else {
			$tempPassword = uniqid();
			$user = new User();
			$user->name = $name;
			$user->email_address = $email_address;
			$user->password = bcrypt($tempPassword);
			$user->status = User::STATUS_ACTIVATED;
			$user->type = User::TYPE_SEEKER;
			$user->level = User::LEVEL_FRONTEND_USER;
			if($user->save()) {
				//MailHelper::sendThroughPassword($tempPassword, $user);
				Notifier::tempPasswordGenerated($tempPassword, $user);
				return $user;
			} else {
				return null;
			}
		}
	}

	public static function processSaveJob($job, $remove) {
		if(MyAuth::check()) { // User is ON
			$user = MyAuth::user();
			if($remove) {
				self::removeSavedJobUser($user, $job->id);
				return [true, "Removed"];
			} else {
				self::addSavedJobUser($user, $job->id);
				return [true, "Saved"];
			}
		} else { // No user, store to session
			if($remove) {
				self::removeSavedJobGuest($job);
				return [true, "Removed"];
			} else {
				self::addSavedJobGuest($job);
				return [true, "Saved"];
			}
		}
	}

	public static function isJobSaved($job_id) {
		if(MyAuth::check()) {
			if(MyAuth::user()->saved_jobs()->where('job_id', $job_id)->first()) {
				return true;
			}
		} else {
			if(session()->has('saved_jobs')) {
				$saved_jobs = session()->get('saved_jobs');
				return isset($saved_jobs[$job_id]);
			}
		}
		return false;
	}

	public static function addSavedJobUser($user, $job_id) {
		if($user->saved_jobs()->where('job_id', $job_id)->count() == 0) {
			$user->saved_jobs()->save(new SavedJob([
				'job_id' => $job_id
			]));
		}
	}

	public static function removeSavedJobUser($user, $job_id) {
		return $user->saved_jobs()->where('job_id', $job_id)->delete();
	}

	public static function addSavedJobGuest($job) {
		$saved_jobs = [];
		if(session()->has('saved_jobs')) {
			$saved_jobs = session()->get('saved_jobs');
		}
		$saved_jobs[$job->id] = true;
		session()->put('saved_jobs', $saved_jobs);
	}

	public static function removeSavedJobGuest($job) {
		$saved_jobs = [];
		if(session()->has('saved_jobs')) {
			$saved_jobs = session()->get('saved_jobs');
		}
		if(isset($saved_jobs[$job->id])) {
			unset($saved_jobs[$job->id]);
		}
		session()->put('saved_jobs', $saved_jobs);
	}

	public static function saveGuestSavedJobsIfAny() {
		if(MyAuth::check()) {
			if(session()->has('saved_jobs')) {
				$saved_jobs = session()->get('saved_jobs');
				foreach($saved_jobs as $job_id => $saved_job) {
					self::addSavedJobUser(MyAuth::user(), $job_id);
				}
				session()->put('saved_jobs', []);
			}
		}
	}

	///////////////////////////////
	/// Searches //////////////////
	///////////////////////////////

	public static function jobCountInCity($cityId) { 
		$q = Job::select("jobs.*");
		$q->join('job_addresses', 'job_addresses.job_id', '=', 'jobs.id');
		$q->notEnded();
		$q->notExpired();
		$q->activeOnly();
		$q->where('job_addresses.city_id', '=', $cityId);
		return $q->count('jobs.id');
	}

	public static function getGeoLocationPoint($address) {
		$point = [0, 0];
		Log::info("getGeoLocationPoint", func_get_args());
		
		if(trim($address) == "") {
			Log::info("getGeoLocationPoint", ["return becuase no address"]);
			return [false, [0,0], ""];
		}

		$uri = "http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false";
		$uri_content = file_get_contents($uri);
		Log::info("getGeoLocationPoint", ["uriContent"=>$uri_content]);
		$json = json_decode($uri_content, true);

		if(is_array($json) && isset($json["status"])) {
			if(strtoupper($json["status"]) == "OK") {
				$results = $json["results"];
				$firstResult = $results[0];
				$geometry = $firstResult["geometry"];
				$location = $geometry["location"];
				$point = [
					$location['lat'],
					$location['lng']
				];

				$clearAddress = isset($firstResult["formatted_address"]) ? $firstResult["formatted_address"] : $address;

				return [true, $point, $clearAddress];
			}
		}

		return [false, [0,0], $address];
	}

	public static function countSearchCvs($filters) {
		$q = self::searchCVs($filters, null);
		return $q->count('search_cvs.id');
	}

	public static function searchCVs($filters, $perPage = 10) {
		$keywords = isset($filters['keywords']) ? $filters['keywords'] : '';
		$location = isset($filters['location']) ? $filters['location'] : '';
		$viewMode = isset($filters['viewMode']) ? $filters['viewMode'] : '';
		$jobCategory = isset($filters['jobCategory']) ? $filters['jobCategory'] : 0;
		$jobType = isset($filters['jobType']) ? $filters['jobType'] : 0;
		$salaryType = isset($filters['salaryType']) ? $filters['salaryType'] : 0;
		$salaryRate = isset($filters['salaryRate']) ? $filters['salaryRate'] : 0;
		$salaryRateTo = isset($filters['salaryRateTo']) ? $filters['salaryRateTo'] : 0;
		$sortBy = isset($filters['sortBy']) ? $filters['sortBy'] : 'date';
		$advancedFilters = isset($filters['advancedFilters']) ? $filters['advancedFilters'] : [];
		
		list($valid_location, $location_point, $clearAddress) = self::getGeoLocationPoint($location);
		
		if(!$valid_location) {
			$location_point = [0,0];
		}
		
		$subResults = User::select(
			'users.*',
			DB::raw("job_titles.title as job_title, job_titles.id as job_title_id"),
			DB::raw("job_categories.name as job_category_name, job_categories.id as job_category_id"),
			DB::raw("salary_ranges.range_from, salary_ranges.range_to, salary_ranges.id as salary_range_id, salary_types.id as salary_type_id, salary_types.salary_type_name"),
			DB::raw("group_concat(job_types.id separator ',') as job_type_ids, group_concat(job_types.name SEPARATOR ',') as job_type_names"),
			DB::raw("group_concat(concat(IF(user_addresses.street='','',concat(user_addresses.street,', ')),'',concat(cities.name,', ',concat(states.name,', ',concat(countries.name,' - ',user_addresses.postal_code)))) SEPARATOR '|') as all_addresses"),
			DB::raw("user_profiles.profile_privacy, user_profiles.alert_content_type")
		);		

		$subResults->join('user_addresses','user_addresses.user_id','=','users.id');
		$subResults->join('cities', 'cities.id', '=', 'user_addresses.city_id');
		$subResults->join('states', 'states.id', '=', 'cities.state_id');
		$subResults->join('countries', 'countries.id', '=', 'states.country_id');

		$subResults->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id');

		$subResults->leftJoin('user_experiences','user_experiences.user_id','=','users.id');
			$subResults->leftJoin('job_titles','job_titles.id','=','user_experiences.desired_job_title_id');
				$subResults->leftJoin('job_categories','job_categories.id','=','job_titles.job_category_id');
			$subResults->leftJoin('salary_ranges','salary_ranges.id','=','user_experiences.desired_salary_range_id');
				$subResults->leftJoin('salary_types','salary_types.id','=','salary_ranges.salary_type_id');


		$subResults->leftJoin('user_job_types','users.id','=','user_job_types.user_id');
			$subResults->leftJoin('job_types','job_types.id','=','user_job_types.job_type_id');

		$subResults->whereNull('user_addresses.deleted_at');
		$subResults->whereNull('user_experiences.deleted_at');
		$subResults->whereNull('job_titles.deleted_at');
		$subResults->whereNull('job_categories.deleted_at');
		$subResults->whereNull('salary_ranges.deleted_at');
		$subResults->whereNull('salary_types.deleted_at');
		$subResults->whereNull('user_job_types.deleted_at');
		$subResults->whereNull('job_types.deleted_at');

		$subResults->where('users.level', '=', User::LEVEL_FRONTEND_USER);
		$subResults->where('users.type', '=', User::TYPE_SEEKER);

		if($jobCategory > 0) {
			$subResults->where('job_categories.id', '=', $jobCategory);
		}

		if($jobType > 0) {
			$subResults->where('job_types.id', '=', $jobType);
		}

		if($salaryType > 0) {
			$subResults->where('salary_types.id', '=', $salaryType);
			if($salaryRate > 0) {
				$subResults->where('salary_ranges.range_from', '>=', $salaryRate);
				if($salaryRateTo > 0) {
					$subResults->where('salary_ranges.range_to', '<=', $salaryRateTo);
				}
			}
		}

		if(strlen($keywords) > 0) {
			$subResults->where('users.name', 'like', '%'.$keywords.'%');
		}

		$subResults->where('user_profiles.profile_privacy', '<', 3);

		$subResults->groupBy('users.id');
		
		$results = User::join(DB::raw("({$subResults->toSql()}) as search_cvs"), "users.id",'=','search_cvs.id')->mergeBindings($subResults->getQuery());

		if($valid_location) {
			$words = explode(',', $location);
			foreach($words as $_word) {
				$word = trim($_word);
				$results->where('all_addresses','like','%'.$word.'%');
			}
		} else {
			$words = explode(',', $location);
			foreach($words as $_word) {
				$word = trim($_word);
				$results->orWhere('all_addresses','like','%'.$word.'%');
			}
		}

		switch ($sortBy) {
			case 'name':
				$results->orderBy('users.name', 'asc');
				break;
			
			default:
				
				break;
		}

		$rows = null;

		if(isset($perPage)) {
			if($perPage == 0) {
				$rows = $results->get();
			} else {
				if($viewMode=="map"){
					$perPage=self::countUsers();	
				}
				$rows = $results->paginate($perPage);
			}
		} else {
			return $results;
		}

		return [$rows, $location_point];

	}
	
	public static function countSearchJobs($get_params) {
		$keyword = isset($get_params["keywords"]) ? $get_params["keywords"] : "";
		$location = isset($get_params["location"]) ? $get_params["location"] : "";
		$radius = isset($get_params["radius"]) ? $get_params["radius"] : 0;
		$salaryType = isset($get_params["salaryType"]) ? $get_params["salaryType"] : 0;
		$salaryRate = isset($get_params["salaryRate"]) ? $get_params["salaryRate"] : 0;
		$daysAgo = isset($get_params["daysAgo"]) ? $get_params["daysAgo"] : 0;
		$recruiterType = isset($get_params["recruiterType"]) ? $get_params["recruiterType"] : 0;
		$jobType = isset($get_params["jobType"]) ? $get_params["jobType"] : 0;
		$jobCategory = isset($get_params["jobCategory"]) ? $get_params["jobCategory"] : 0;
		$sortBy = isset($get_params["sortBy"]) ? $get_params["sortBy"] : 0;
		$perPage = null;
		$filters = [
			'jobTitle' => isset($get_params["jobTitle"]) ? $get_params["jobTitle"] : "",
			'jobCategory' => isset($get_params["jobCategory"]) ? $get_params["jobCategory"] : "",
			'salaryRateTo' => isset($get_params["salaryRateTo"]) ? $get_params["salaryRateTo"] : 0,
			'radius_data'=>(isset($get_params["radius_data"]) && $get_params["radius_data"]) ? true : false
		];

		if(isset($get_params["onlyNegotiable"])) {
            $filters["onlyNegotiable"] = true;
        }
		$returnQuery = true;
		$q = self::searchJobs($keyword, $location, $radius, $salaryType, $salaryRate, $daysAgo, $recruiterType, $jobType, $sortBy, $perPage, $filters, $returnQuery,$jobCategory);
		return $q->count('search_jobs.id');
	}

	public static function searchJobs($keyword, $location, $radius, $salaryType, $salaryRate, $daysAgo, $recruiterType, $jobType, $sortBy, $perPage = 10, $filters = [], $returnQuery = false,$jobCategory=0) {

		//// Old Map API Call ///// <---
		// list($valid_location, $location_point, $clearAddress) = self::getGeoLocationPoint($location);
		// if(!$valid_location) {
		// 	$location_point = [0,0];
		// }
		////--> Old Map API Call /////
		//print_r($location);exit;
		$valid_location = false;
		$location_point = [0,0];
		$clearAddress = "";
		$cityId = 0;

		$locationData=explode(",", $location);
		$locationData=array_filter($locationData);

		if($locationData){
			if(isset($locationData[2])){
				$CountryData=Country::where('name','=',trim($locationData[2]))->first();
				if($CountryData){
					$StateData=State::where('name','=',trim($locationData[1]))->where('country_id','=',$CountryData->id)->first();
					if($StateData){
						$CityData=City::where('name','=',trim($locationData[0]))->where('state_id','=',$StateData->id)->first();
						if($CityData){
							$valid_location = true;
							$location_point = [$CityData->latitude,$CityData->longitude];	
						}
						
					}
				}
			}elseif(isset($locationData[1])){
				$StateData=State::where('name','=',trim($locationData[1]))->first();
				if($StateData){
					$CityData=City::where('name','=',trim($locationData[0]))->where('state_id','=',$StateData->id)->first();
					$valid_location = true;
					$location_point = [$CityData->latitude,$CityData->longitude];
				}
			}elseif(isset($locationData[0])){
				$CityData=City::where('name','=',trim($locationData[0]))->first();
				if($CityData){
					$valid_location = true;
					$location_point = [$CityData->latitude,$CityData->longitude];
				}
					
			}
		}

		if(isset($filters["cityId"])) {
			$cityId = $filters["cityId"];
		}

		if($cityId > 0) {
			$city = self::getCity($cityId);
			if($city) {
				$location_point = [$city->latitude, $city->longitude];
				$clearAddress = $city->fetchFullAddress();
			}
		}

		$subResults = Job::select(
			'jobs.*',
			DB::raw("IF(jobs.title LIKE ?,1,0) as search_order"),
			DB::raw("job_titles.title as job_title"),
			DB::raw("job_categories.name as job_category_name, job_categories.id as job_category_id"),
			DB::raw("group_concat(job_keywords.keyword SEPARATOR ',') as keywords"),
			DB::raw("job_addresses.street, job_addresses.postal_code,job_addresses.longitude,job_addresses.latitude"),
			DB::raw("cities.name as city_name, states.name as state_name, countries.name as country_name, concat(IF(job_addresses.street='','',concat(job_addresses.street,', ')),'',concat(cities.name,', ',concat(states.name,', ',concat(countries.name,' - ',job_addresses.postal_code)))) as full_address"),
			DB::raw('geo_distance(job_addresses.latitude, job_addresses.longitude, '.$location_point[0].', '.$location_point[1].') as distance'),
			DB::raw('abs(datediff(jobs.renew_date, "'.\Carbon\Carbon::now()->format('Y-m-d').'")) as posted_days_ago')
		);

		$subResults->addBinding('%'.$keyword.'%');

		if(isset($filters['jobId'])) {
			$subResults->where('jobs.id', $filters['jobId']);
		}

		if(isset($filters["jobTitleId"])) {
			$ids = explode(",",$filters["jobTitleId"]);
			$subResults->whereIn('jobs.job_title_id',$ids);
		}

		if(isset($filters["jobCategoryId"])) {
			$ids = explode(",", $filters["jobCategoryId"]);
			$subResults->whereIn('job_categories.id', $ids);
		}

		if(isset($filters["employerId"])) {
			$ids = explode(",", $filters["employerId"]);
			$subResults->whereIn('jobs.employer_id', $ids);
		}

		$subResults->notExpired();
		$subResults->notEnded();
		$subResults->activeOnly();

		// Filter for expiration date..
		// $currentDate = \Carbon\Carbon::now()->format('Y-m-d');
		// $subResults->where(function($query) use($currentDate) {
		// 	// where ( expiration_date is null or (expiration_date is not null && expiration_date <= DATE) )
		// 	$query->whereNull('expiration_date');
		// 	$query->orWhere(function($query) use($currentDate) {
		// 		$query->whereNotNull('expiration_date');
		// 		$query->whereDate('expiration_date', '>=', $currentDate);
		// 	});
		// });

		// // Filter for ending date
		// $subResults->where(function($query) use($currentDate) {
		// 	// where ( ending_date is null or (ending_date is not null && ending_date <= DATE) )
		// 	$query->whereNull('ending_date');
		// 	$query->orWhere(function($query) use($currentDate) {
		// 		$query->whereNotNull('ending_date');
		// 		$query->whereDate('ending_date', '>=', $currentDate);
		// 	});
		// });

		$subResults->join('job_titles', 'jobs.job_title_id', '=', 'job_titles.id');
		$subResults->join('job_categories', 'job_titles.job_category_id', '=', 'job_categories.id');
		$subResults->leftJoin('job_keywords', 'jobs.id', '=', 'job_keywords.job_id');
		$subResults->join('job_addresses', 'jobs.id', '=', 'job_addresses.job_id');
		$subResults->join('cities', function($join) {
			$join->on('cities.id','=','job_addresses.city_id');
			//$join->where('cities.status',"=", 0);
		});
		$subResults->where('cities.status','=',0);
		//echo '<pre>';print_r($subResults->getBindings());exit;
		$subResults->join('states', function($join) {
			$join->on('states.id','=','cities.state_id');
			//$join->where('states.status',"=", 0);
		});
		$subResults->where('states.status','=',0);
		$subResults->join('countries', function($join) {
			$join->on('countries.id','=','states.country_id');
			//$join->where('countries.status',"=", 0);
		});
		$subResults->where('countries.status','=',0);

		if(isset($filters['countryId'])) {
			$subResults->where('countries.id','=', $filters['countryId']);
		}

		if(isset($filters['stateId'])) {
			$subResults->where('states.id','=', $filters['stateId']);
		}
		
		//comment bye sagar 13/09/2016///
		// if(isset($filters['cityId'])) {
		// 	$subResults->where('cities.id','=', $filters['cityId']);
		// }

		///////////////////////////////
		/// New Search Logic //////////
		///////////////////////////////

		$presetSearchTerms = false;

		// Find jobcategories only..
		$jobCategories = JobCategory::select(
			"job_categories.id",
			DB::raw("count(jobs.id) as jobs_count")
		)
		->join('job_titles', "job_titles.job_category_id","=","job_categories.id")
		->leftJoin('jobs','job_titles.id','=','jobs.job_title_id')
		->where(function($q) use($keyword) {
			$q->orWhere('job_categories.name', 'like', '%'.$keyword.'%');
			$q->orWhere('job_categories.name', 'like', '%'.str_singular($keyword).'%');
		})
		->groupBy('job_categories.id')
		->get();

		if(count($jobCategories)>0) {
			$jobCategoriesIds = [];
			foreach ($jobCategories as $key => $value) {
				if($value->jobs_count > 0) {
					$jobCategoriesIds[] = $value->id;
				}
			}
			if(count($jobCategoriesIds)>0) {
				$presetSearchTerms = true;
				$subResults->whereIn("job_category_id", $jobCategoriesIds);
			}
		}

		// Find job titles only...
		if(!$presetSearchTerms) {
			$jobTitles = JobTitle::select(
				"job_titles.id",
				DB::raw("count(jobs.id) as jobs_count")
			)
			->join("jobs","jobs.job_title_id","=","job_titles.id")
			->where(function($q) use($keyword) {
				$q->orWhere('job_titles.title', 'like', '%'.$keyword.'%');
				$q->orWhere('job_titles.title', 'like', '%'.str_singular($keyword).'%');
			})
			->groupBy("job_titles.id")
			->get();
			if(count($jobTitles) > 0) {
				$jobTitlesIds = [];
				foreach ($jobTitles as $key => $value) {
					if($value->jobs_count > 0) {
						$jobTitlesIds[] = $value->id;
					}
				}
				if(count($jobTitlesIds)>0) {
					$presetSearchTerms = true;
					$subResults->whereIn("job_title_id", $jobTitlesIds);
				}
			}
		}

		/// Ended /////////////////////

		$subResults->groupBy('jobs.id');
		$subResults->orderBy('jobs.renew_date', 'desc');

		// echo '<pre>';
		// $subData = $subResults->get();
		// echo count($subData)."<br/>";
		// foreach($subData as $subD) {
		// 	echo $subD->job_category_id.",";
		// }
		// echo "<br/>";
		// print_r($subData);
		// exit;

		//$results = DB::table(DB::raw("({$subResults->toSql()}) as search_jobs"))->mergeBindings($subResults->getQuery());
		$results = Job::join(DB::raw("({$subResults->toSql()}) as search_jobs"), "jobs.id",'=','search_jobs.id')->mergeBindings($subResults->getQuery());

		$results->select(
			'search_jobs.*',
			DB::raw('employers.recruiter_type_id, employers.company_name, employers.description as employeer_description')
		);

		$results->join('employers', 'employers.id', '=', 'search_jobs.employer_id');

		$results->where(function($results) use($presetSearchTerms, $valid_location, $keyword, $radius, $salaryType, $salaryRate, $jobType, $recruiterType, $daysAgo, $filters,$jobCategory) {
			
			if(trim($keyword) != "" && !$presetSearchTerms) {
				$results->where(function($results) use($keyword) {
					$results->orWhere('search_jobs.title', 'like', '%'.$keyword.'%');
					$results->orWhere('search_jobs.keywords', 'like', '%'.$keyword.'%');
				});
			}

			
			// if(isset($filters["radius_data"])){
			// 	$filters["radius_data"]=$filters["radius_data"];
			// }else{
			// 	$filters["radius_data"]=false;
			// }

			// if($valid_location && !$filters["radius_data"] && $radius> -1)
			// {
			// 	$results->where('search_jobs.distance', '<=', $radius);
			// }

			if($valid_location && $radius > 0) 
			{
				$results->where('search_jobs.distance', '<=', $radius);
			}

			if(isset($filters["onlyNegotiable"])) {
				$results->where('search_jobs.salary','=',0);
			}

			if($salaryType > 0) {
				$results->where('search_jobs.salary_type_id', $salaryType);
				$results->where(function($q) use($salaryRate, $filters) {
					//$q->orWhere('search_jobs.salary','=',0);
					//$q->orWhere('search_jobs.salary','>=',$salaryRate); old one
					$q->orWhere(function($q) use($salaryRate, $filters) {
						$q->where('search_jobs.salary','>=',$salaryRate);
						if(isset($filters["salaryRateTo"])) {
							if($filters["salaryRateTo"] > 0) {
								$q->where('search_jobs.salary', '<=', $filters["salaryRateTo"]);
							}
						}
					});
				});
				//$results->where('search_jobs.salary', '>=', $salaryRate);
			}

			if($jobType > 0) {
				$results->where('search_jobs.job_type_id', $jobType);
			}

			if($jobCategory > 0) {
				$results->where('search_jobs.job_category_id', $jobCategory);
			}

			if($recruiterType > 0) {
				$results->where('employers.recruiter_type_id', $recruiterType);
			}

			if($daysAgo > 0) {
				$results->where('search_jobs.posted_days_ago', '<=', $daysAgo);
			}

		});

		$results->where(function($results) use($location, $valid_location, $radius) {
			if($valid_location && $radius > 0) {
				$words = explode(',', $location);
				foreach($words as $_word) {
					$word = trim($_word);
					$results->orWhere('full_address','like','%'.$word.'%');
				}
			} else {
				$words = explode(',', $location);
				foreach($words as $_word) {
					$word = trim($_word);
					$results->where('full_address','like','%'.$word.'%');
				}
			}
		});

		// echo "<blockquote>".$results->toSql()."</blockquote>";

		switch($sortBy) {
			case "date":
				$results->orderBy('search_jobs.renew_date','desc');
			break;

			case "salary-low-to-high":
				$results->orderBy('search_jobs.salary');
			break;

			case "salary-high-to-low":
				$results->orderBy('search_jobs.salary','desc');
			break;

			case "distance":
				$results->orderBy('search_jobs.distance');
			break;

			default:
				$results->orderBy('search_jobs.search_order',"desc");
				$results->orderBy('search_jobs.renew_date',"desc");
				break;
		}

		// DB::connection()->enableQueryLog();

		if($returnQuery) {
			return $results;
		} else {
			if(isset($perPage)) {
				$rows = $results->paginate($perPage);
			} else {
				$rows = $results->get();
			}
		}

		// $queries = DB::getQueryLog();

		// echo '<pre>';
		// print_r($queries);
		// print_r($rows);
		// echo '</pre>';exit;


		// $jobs = [];

		// foreach($rows as $row) {
		// 	$job = new Job();
		// 	foreach($row as $key=>$value) {
		// 		$job->{$key} = $value;
		// 	}
		// 	$jobs[] = $job;
		// }

		return [$rows, $location_point, $location];

	}

	public static function searchCountries($q, $limit = null, $status = null) {
		$q = Country::where('name','like','%'.$q.'%');
		if(isset($status)) {
			$q->where('status', $status);
		}
		if(isset($limit)) {
			return $q->paginate($limit);
		} else {
			return $q->get();
		}
	}
	public static function searchStates($countryId, $q, $limit = null, $status = null) {
		$query = State::where('country_id', '=',$countryId);
		if(isset($q)) {
			$query->where('name','like','%'.$q.'%');
		}
		if(isset($status)) {
			$query->where('status', $status);
		}
		if(!empty($limit)) {
			$limit=0;
			return $query->paginate($limit);
		} else {
			return $query->get();
		}
	}
	public static function searchCities($stateId, $q, $limit = null, $status = null) {
		$query = City::where('state_id', '=',$stateId);
		if(isset($q)) {
			$query->where('name','like','%'.$q.'%');
		}
		if(isset($status)) {
			$query->where('status', $status);
		}
		if(!empty($limit)) {
			$limit=0;
			return $query->paginate($limit);
		} else {
			return $query->get();
		}
	}
	public static function searchJobCategories($q , $limit = null) {
		$query = JobCategory::where('name','like','%'.$q.'%');
		if(isset($limit)) {
			return $query->paginate($limit);
		}
		return $query->get();
	}

	public static function searchLocations($q, $status=null, $cityId = null, $limit = 0) {
		$subQuery = City::select(
			'cities.*',
			'states.name as state_name',
			//'states.id as state_id',
			'countries.id as country_id',
			'countries.name as country_name',
			DB::raw("concat(cities.name,', ',concat(states.name,', ',concat(countries.name,''))) as full_address")
		)
		->join('states', 'states.id','=','cities.state_id')
		->join('countries', 'countries.id','=','states.country_id');
		

		if(isset($status)) {
			$subQuery->where(function($subQuery) use($status) {
				$subQuery->where('cities.status', $status);
				$subQuery->where('states.status', $status);
				$subQuery->where('countries.status', $status);
			});
		}

		$query = City::join(DB::raw("({$subQuery->toSql()}) as found_cities"), "cities.id",'=','found_cities.id')->mergeBindings($subQuery->getQuery());

		if(isset($q)) {
			$query->where(function($query) use($q) {
				$query->orWhere('found_cities.name','like','%'.$q.'%');
				$query->orWhere('found_cities.state_name','like','%'.$q.'%');
				$query->orWhere('found_cities.country_name','like','%'.$q.'%');
				$query->orWhere('found_cities.full_address', 'like', '%'.$q.'%');
			});
		}

		if(isset($cityId)) {
			$query->where('cities.id', '=', $cityId);
		}
		
		if($limit == 0) {
			return $query->get();
		} else {
			return $query->paginate($limit);

		}
	}

	public static function searchLocationsHome($q, $status=null, $cityId = null, $limit = 0) {
		$subQuery = City::select(
			'cities.*',
			'states.name as state_name',
			//'states.id as state_id',
			//'countries.id as country_id',
			'countries.name as country_name',
			'job_addresses.city_id as CityId',
			DB::raw("concat(cities.name,', ',concat(states.name,', ',concat(countries.name,''))) as full_address")
		)
		->join('states', 'states.id','=','cities.state_id')
		->join('countries', 'countries.id','=','states.country_id')
		->join('job_addresses', 'job_addresses.city_id','=','cities.id');

		if(isset($status)) {
			$subQuery->where(function($subQuery) use($status) {
				$subQuery->where('cities.status', $status);
				$subQuery->where('states.status', $status);
				$subQuery->where('countries.status', $status);
			});
		}

		$query = City::join(DB::raw("({$subQuery->toSql()}) as found_cities"), "cities.id",'=','found_cities.id')->mergeBindings($subQuery->getQuery());

		if(isset($q)) {
			$query->where(function($query) use($q) {
				$query->orWhere('found_cities.name','like','%'.$q.'%');
				$query->orWhere('found_cities.state_name','like','%'.$q.'%');
				$query->orWhere('found_cities.country_name','like','%'.$q.'%');
				$query->orWhere('found_cities.full_address', 'like', '%'.$q.'%');
			});
		}

		if(isset($cityId)) {
			$query->where('cities.id', '=', $cityId);
		}
		$query->groupBy('found_cities.full_address');
//		print_r($query->toSql());exit;
		if($limit == 0) {
			return $query->get();
		} else {
			return $query->paginate($limit);

		}
	}


	public static function searchExactLocations($q, $status=null, $cityId = null, $limit = 0) {
		$subQuery = City::select(
			'cities.*',
			'states.name as state_name',
			//'states.id as state_id',
			'countries.id as country_id',
			'countries.name as country_name',
			DB::raw("concat(cities.name,', ',concat(states.name,', ',concat(countries.name,''))) as full_address")
		)
		->join('states', 'states.id','=','cities.state_id')
		->join('countries', 'countries.id','=','states.country_id');
		//->join('job_addresses', 'job_addresses.city_id','=','cities.id');
		if(isset($status)) {
			$subQuery->where(function($subQuery) use($status) {
				$subQuery->where('cities.status', $status);
				$subQuery->where('states.status', $status);
				$subQuery->where('countries.status', $status);
			});
		}

		$query = City::join(DB::raw("({$subQuery->toSql()}) as found_cities"), "cities.id",'=','found_cities.id')->mergeBindings($subQuery->getQuery());

		if(isset($q)) {
			$query->where(function($query) use($q) {
				$query->orWhere('found_cities.name','=',$q);
				$query->orWhere('found_cities.state_name','=',$q);
				$query->orWhere('found_cities.country_name','=',$q);
				$query->orWhere('found_cities.full_address', '=',$q);
			});
		}

		if(isset($cityId)) {
			$query->where('cities.id', '=', $cityId);
		}
		
		if($limit == 0) {
			return $query->get();
		} else {
			return $query->paginate($limit);
		}
	}

	public static function searchSkills($q) {
		return Tag::where('name', 'like', '%'.$q.'%')->orderBy('name')->get();
	}

	public static function searchJobKeywords($q) {
		return JobKeyword::select('keyword', DB::raw('count(*) as jobCount'))->where('keyword', 'like', '%'.$q.'%')->orderBy('keyword')->groupBy('keyword')->get();
	}

	public static function getPostedJob($recruiterId){
		$currentDate = \Carbon\Carbon::now()->format("Y-m-d");
		$subResults = Job::select(
			'jobs.*',
			DB::raw("users.email_address,users.id as userId"),
			//DB::raw("jobs.salary,jobs.description,jobs.expiration_date,jobs.created_at,jobs.id as jobId,jobs.salary,jobs.title"),
			DB::raw('employers.user_id,employers.company_name'),
			DB::raw("job_titles.title as job_title"),
			DB::raw("job_types.name as jobType_name"),
			DB::raw("salary_types.salary_type_name"),
			DB::raw("job_addresses.street, job_addresses.postal_code,job_addresses.longitude,job_addresses.latitude"),
			DB::raw("cities.name as city_name, states.name as state_name, countries.name as country_name, concat(IF(job_addresses.street='','',concat(job_addresses.street,', ')),'',concat(cities.name,', ',concat(states.name,', ',concat(countries.name,' - ',job_addresses.postal_code)))) as full_address"),
			DB::raw('abs(datediff(jobs.renew_date, "'.\Carbon\Carbon::now()->format('Y-m-d').'")) as posted_days_ago'),
			DB::raw('datediff(jobs.expiration_date, "'.$currentDate.'") as expired_days')
			//DB::raw('count(job_applications.id) as application_count')
		);
		$subResults->join('employers', 'employers.id', '=', 'jobs.employer_id');
		$subResults->join('users', 'users.id', '=', 'employers.user_id');
		//$subResults->join('jobs', 'jobs.employer_id', '=', 'employers.id');
		$subResults->join('salary_types', 'salary_types.id', '=', 'jobs.salary_type_id');
		$subResults->join('job_types', 'job_types.id', '=', 'jobs.job_type_id');
		$subResults->join('job_titles', 'jobs.job_title_id', '=', 'job_titles.id');
		$subResults->join('job_addresses', 'jobs.id', '=', 'job_addresses.job_id');
		//$subResults->leftJoin("job_applications", "jobs.id", '=', 'job_applications.job_id');
		
		$subResults->join('cities', function($join) {
			$join->on('cities.id','=','job_addresses.city_id');
		});
		$subResults->where('cities.status','=',0);
		
		$subResults->join('states', function($join) {
			$join->on('states.id','=','cities.state_id');
		});
		$subResults->where('states.status','=',0);
		
		$subResults->join('countries', function($join) {
			$join->on('countries.id','=','states.country_id');
		});
		$subResults->where('countries.status','=',0);
		//$subResults->whereNull('jobs.deleted_at');
		
		$subResults->where('users.id','=',$recruiterId->id);
		$subResults->orderBy('jobs.renew_date', 'desc');
		return $subResults->paginate(15);

	}

	public static function getAllJobApplication($jobId,$default='application'){
		$subResults = JobApplication::select(
			'job_applications.*',
			DB::raw("users.name,users.email_address,users.id as userId,users.mobile_number"),
			DB::raw("jobs.title")

			///DB::raw("user_addresses.street, user_addresses.postal_code,user_addresses.longitude,user_addresses.latitude")
			//DB::raw("cities.name as city_name, states.name as state_name, countries.name as country_name, concat(IF(user_addresses.street='','',concat(user_addresses.street,', ')),'',concat(cities.name,', ',concat(states.name,', ',concat(countries.name,' - ',user_addresses.postal_code)))) as full_address")		
		);
		$subResults->join('users', 'users.id', '=', 'job_applications.user_id');
		$subResults->join('jobs', 'jobs.id', '=', 'job_applications.job_id');
		//$subResults->join('user_addresses', 'user_addresses.user_id', '=', 'job_applications.user_id');
		
		// $subResults->join('cities', function($join) {
		// 	$join->on('cities.id','=','user_addresses.city_id');
		// });
		// $subResults->where('cities.status','=',0);
		
		// $subResults->join('states', function($join) {
		// 	$join->on('states.id','=','cities.state_id');
		// });
		// $subResults->where('states.status','=',0);
		
		// $subResults->join('countries', function($join) {
		// 	$join->on('countries.id','=','states.country_id');
		// });
		// $subResults->where('countries.status','=',0);
		

		$subResults->where('job_applications.job_id','=',$jobId);
		if($default=='candidate'){
			$subResults->where('job_applications.status','=',"accepted");
		}else{
			$subResults->where('job_applications.status','=',"in-process");
		}

		return $subResults->get();
	}

	Public static function getPostedJobDetails($job,$user_id=0){
		$subResults = Job::select(
			'jobs.*',
			DB::raw('employers.user_id,employers.company_name'),
			DB::raw("job_types.name as jobType_name,job_types.day_selection"),
			DB::raw("job_addresses.street, job_addresses.postal_code,job_addresses.longitude,job_addresses.latitude"),
			DB::raw("cities.name as city_name, states.name as state_name, countries.name as country_name, concat(IF(job_addresses.street='','',concat(job_addresses.street,', ')),'',concat(cities.name,', ',concat(states.name,', ',concat(countries.name,' - ',job_addresses.postal_code)))) as full_address"),
			DB::raw('abs(datediff(jobs.renew_date, "'.\Carbon\Carbon::now()->format('Y-m-d').'")) as posted_days_ago')
		);
		$subResults->join('employers', 'employers.id', '=', 'jobs.employer_id');
		$subResults->join('job_types', 'job_types.id', '=', 'jobs.job_type_id');
		$subResults->join('job_addresses', 'jobs.id', '=', 'job_addresses.job_id');
		$subResults->join('cities', function($join) {
			$join->on('cities.id','=','job_addresses.city_id');
		});
		$subResults->where('cities.status','=',0);
		
		$subResults->join('states', function($join) {
			$join->on('states.id','=','cities.state_id');
		});
		$subResults->where('states.status','=',0);
		
		$subResults->join('countries', function($join) {
			$join->on('countries.id','=','states.country_id');
		});
		$subResults->where('countries.status','=',0);
		
		$subResults->where('jobs.id','=',$job->id);
		if($user_id!=0){
			$subResults->where('employers.user_id','=',$user_id);
		}

		return $subResults->first();
	}

	public static function getApplicantApply($user_id){
		return JobApplication::where('user_id', $user_id)->first();
	}

	public static function CheckValidUserToSeeCandidateDetails($applicantId,$jobId,$loginUserId){
		$subResults = JobApplication::select(
			'job_applications.*',
			DB::raw('jobs.employer_id'),
			DB::raw('employers.company_name')
		);
		$subResults->join('jobs', 'jobs.id', '=', 'job_applications.job_id');
		$subResults->join('employers', 'employers.id', '=', 'jobs.employer_id');
		$subResults->where('job_applications.job_id','=',$jobId);
		$subResults->where('job_applications.user_id','=',$applicantId);
		$subResults->where('jobs.id','=',$jobId);
		$subResults->where('employers.user_id','=',$loginUserId);

		return $subResults->first();	
	}

	///////////////////////////////
	/// Get By ID /////////////////
	///////////////////////////////

	public static function fetchJobSeekers($marker_ids) {
		$q = User::select('users.*')->whereIn('users.id', $marker_ids);
		$q->where('users.type', '=', User::TYPE_SEEKER);
		$q->where('users.level', '=', User::LEVEL_FRONTEND_USER);
		$q->join('user_profiles', 'user_profiles.user_id', '=', 'users.id');
		$q->where('user_profiles.profile_privacy', '<', 3);
		return $q->get();
	}

	public static function getPublicSearch() {
		return PublicSearch::where('results','>',0)->orderBy('results','desc')->get();
	}

	public static function getJobApplication($id) {
		return JobApplication::find($id);
	}

	public static function getExperienceLevel($id) {
		return ExperienceLevel::find($id);
	}

	public static function getExperience($id) {
		return Experience::find($id);
	}

	public static function getEducation($id) {
		$query = Education::where('id', $id);
		return $query->first();
	}

	public static function getUser($id) {
		$query = User::where('id', $id);
		return $query->first();	
	}
	public static function getUsercontact($id) {
		$query = UserContacts::where('id', $id);
		return $query->first();	
	}
	
	public static function getUserUsingEmail($email_address){
		$query = User::where('email_address', $email_address);
		return $query->first();		
	}

	public static function getJobAlert($id, $user = null) {
		$query = JobAlert::withTrashed()->where("id", $id);
		if(isset($user)) {
			$query->where("user_id", $user->id);
		}
		return $query->first();
	}

	public static function getCountry($id, $status=null) {
		$query = Country::where('id', '=',$id);
		if(isset($status)) {
			$query->where('status', $status);
		}
		return $query->first();
	}

	public static function getState($id, $status=null) {
		$query = State::where('id', '=',$id);
		if(isset($status)) {
			$query->where('status', $status);
		}
		return $query->first();
	}
	
	public static function getCity($id, $status=null) {
		$query = City::where('id', '=',$id);
		if(isset($status)) {
			$query->where('status', $status);
		}
		return $query->first();
	}

	public static function getSalaryRange($id) {
		return SalaryRange::find($id);
	}

	public static function getJobTitle($id) {
		return JobTitle::find($id);
	}

	public static function getJobType($id) {
		return JobType::find($id);
	}

	public static function getSkill($id) {
		return Tag::find($id);
	}

	public static function getJob($id) {
		return Job::find($id);
	}
	public static function getJobCategory($id) {
		return JobCategory::find($id);
	}
	public static function getSalaryType($id) {
		return SalaryType::find($id);
	}
	public static function getRecruiterType($id) {
		return RecruiterType::find($id);
	}

	///////////////////////////////
	/// Adding ////////////////////
	///////////////////////////////

	public static function addCertificateToUser($user, $certificate) {
		$userCertificate = new UserCertificate();
		$userCertificate->certificate = $certificate;
		return $user->certificates()->save($userCertificate);
	}

	public static function addUserSkill($user, $tag_id) {
		$userSkill = new UserSkill();
		$userSkill->tag_id = $tag_id;
		return $user->skills()->save($userSkill);
	}

	///////////////////////////////
	/// Partial Results ///////////
	///////////////////////////////

	public static function passwordResetOf($code) {
		return PasswordReset::where('code', $code)->first();
	}

	public static function jobApplicationsOf($user, $months = 3) {
		$startDate = \Carbon\Carbon::now()->addMonths(0-$months);
    	return $user->applications()->whereDate('created_at','>=',$startDate)->orderBy('created_at','desc')->get();
	}

	public static function salaryRangeOf($salaryTypeId) {
		return SalaryRange::where('salary_type_id', $salaryTypeId)->orderBy('range_from')->orderBy('range_to')->get();
	}

	public static function citiesOf($stateId, $status=null) {
		$query = City::orderBy('name')->where('state_id', $stateId);
		if(isset($status)) {
			$query->where('status', $status);
		}
		return $query->get();
	}	


	public static function statesOf($countryId, $status=null) {
		$query = State::orderBy('name')->where('country_id', $countryId);
		if(isset($status)) {
			$query->where('status', $status);
		}
		return $query->get();
	}

	public static function jobTitlesOf($jobCategoryId, $q) {
		$query = JobTitle::orderBy('title')->where('job_category_id', $jobCategoryId);
		if(isset($q))
			$query->where("title",'like','%'.$q.'%');

		return $query->get();
	}

	public static function skillsOf($jobTitleId) {
		return Tag::orderBy('name')->where('job_title_id', $jobTitleId)->get();
	}

	public static function jobKeywordsOf($jobId) {
		return JobKeyword::orderBy('keyword')->where('job_id', $jobId)->get();
	}

	public static function jobCertificateOf($jobId) {
		return JobCertificate::orderBy('certificate')->where('job_id', $jobId)->get();
	}

	public static function jobWeekdayOf($jobId) {
		return JobWeekday::orderBy('id')->where('job_id', $jobId)->get();
	}

	public static function getsalaryof($salary,$salaryRangeId) {
		$q = SalaryRange::where('range_from', '<=', $salary)
		->where('range_to', '>=', $salary)
		->where('id', '=', $salaryRangeId)
		->first();
		if($q){
			return [true, $q];
		}else {
			return [false, null];
		}

	}
	

	///////////////////////////////
	/// All Methods ///////////////
	///////////////////////////////

	public static function allIndustries() {
		return Industry::orderBy("name")->get();
	}

	public static function allJobs() {
		return Job::orderBy('created_at', 'desc')->where('status','active')->get();
	}

	public static function getallJobs() {
		return Job::orderBy('created_at', 'desc')->get();
	}

	public static function allRecruiterTypes() {
		return RecruiterType::orderBy('name')->get();
	}

	public static function allSearchDayAgos() {
		return SearchDayAgo::orderBy('day')->get();
	}

	public static function allSearchMiles() {
		return SearchMile::orderBy('mile')->get();
	}

	public static function allLocations($status=null,$limit=0) {
		$query = City::select('cities.*','states.name as state_name','states.id as state_id','countries.id as country_id')
						->join('states', 'states.id','=','cities.state_id')
						->join('countries', 'countries.id','=','states.country_id');
		if(isset($status)) {
			$query->where('cities.status', $status);
			$query->where('states.status', $status);
			$query->where('countries.status', $status);
		}
		if($limit == 0) {
			return $query->get();
		} else {
			return $query->paginate($limit);
		}
	}

	public static function allJObLocations($stateId,$defaultQuery) {
			$q = Job::select(
				"jobs.*", 
				DB::raw("job_addresses.id as addressId"),
				DB::raw("cities.name as city_name,cities.id as city_id"),
				DB::raw("states.name as state_name,states.id as state_id"),
				DB::raw("countries.id as country_id,countries.name as country_name")
			);
			$q->join('job_addresses', 'job_addresses.job_id','=','jobs.id');
			$q->join('cities', 'cities.id','=','job_addresses.city_id');
	 		$q->join('states', 'states.id','=','cities.state_id');
	 		$q->join('countries', 'countries.id','=','states.country_id');
	 		$q->where('cities.status', 0);
	 		$q->where('states.status', 0);
	 		$q->where('countries.status', 0);
	 		$q->whereNull('job_addresses.deleted_at');
			$q->whereNull('cities.deleted_at');
			$q->whereNull('states.deleted_at');
			$q->whereNull('countries.deleted_at');
			$q->whereNull('jobs.deleted_at');
			$q->notExpired();
			$q->notEnded();
			$q->activeOnly();
			if($defaultQuery){
				$q->where('states.id', $stateId);	
			}
				
			$q->groupBy('cities.id');
			return $q->get();
	}
	// public static function allJObLocations($stateId,$defaultQuery) {
	// 	$query = City::select('job_addresses.*','cities.name as city_name','states.name as state_name','states.id as state_id','countries.id as country_id','countries.name as country_name')
	// 				->join('job_addresses', 'job_addresses.city_id','=','cities.id')
	// 				->join('states', 'states.id','=','cities.state_id')
	// 				->join('countries', 'countries.id','=','states.country_id');
	// 			$query->where('cities.status', 0);
	// 			$query->where('states.status', 0);
	// 			$query->where('countries.status', 0);
	// 			if($defaultQuery){
	// 				$query->where('states.id', $stateId);	
	// 			}
				
	// 			$query->groupBy('cities.id');
	// 		return $query->get();
		
	// }

	public static function allStates($status=null) {
		$query = State::orderBy('name');
		if(isset($status)) {
			$query->where('status', $status);
		}
		return $query->get();
	}

	public static function allCities($status=null) {
		$query = City::orderBy('name');
		if(isset($status)) {
			$query->where('status', $status);
		}
		return $query->get();
	}

	public static function allCountries($status=null) {
		$query = Country::orderBy('name');
		if(isset($status)) {
			$query->where('status', $status);
		}
		return $query->get();
	}

	public static function allPersonTitles() {
		return PersonTitle::orderBy('order')->get();
	}

	public static function allEducations() {
		return Education::orderBy('order')->get();
	}

	public static function allCertificates() {
		return Degree::orderBy('name')->get();
	}

	public static function allSalaryTypes() {
		return SalaryType::orderBy('order')->get();
	}

	public static function allJobCategories() {
		return JobCategory::orderBy('name')->get();
	}

	public static function allJobTitles() {
		return JobTitle::orderBy('title')->get();
	}

	public static function allSkills() {
		return Tag::orderBy('name')->get();
	}

	public static function allJobTypes() {
		return JobType::orderBy('order')->get();
	}

	public static function allExperiences() {
		return Experience::orderBy('order')->get();
	}

	public static function allExperienceLevels() {
		return ExperienceLevel::orderBy('order')->get();
	}

	public static function allTags() {
		return self::allSkills();
	}

	public static function allJobKeywords() {
		return JobKeyword::orderBy('keyword')->get();
	}

	public static function allEmployers() {
		return Employer::orderBy('company_name')->get();
	}

	public static function allPayBy() {
		return PayBy::orderBy('name')->get();
	}

	public static function allPayPeriod() {
		return PayPeriod::orderBy('order')->get();
	}

	public static function getEmployerUserAddress($userId){
		return $user_address = UserAddress::where('type','residance')->where('user_id',$userId)->first();
	}

	public static function getEmployerUser($userId){
		return $user = User::where('id',$userId)->first();
	}

	public static function getEmployer($Id){
		return Employer::where('user_id',$Id)->first();	
	}
	public static function getEmployerData($Id){
		return Employer::where('id',$Id)->first();	
	}

	public static function getApplicationjob($job_id){
		return Job::where('id',$job_id)->first();	
	}

	public static function getJobEmployer($EmployerId) {
		$query = Employer::select('employers.user_id','users.*')
						->join('users', 'users.id','=','employers.user_id');
			
			$query->where('employers.id',$EmployerId);
			return $query->first();
	}

	public static function getJobEmployerOf($applicationId){
		$query = JobApplication::select('job_applications.job_id as jobId','jobs.employer_id','employers.*')
					->join('jobs', 'jobs.id','=','job_applications.job_id')
					->join('employers', 'employers.id','=','jobs.employer_id');
			
			$query->where('job_applications.id',$applicationId);
			return $query->first();	
	}

	public static function getUserData($applicationId){
		$query = JobApplication::select('job_applications.user_id','users.*')
					->join('users', 'users.id','=','job_applications.user_id');
			
			$query->where('job_applications.id',$applicationId);
			return $query->first();		
	}

	public static function getJobSeekerOrRecruiter($type,$search="",$is_paging=true){
		if($type=='jobseeker'){
			$query = User::where('type', 'JOB_SEEKER')->where('name','like','%'.$search.'%')->where('status', 'ACTIVATED');
			if($is_paging){
				return $query;
			}else{
				return $query->get();
			}

		}else{
			$query = User::where('type', 'EMPLOYER')->where('name','like','%'.$search.'%')->where('status', 'ACTIVATED');
			if($is_paging){
				return $query;
			}else{
				return $query->get();
			}
		}
		return null;
		
	}

	public static function getJobSeekerOrRecruiterForAdminChat($type,$search="",$is_paging=true,$is_messages=0)
	{
		$userData = MyAuth::user('admin');
		$query = User::select(
						DB::raw("users.*"),
						DB::raw('(SELECT count(*) FROM messages where receiver = "'.$userData->id.'" AND readed = "0" AND messages.sender = users.id AND is_message = "'.$is_messages.'") AS msg_count'),
						DB::raw('(SELECT created_at FROM messages where receiver = "'.$userData->id.'" AND readed = "0" AND messages.sender = users.id AND is_message = "'.$is_messages.'" ORDER BY created_at DESC LIMIT 1) AS msg_created')
					);
		$query->where('users.type','=' , $type);
		$query->where('users.status','=' ,'ACTIVATED');
		$query->where('users.name','like','%'.$search.'%');
		$query->orderBy('msg_created','DESC');
		$query->orderBy('msg_count','DESC');
		
		if($is_paging){
			return $query;
		}else{
			return $query->get();
		}
		return null;
		
		// // Query1 = Select count (*) as msg_count, messages.ref from users, messages where (messages.fromid=users.id or messages.toid=users.id) 
		// //and users.id=[LOGGEDUSERID]
		// // Query2 = Select * from users, messages, (QUERY1) q1 where (fromid=users.id or toid=users.id) and messages.ref=q1.ref 
		// //order by q1.msg_count, messages.created_date
		// if($type=='jobseeker')
		// {
			
		// 	$query = User::select(
		// 		"users.*",
		// 		DB::raw("messages.id as MessageId")
		// 	);
		// 	$query->leftjoin('messages', 'messages.sender','=','users.id');
		// 	$query->where('users.type','=' ,'JOB_SEEKER');
		// 	$query->where('users.status','=' ,'ACTIVATED');
		// 	$query->where('users.name','like','%'.$search.'%');
		// 	$query->orderBy('messages.created_at','desc');
		// 	/*if($is_messages==0){
		// 		$query->orderBy('messages.is_message');
		// 		$query->orderBy('messages.id','DESC');
		// 	}else{
		// 		$query->orderBy('messages.is_message','desc');
		// 		$query->orderBy('messages.id','DESC');
		// 	}*/
			
		// 	//$query->groupBy('users.id');

		// 	if($is_paging){
		// 		return $query;
		// 	}else{
		// 		return $query->get();
		// 	}
		// 	// $query = User::select('users.*','messages.id as MessageId')
		// 	// 	->where('users.type', 'JOB_SEEKER')
		// 	// 	->where('users.name','like','%'.$search.'%')
		// 	// 	->where('users.status', 'ACTIVATED')

		// 	// if($is_paging){
		// 	// 	return $query;
		// 	// }else{
		// 	// 	return $query->get();
		// 	// }

		// }else{
		// 	$query = User::select(
		// 		"users.*", 
		// 		DB::raw("messages.id as MessageId")
		// 	);
		// 	$query->leftjoin('messages', 'messages.sender','=','users.id');
		// 	$query->where('users.type','=' ,'EMPLOYER');
		// 	$query->where('users.status','=' ,'ACTIVATED');
		// 	$query->where('users.name','like','%'.$search.'%');

		// 	if($is_messages==0){
		// 		$query->where('users.status','=' ,'ACTIVATED');
		// 		$query->orderBy('messages.is_message','desc');
		// 		$query->orderBy('messages.id','DESC');
		// 	}else{
		// 		//$query->where('messages.is_message','=' ,1);
		// 		$query->orderBy('messages.is_message','desc');
		// 		$query->orderBy('messages.id','DESC');
		// 	}
		// 	$query->groupBy('users.id');
		// 	//$query->where('messages.is_message',"=",$is_messages);
		// 	// $query->orderBy('messages.created_at','desc');
		// 	// if($is_messages==0){
		// 	// 	$query->orderBy('messages.is_message');
		// 	// }else{
		// 	// 	$query->orderBy('messages.is_message','DESC');
		// 	// }
		// 	// $query->groupBy('users.id');

		// 	if($is_paging){
		// 		return $query;
		// 	}else{
		// 		return $query->get();
		// 	}
		// 	// $query = User::where('type', 'EMPLOYER')->where('name','like','%'.$search.'%')->where('status', 'ACTIVATED');
		// 	// if($is_paging){
		// 	// 	return $query;
		// 	// }else{
		// 	// 	return $query->get();
		// 	// }
		// }
		// return null;


		
	}

	// Resize image to 100x100
	public static function resizeImageTo100x100($imageName, Request $request)
	{
		$background = Image::canvas(100, 100);
        $image = Image::make($request->file('image')->getRealPath())->resize(100, 100, function($c){
            $c->aspectRatio();
            $c->upsize();
        });
        $background->insert($image, 'center');
        $background->save(storage_path().'/app/avatars/100x100/'.$imageName);
	}

	// Resize image to 200x200
	public static function resizeImageTo200x200($imageName, Request $request)
	{
		$background = Image::canvas(200, 200);
        $image = Image::make($request->file('image')->getRealPath())->resize(200, 200, function($c){
            $c->aspectRatio();
            $c->upsize();
        });
        $background->insert($image, 'center');
        $background->save(storage_path().'/app/avatars/200x200/'.$imageName);
	}

	public static function updateJobStatus($jobId) {
			$Job = Job::where('id', $jobId)->first();
			if($Job){
				$Job->status = User::STATUS_DEACTIVATED;;
			}
			return $Job->update();
	}

	public static function getAlertJob() {
		return Job::where('is_jobalert',"!=",'1')->get();
	}

	public static function UpdateUserAlertJobStatus($email,$jobData) {
		$user = User::where('email_address', $email)->first();

		if($user){
			$user->jobAlerted = trim($user->jobAlerted.",".implode(",", $e),",");
			$user->update();
		}
	}

	Public static function getTermCondition(){
		return CmsPage::where('page_name', 'terms')->where('status', '1')->first();
	}

	public static function getlocationSearchState($locationSearch){
		$valid_location=null;
		$locationData=array_filter($locationSearch);
		if($locationData){
			if(isset($locationData[2])){
				$CountryData=Country::where('name','=',trim($locationData[2]))->where('status', '0')->first();
				if($CountryData){
					$StateData=State::where('name','=',trim($locationData[1]))->where('country_id','=',$CountryData->id)->where('status', '0')->first();
					if($StateData){
						$CityData=City::where('name','=',trim($locationData[0]))->where('state_id','=',$StateData->id)->where('status', '0')->first();
						if($CityData){
							$valid_location = $CityData;;
						}
					}
				}
			}elseif(isset($locationData[1])){
				$StateData=State::where('name','=',trim($locationData[1]))->where('status', '0')->first();
				if($StateData){
					$CityData=City::where('name','=',trim($locationData[0]))->where('state_id','=',$StateData->id)->where('status', '0')->first();
					if($CityData){
						$valid_location = $CityData;;
					}
				}
			}elseif(isset($locationData[0])){
				$CityData=City::where('name','=',trim($locationData[0]))->where('status', '0')->first();
				if($CityData){
					$valid_location = $CityData;
				}
			}
		}
		
		return $valid_location;
		



		// if(isset($locationSearch[0])){
		// 	return City::where('name', $locationSearch[0])->where('status', '0')->first();	
		// }else{
		// 	return false;
		// }
		
	}

	Public static function IsUnicJob($EmployerId,$title,$cityId,$startDate,$JobId=null){
		
		if($EmployerId && $cityId){
			$subResults = Job::select(
				'jobs.*',
				DB::raw('job_addresses.job_id'),
				DB::raw('employers.company_name')
			);
			$subResults->join('job_addresses', 'job_addresses.job_id', '=', 'jobs.id');
			$subResults->join('employers', 'employers.id', '=', 'jobs.employer_id');
			$subResults->where('jobs.title','=',$title);
			$subResults->where('employers.id','=',$EmployerId);
			$subResults->where('job_addresses.city_id','=',$cityId);
			$subResults->where('jobs.starting_date',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
			$subResults->where('jobs.starting_date',"<=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 23:59:59'));
			if($JobId){
				$subResults->where('jobs.id','!=',$JobId);
			}
			
			$result=$subResults->first();	
			if($result){
				return [false,"one job is already exit please enter another jobs.",$result];
			}else{
				return [true,"successfully",null];
			}
		}else{
			return [false,"City or employer is not found",null];	
		}
	}

	public static function addUserContacts($data,$LoginUser,$type){

		foreach ($data as $value) {
			$user = UserContacts::where('email','=',$value['email'])->where('user_id','=',$LoginUser->id)->first();

			if(!$user){
				$UserContacts = new UserContacts();
				$UserContacts->user_id = $LoginUser->id;
				$UserContacts->name = $value['name'];
				$UserContacts->email = $value['email'];
				$UserContacts->mobile_number = "";
				$UserContacts->status = User::STATUS_ACTIVATED;
				$UserContacts->type = $type;
				if($UserContacts->save()){
					
				}
			}else{
				$user->user_id = $LoginUser->id;
				$user->name = $value['name'];
				$user->email = $value['email'];
				$user->mobile_number = "";
				$user->status = User::STATUS_ACTIVATED;
				$user->type = $type;
				if($user->update()){

				}

			}
		}

		return [true,"successfully",null];
	}

	public static function countJobs(){
		return Job::count();
	}

	public static function countUsers(){
		return User::count();
	}

	Public static function getJobApplicationRecord($user,$applicantId){
		$subResults = User::select(
				'users.*',
				DB::raw('employers.company_name'),
				DB::raw('jobs.id as applicant_jobId'),
				DB::raw('job_applications.user_id as userId,job_applications.job_id as user_jobId')
				
			);
			$subResults->join('employers', 'employers.user_id', '=', 'users.id');
			$subResults->join('jobs', 'jobs.employer_id', '=', 'employers.id');
			$subResults->join('job_applications', 'job_applications.job_id', '=', 'jobs.id');
			$subResults->where('employers.user_id','=',$user->id);
			$subResults->where('job_applications.user_id','=',$applicantId);
			return $result=$subResults->get();
	}

	public static function getPageTitle($url){
		$title = null;
		$dom = new \DOMDocument();
		if (@$dom->loadHTMLFile($url))
		{
		  $elements = $dom->getElementsByTagName('title');
		  if ($elements->length > 0)
		  {
		    $title = $elements->item(0)->textContent;

		  }
		}
		return $title;
	}
}