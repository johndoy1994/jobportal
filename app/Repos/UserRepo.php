<?php

namespace App\Repos;

use App\Models\Employer;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserCertificate;
use App\Models\UserExperience;
use App\Models\UserJobType;
use App\Models\UserProfile;
use App\Models\UserSkill;
use App\Repos\API\PublicRepo;
use App\Repos\GeneralRepo;
use App\Repos\Repo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserRepo extends Repo {

	public function create($data, &$user = null) {
		$user = new User($data);
		if($user->save()){
			return $user;
		}else{
			return false;	
		}
		
	}

	public static function updateUserJobTypes($user, $jobTypeIds) {
		$user->job_types()->delete();
		$newUserJobTypes = [];
		foreach($jobTypeIds as $jobTypeId) {
			if($realJobType = PublicRepo::getJobType($jobTypeId)) {
				$userJobType = new UserJobType();
				$userJobType->job_type_id = $realJobType->id;
				$newUserJobTypes[] = $userJobType;
			}
		}
		$user->job_types()->saveMany($newUserJobTypes);
		return [true, "Job Types saved"];
	}

	public static function updateUser($user, $values) {
		
		foreach ($values as $key => $value) {
			$user->{$key} = $value;
		}

		if($user->update()) {
			return [true, "Profile data saved"];
		} else {
			return [false, 'There was an error while saving your profile, try again'];			
		}
	}

	public static function addUserAddress($user, $address_type, $values) {

		$userAddress = new UserAddress();
		$userAddress->user_id = $user->id;
		$userAddress->type = $address_type;

		foreach ($values as $key => $value) {
			$userAddress->{$key} = $value;
		}

		if($userAddress->save()) {
			return [true, "User address added"];
		} else {
			return [false, "Unable to save address please try again"];
		}

	}

	public static function updateUserAddress($user, $address_type, $values, $createIfNotFound=true) {

	
		$userAddress = $user->addresses()->where('type', $address_type)->first();

		if($address_type != 'desired' && $userAddress) { // found, update
			foreach ($values as $key => $value) {
				$userAddress->{$key} = $value;
			}

			if($userAddress->update()) {
				return [true, "User address updated successfully"];
			} else {
				return [false, "Unable to save user address"];
			}

		} else { // not found, create one
			if($createIfNotFound) {
					return self::addUserAddress($user, $address_type, $values);
			} else {
				return [false, "User address not found"];
			}
		}

		
	}
	
	public static function addUserExperience($user, $values) {

		$userExperience = new UserExperience();
		$userExperience->user_id = $user->id;

		foreach ($values as $key => $value) {
			$userExperience->{$key} = $value;
		}

		if($userExperience->save()) {
			return [true, "User experience added"];
		} else {
			return [false, "Unable to save experience details please try again"];
		}

	}

	public static function updateUserExperience($user, $values, $createIfNotFound=true) {
		
		$userExperience  = $user->experiences()->first();
		if($userExperience) { // found, update

			foreach ($values as $key => $value) {
				$userExperience->{$key} = $value;
			}

			if($userExperience->update()) {
				return [true, "User experience updated successfully", $userExperience];
			} else {
				return [false, "Unable to save user experience"];
			}

		} else { // not found, create one
			
			if($createIfNotFound) {
					return self::addUserExperience($user, $values);
			} else {
				return [false, "User experience not found"];
			}

		}
	

	}

	public static function updateUserProfile($user, $values, $createIfNotFound=true) {

		$profile = $user->profile;

		if($profile) {
			foreach ($values as $key => $value) {
				$profile->{$key} = $value;
			}
			if($profile->update()) {
				return [true, "Saved"];
			} else {
				return [false, "There was an error while updating user profile."];
			}
		} else {
			if($createIfNotFound) {
				$userProfile = new UserProfile();
				$userProfile->user_id = $user->id;
				foreach ($values as $key => $value) {
					$userProfile->{$key} = $value;
				}
				$saved = $userProfile->save();
				if($saved) {
					return [true, "Saved"];
				} else {
					return [false, "There was an error while initializing user profile, try agian"];
				}
			} else {
				return [false, "User profile not found"];
			}
		}

	}

	public static function updateUserCertificates($user, $certs) {

		$user->certificates()->delete();

		$newCerts = [];
		foreach ($certs as $key => $value) {
			$cert = new UserCertificate();
			Log::info('updateCertificate', array("value"=>$value));
			$cert->certificate = trim($value);
			$newCerts[] = $cert;
		}

		$user->certificates()->saveMany($newCerts);

		return [true, "Certificates saved"];

	}

	public static function updateSkills($user, $job_title_id, $skills) {
		$user->skills()->delete();

		$addedTags = [];
		$newSkills = [];

		foreach ($skills as $key => $value) {

			if(isset($addedTags[$value])) {
				continue;
			} else {
				$addedTags[$value] = $key;
			}

			list($tagSaved, $tagMessage, $new_tag) = GeneralRepo::findOrCreateTag($job_title_id, $value, true);
			if($tagSaved && $new_tag) {
				$skill = new UserSkill();
				$skill->tag_id = $new_tag->id;
				$newSkills[] = $skill;
			}

		}

		$user->skills()->saveMany($newSkills);

		return [true, "User skills saved"];
	}

	public static function updateDesiredLocations($user, $locations) {

		$user->addresses()->where('type','desired')->delete();

		foreach ($locations as $key => $value) {

			$values = explode(",",$value);

			if(count($values)==2) {
				UserRepo::updateUserAddress($user, "desired", array(
					"city_id"	=> $values[0],
					"miles" => $values[1]
				));
				$city = PublicRepo::getCity($values[0]);
				if($city) {
					if($city->latitude == 0 && $city->longitude == 0) {
						list($valid_city_add, $point, $clearAdd) = PublicRepo::getGeoLocationPoint($city->fetchFullAddress());
						$city->latitude = $point[0];
						$city->longitude = $point[1];
						$city->update();
					}
				}
			}
	        
		}

		return [true, "User desired locations updated"];

	}

	public function updatePassword($id, $current, $new) {

		$user = User::where('id', $id)->first();

		if($user) {

			if(Hash::check($current, $user->password)) {
				$user->password=bcrypt($new);
				Log::info('UserRepo::updatePassword', array($id, $current, $new));
				if($user->update()) {
					return [true, 'Password successfully updated'];
				} else {
					return [false, 'There was an error please try again'];
				}
			} else {
				return [false, "Password doesn't match"];
			}
		}
		


		return [false, 'Invalid request, please try again'];

	}

	public function deleteAccount($id) {
		$user = User::where('id', $id)->first();
		if($user->delete()) {
			return [true, 'Account deleted successfully'];
		} else {
			return [false, 'There was an error please try again'];
		}
		
		return [false, 'Invalid request, please try again'];

	}

	public function addEmployer($user,$recruiterType,$company_name){
		$Employer = new Employer();
		$Employer->user_id = $user->id;
		$Employer->recruiter_type_id = $recruiterType;
		$Employer->company_name = $company_name;
		if($Employer->save()){
			return [true, "Saved", $Employer];
		} else {
			return [false, "Not saved", null];
		}
	}

	public static function updateEmployer($user, $values) {
		
		$employer = Employer::where('user_id', $user->id)->first();
		
		foreach ($values as $key => $value) {
			$employer->{$key} = $value;
		}

		if($employer->update()) {
			return [true, "Recruiter and company name saved"];
		} else {
			return [false, 'There was an error while save Recruiter type and company name, try again'];			
		}
	}

}

