<?php

namespace App\Http\Controllers\FrontendControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendControllers\FrontendController;
use App\Http\Requests;
use App\Models\Country;
use App\Models\PersonTitle;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Repos\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MyProfileController extends FrontendController
{
    public function __construct() {
    	parent::__construct();
    }

    public function getIndex(Request $request) {
        $user = MyAuth::user();

        $user_profile = $user->profile()->first();
        $user_address = $user->addresses()->where('type','residance')->first();
        $user_experience = $user->experiences()->first();
        $user_certificates = $user->certificates;
        $user_skills = $user->skills;
        $user_desired_locations = $user->addresses()->where('type','desired')->get();
        $user_jobtypes = $user->job_types;

        $current_values = [
            
            "person_title_id" => 0,
            "first_name" => $user->getFirstName(),
            "surname" => $user->getLastName(),
            "email_address" => $user->getEmailAddress(),
            "mobile_number" => $user->getMobileNumber(),

            "country_id" => 0,
            "state" =>  null,
            "city" => null,
            "street" => "",
            "postalcode" => "",

            "education_id" => 0,
            "certificates" => "",
            "recent_job_title" => "",
            "current_salary_type_id" => 0,
            "current_salary_range" => null,
            'desired_job_category_id' => 0,
            'desired_job_title' => null,
            'skills' => [],
            'job_title_skills' => [],
            'desired_locations' => [],
            "desired_salary_type_id" => 0,
            "desired_salary_range" => null,
            //'current_job_type_id' => 0,
            'experience_id' => 0,
            'experience_level_id' => 0,

            'about_me' => "",
            "profile_privacy" => 0,

            "job_types" => []

        ];

        if($user_profile) {
            $current_values["person_title_id"] = $user_profile->person_title_id;
            $current_values["about_me"] = $user_profile->about_me;
            $current_values["profile_privacy"] = $user_profile->profile_privacy;
        }        

        if($user_address) {
            if($user_address->country()) {
                $current_values["country_id"] = $user_address->country()->id;
            }
            if($user_address->country() && $user_address->country()->status == 0) {
                if($user_address->state()) {
                    $state = $user_address->state();
                    if($state->status == 0) {
                        $current_values["state"] = [$state->id, $state->name];
                        if($user_address->city && $user_address->city->status == 0) {
                            $current_values["city"] = [$user_address->city->id, $user_address->city->name];
                        }
                    }
                }
            }
            $current_values["street"] = $user_address->street;
            $current_values["postalcode"] = $user_address->postal_code;
        }

        if($user_experience) {
            $current_values["education_id"] = $user_experience->education_id;
            $current_values["recent_job_title"] = $user_experience->recent_job_title;
            if($user_experience->current_salary_range) {
                $current_values["current_salary_range"] = [$user_experience->current_salary_range->id, $user_experience->current_salary_range->range()];
                $current_values["current_salary_type_id"] = $user_experience->current_salary_range->salaryType->id;
            }
            if($user_experience->desired_job_title) {
                if($user_experience->desired_job_title->category) {
                    $current_values["desired_job_category_id"] = $user_experience->desired_job_title->category->id;
                }
                $current_values["desired_job_title"] = [$user_experience->desired_job_title->id,$user_experience->desired_job_title->getTitle()];
                $current_values["job_title_skills"] = PublicRepo::skillsOf(old('desired_job_title') ? old('desired_job_title') : $user_experience->desired_job_title_id);
            }
            if($user_experience->desired_salary_range) {
                $current_values["desired_salary_range"] = [$user_experience->desired_salary_range->id, $user_experience->desired_salary_range->range()];
                $current_values["desired_salary_type_id"] = $user_experience->desired_salary_range->salaryType->id;
            }
//            $current_values["current_job_type_id"] = $user_experience->current_job_type_id;
            $current_values["experience_id"] = $user_experience->experinece_id;
            $current_values["experience_level_id"] = $user_experience->experinece_level_id;
        }

        if($user_certificates) {
            $cert_line = "";
            foreach ($user_certificates as $cert) {
                $cert_line .= $cert->certificate . ", ";
            }
            $current_values["certificates"] = substr($cert_line, 0, strlen($cert_line)-2);
        }

        if($user_skills) {
            foreach ($user_skills as $user_skill) {
                if($user_skill->tag) {
                    $tag = $user_skill->tag;
                    $current_values["skills"][] = [$tag->id, $tag->getName()];
                }
            }
        }

        if($user_desired_locations) {
            foreach($user_desired_locations as $desired_location) {
                $dl_id = $desired_location->city_id.",".$desired_location->miles;
                $current_values["desired_locations"][] = [$dl_id, $desired_location->getFullLine(true)];
            }
        }

        if($user_jobtypes) {
            foreach ($user_jobtypes as $user_jobtype) {
                $current_values["job_types"][] = $user_jobtype->getTypeId();
            }
        }

        if($request->old()) {

            $current_values["person_title_id"] = old('title');
            $current_values["first_name"] = old('first_name');
            $current_values["surname"] = old('surname');
            $current_values["email_address"] = old('email_address');
            $current_values["mobile_number"] = old('mobile_number');
            $current_values["country_id"] = old('country');
            $oldState = PublicRepo::getState(old('state'));
            if($oldState) {
                $current_values["state"] = [$oldState->id, $oldState->getName()];
            }
            $oldCity = PublicRepo::getCity(old('city'));
            if($oldCity) {
                $current_values["city"] = [$oldCity->id, $oldCity->getName()];
            }
            $current_values["street"] = old('street');
            $current_values["postalcode"] = old('postalcode');
            $current_values['education_id'] = old('education');
            // $current_values["certificates"] = [];
            // foreach(old('certificates') as $certificate) {

            // }
            $current_values["recent_job_title"] = old('recent_job_title');
            $current_values["current_salary_type_id"] = old('current_salary_type');
            $oldCurrentSalaryRange = PublicRepo::getSalaryRange(old('current_salary_range'));
            if($oldCurrentSalaryRange) {
                $current_values["current_salary_range"] = [$oldCurrentSalaryRange->id, $oldCurrentSalaryRange->range()];
            }
            $current_values["desired_job_category_id"] = old('desired_job_category');
            $oldDesiredJobTitle = PublicRepo::getJobTitle(old('desired_job_title'));
            if($oldDesiredJobTitle) {
                $current_values["desired_job_title"] = [$oldDesiredJobTitle->id, $oldDesiredJobTitle->getTitle()];
            }
            //$current_values["skills"] = [];
            if(old("skills")) {
                foreach(old("skills") as $skill) {
                    $oldSkill = PublicRepo::getSkill($skill);
                    if($oldSkill) {
                        $current_values["skills"][] =  [$oldSkill->id, $oldSkill->getName()];
                    }
                }
            }

            $current_values["job_types"] = old('job_types');

            $current_values["certificates"] = old('certificates');

            //$current_values["desired_locations"] = old('desired_locations');
            if(old("desired_locations")) {
                $current_values["desired_locations"] = [];
                foreach(old("desired_locations") as $desired_location_value) {
                    $values = explode(",", $desired_location_value);
                    if(count($values) == 2) {
                        $city = PublicRepo::getCity($values[0]);
                        if($city) {
                            $dl_id = $city->id.",".$values[1];
                            $dl_title = $city->fetchFullAddress();
                            if($values[1]>0) {
                                $dl_title .= " + ".$values[1]." miles";
                            }
                            $current_values["desired_locations"][] = [$dl_id, $dl_title];
                        }
                    }
                }
            }

            $current_values["desired_salary_type_id"] = old('desired_salary_type');
            $oldDesiredSalaryRange = PublicRepo::getSalaryRange(old('desired_salary_range'));
            if($oldDesiredSalaryRange) {
                $current_values["desired_salary_range"] = [$oldDesiredSalaryRange->id, $oldDesiredSalaryRange->range()];
            }
//            $current_values["current_job_type_id"] = old('job_type');
            $current_values["experience_id"] = old('experience');
            $current_values["experience_level_id"] = old('experience_level');
            $current_values["about_me"] = old('about_me');
            $current_values["profile_privacy"] = old('profile_privacy');
        }

    	return view('frontend.account.my-profile.index', [
    		'user'          => $user,
            // 'user_profile'  => $user_profile,
            // 'user_address'  => $user_address,
            // 'user_experience' => $user_experience,
            // 'user_certificates' => $user_certificates,
            // 'user_skills'   => $user_skills,
            // 'user_desired_locations' => $user_desired_locations,

            'countries'     => PublicRepo::allCountries(0),
            'person_titles' => PublicRepo::allPersonTitles(),
            'educations'    => PublicRepo::allEducations(),
            'salaryTypes'   => PublicRepo::allSalaryTypes(),
            'jobCategories' => PublicRepo::allJobCategories(),
            'jobTypes'      => PublicRepo::allJobTypes(),
            'experiences'   => PublicRepo::allExperiences(),
            'experienceLevels'  => PublicRepo::allExperienceLevels(),
            'skills'          => PublicRepo::allTags(),
            "searchMiles"   => PublicRepo::allSearchMiles(),

            'values' => $current_values
		]);
    }

    public function postSaveProfile(Request $request) {

        $user = MyAuth::user();

    	$this->validate($request, [
    		'title' 				=> 'required|not_in:0|integer', 	// user
    		'first_name' 			=> 'required|min:1|alpha_single_name',
    		'surname' 				=> 'required|min:1|alpha_single_name',
    		'email_address' 		=> 'required|email|unique:users,email_address,'.$user->id,
    		'mobile_number' 		=> 'digits:10',

    		//hidden 'country'				=> 'required|not_in:0|integer', 	// user_address
    		//hidden 'state'					=> 'required|not_in:0|integer',
    		'city'					=> 'required|not_in:0|integer',
    		//'street'				=> 'required',
    		'postalcode'			=> 'postalcode',

    		'education'				=> 'required|not_in:0|integer', 	// user_experiences
    		'recent_job_title'		=> 'required', 
    		//hidden 'current_salary_type'	=> 'required|not_in:0|integer',
    		'current_salary_range'	=> 'required|not_in:0|integer',
    		//removed 'job_type'				=> 'required|not_in:0|integer',
    		'experience'			=> 'required|not_in:0|integer',
    		'experience_level'		=> 'required|not_in:0|integer',

    		'desired_job_title'		=> 'required|not_in:0|integer',
    		//array 'desired_location'		=> 'address',
    		//hidden 'desired_salary_type'	=> 'required|not_in:0|integer',
    		'desired_salary_range'	=> 'required|not_in:0|integer',
    		
    		//'certificates'											// user_certificates
    		//'skills'													// user_skills

    		//'about_me'				=> '',			// user_settings
    		'profile_privacy'		=> 'required|in:1,2,3|integer'
		]);
        
        Log::info("postSaveProfile", array($request->all()));

        $validCity = false;
        if($request->has('city')) {
            $city = PublicRepo::getCity($request->city);
            if($city && $city->status == 0) {
                if($city->State && $city->State->status == 0) {
                    if($city->State->Country && $city->State->Country->status == 0) {
                        $validCity = true;
                    }
                }
            }
        }

        if(!$validCity) {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'City/State/Country is not available, please try again.'
            ]);
        }

        if(PublicRepo::getEducation($request->education)) {

        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'Selected education is not valid, please try again.'
            ]);
        }

        if(PublicRepo::getSalaryRange($request->current_salary_range)) {

        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'Selected current salary range is not valid, please try again.'
            ]);
        }

        if(PublicRepo::getExperience($request->experience)) {

        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'Selected experience is not valid, please try again.'
            ]);
        }

        if(PublicRepo::getExperienceLevel($request->experience_level)) {

        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'Selected experience level is not valid, please try again.'
            ]);
        }

        if(PublicRepo::getJobTitle($request->desired_job_title)) {

        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'Selected desired job title is not valid, please try again.'
            ]);
        }

        if(PublicRepo::getSalaryRange($request->desired_salary_range)) {

        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'Selected desired salary range is not valid, please try again.'
            ]);
        }      

    	$certificate_line = $request->has('certificates') ? $request->certificates : "";
        $certificates = explode(",", $certificate_line);

    	$skills 			= $request->has('skills') ? $request->skills : [];
    	$desired_locations	= $request->has('desired_locations') ? $request->desired_locations : [];
        $job_types          = $request->has('job_types') ? $request->job_types : [];

        // Update logic
        // update basic details
        $user_saved = UserRepo::updateUser($user, array(
            "name"          => $request->first_name." ".$request->surname,
            "email_address"         => $request->email_address,
            "mobile_number" => $request->mobile_number,
        ));

        // city, street, postal_code, type=residance
        $address_saved = UserRepo::updateUserAddress($user, "residance", array(
            'city_id'       => $request->city,
            'postal_code'   => $request->postalcode,
            'street'        => $request->street
        ));

        // update exeperience details
        $experience_saved = UserRepo::updateUserExperience($user, array(
            "education_id"              => $request->education,
            //removed "current_job_type_id"       => $request->job_type,
            "current_salary_range_id"   => $request->current_salary_range,
            "experinece_id"             => $request->experience,
            "experinece_level_id"       => $request->experience_level,
            "desired_job_title_id"      => $request->desired_job_title,
            "desired_salary_range_id"   => $request->desired_salary_range,
            "recent_job_title"          => $request->recent_job_title
        ));

        // update user job types
        $user_jobtypes = UserRepo::updateUserJobTypes($user, $job_types);

        // user profile details
        $profile_saved = UserRepo::updateUserProfile($user, array(
            "person_title_id"   => $request->title,
            "about_me"          => $request->about_me,
            "profile_privacy"   => $request->profile_privacy
        ));

        // save certificates
        $certificates_saved = UserRepo::updateUserCertificates($user, $certificates);

        // save skills
        $skills_saved = UserRepo::updateSkills($user, $request->desired_job_title, $skills);

        // save desired locations
        $desired_locations_saved = UserRepo::updateDesiredLocations($user, $desired_locations);


        $messages = [];

        $messages[] = $user_saved[0] ? "Users details saved" : $user_saved[1];
        $messages[] = $address_saved[0] ? "Residance address saved" : $address_saved[1];
        $messages[] = $experience_saved[0] ? "Experience details saved" : $experience_saved[1];
        $messages[] = $profile_saved[0] ? "Profile privacy details saved" : $profile_saved[1];
        $messages[] = $certificates_saved[0] ? "Certificates details saved" : $certificates_saved[1];
        $messages[] = $skills_saved[0] ? "User skills details saved" : $skills_saved[1];
        $messages[] = $desired_locations_saved[0] ? "Desired City saved" : $desired_locations_saved[1];
        $messages[] = $user_jobtypes[0] ? "User job types saved" : $user_jobtypes[1];

        $finalStatus = $user_jobtypes[0] && $user_saved[0] && $address_saved[0] && $experience_saved[0] && $profile_saved[0] && $certificates_saved[0] && $skills_saved[0] && $desired_locations_saved[0];
        $messageKey = $finalStatus ? "success_message" : "error_message";

        return redirect()->back()->withInput(Input::all())->with([
            "$messageKey" => $messages
        ]);

    }

    public function postSaveProfilePicture(Request $request){
        // and for max size max:10000
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,gif,png'
        ]);

        $user = MyAuth::user();
        
        $imageName = $user->id . '.png';// . $request->file('image')->getClientOriginalExtension();

        $result=Storage::put(
            'avatars/'.$imageName,
            file_get_contents($request->file('image')->getRealPath())
        );

        if($result) {
            
            //Resize image to 100x100 and 200x200
            PublicRepo::resizeImageTo100x100($imageName, $request);
            PublicRepo::resizeImageTo200x200($imageName, $request);

            return redirect()->route('account-myprofile')->with([
                'success_message' => "Profile Picture uploaded successfull!"
            ]);
        } else {
            return redirect()->route('account-myprofile')->with([
                'error_message' => "There was an error please try again"
            ]);
        }
        
    }

}
