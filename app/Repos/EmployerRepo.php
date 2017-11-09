<?php

namespace App\Repos;

use App\Models\Job;
use App\Models\JobAddress;
use App\Models\JobCertificate;
use App\Models\JobSkill;
use App\Models\JobWeekday;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Repos\GeneralRepo;

class EmployerRepo {

	public static function saveJob($job_id, $employer_id, $title, $vacancies, $city_id, $street, $postalcode, $job_title_id, $tags, $education_id, $experience_id, $experience_level_id, $job_type_id, $certificates, $starting_date, $ending_date, $weekdays, $schedule_from, $schedule_to, $pay_by_id, $pay_period_id, $salary_type_id, $salary, $expiration_date, $benefits, $description, $meta, $updateRenewDate = false) {

		$job = new Job();

		if($job_id > 0) {
			$job = Job::find($job_id);
			if($job) {

			} else {
				return [false, "Target job not available."];
			}
		}

		$job->employer_id = $employer_id;
		$job->title = $title;
		$job->vacancies = $vacancies;
		$job->job_title_id = $job_title_id;
		$job->education_id = $education_id;
		$job->experience_id = $experience_id;
		$job->job_type_id = $job_type_id;
		$job->experience_level_id = $experience_level_id;
		$job->starting_date = $starting_date;
		$job->ending_date = $ending_date;
		$job->work_schedule_from = $schedule_from;
		$job->work_schedule_to = $schedule_to;
		$job->salary_type_id = $salary_type_id;
		$job->salary = $salary;
		$job->pay_by_id = $pay_by_id;
		$job->pay_period_id = $pay_period_id;
		$job->benefits = $benefits;
		$job->description = $description;
		$job->expiration_date = $expiration_date;
		$job->status = 'active';
		if($updateRenewDate) {
			$job->renew_date = \Carbon\Carbon::now();
		}
		$job->meta = json_encode($meta);

		$opStatus = $job_id > 0 ? $job->update() : $job->save();

		if($opStatus) {

			//job addresss
			$city = PublicRepo::getCity($city_id);
			if($city) {
				$jobAddress = new JobAddress();
				if($job_id > 0) {
					$jobAddress = $job->jobAddresses;
				}
				$jobAddress->city_id = $city->id;
				$jobAddress->street = $street;
				$jobAddress->postal_code = $postalcode;
				$fullAddress = $city->fetchFullAddress();
				if(strlen($street)>0) {
					$fullAddress = $street.", ".$fullAddress;
				}
				list($pointSuccess, $point, $realAddress) = PublicRepo::getGeoLocationPoint($fullAddress);
				$jobAddress->latitude = $point[0];
				$jobAddress->longitude = $point[1];
				$opJobAddress = $job_id > 0 ? $jobAddress->update() : $job->jobAddresses()->save($jobAddress);
				if(!$opJobAddress) {
					$job->forceDelete();
					return [false, "Unable to process job address, please try again."];
				}
			} else {
				$job->forceDelete();
				return [false, "City is not available."];
			}

			//skills
			$jobSkills = [];
			if(!empty($tags))
			{
				foreach($tags as $tag) {
					$isTag = false;
					$opMessage = "";
					$mTag = null;
					list($isTag, $opMessage, $mTag) = GeneralRepo::findOrCreateTag($job_title_id, $tag, is_numeric($tag));
					if($isTag && $mTag) {
						$jobSkill = new JobSkill();
						$jobSkill->tag_id = $mTag->id;
						$jobSkills[] = $jobSkill;
					}
				}
			}
			$job->skills()->delete();
			$job->skills()->saveMany($jobSkills);

			// validate part-dates only on part-time type job
			$jobType = PublicRepo::getJobType($job_type_id);
			if($jobType->day_selection == 1) {
				if(!isset($meta["days"])) {
					$job->forceDelete();
					return [false, "Unable to process calendar dates, try again."];
				}
			}

			//certificates
			$certificatesArr = explode(",", $certificates);
			$jobCertificates = [];
			foreach ($certificatesArr as $certificate) {
				$jobCertificate = new JobCertificate();
				$jobCertificate->certificate = $certificate;
				$jobCertificates[] = $jobCertificate;
			}
			$job->certificates()->delete();
			$job->certificates()->saveMany($jobCertificates);

			// weekdays
			$jobWeekdays = [];
			if(!empty($weekdays))
			{
				foreach ($weekdays as $weekday) {
					$jobWeekday = new JobWeekday();
					$jobWeekday->day = $weekday;
					$jobWeekdays[] = $jobWeekday;
				}
			}
			$job->jobWeekday()->delete();
			$job->jobWeekday()->saveMany($jobWeekdays);

			return [true, "Job updated", $job->id];
		}

		return [false, "There was an error while publishing new job, try again."];
	}

	public static function getApplications($employer = null) {
		if(!isset($employer)) {
			$user = MyAuth::recruiter();
			if($user) {
				$employer = $user->employer;
			}
		}

		if($employer) {
			$applications = PublicRepo::getJobApplications($employer);
			return $applications;
		}

		return false;

	}

}

