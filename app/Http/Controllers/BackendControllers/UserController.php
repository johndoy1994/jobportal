<?php

namespace App\Http\Controllers\BackendControllers;

use App\Helpers\Notifier;
use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\User;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Repos\AdminRepo;
use App\Repos\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends BackendController
{
    public function getListing(Request $request)
    {	
        
        if($request->has("search")) {
            $q =User::where('name', 'like', "%".$request["search"]."%")->orWhere('mobile_number', 'like', "%".$request["search"]."%")->orWhere('email_address', 'like', "%".$request["search"]."%")->orWhere('type', 'like', "%".$request["search"]."%")->orWhere('level', 'like', "%".$request["search"]."%");
        } else {
            $q =User::where('name', 'like', "%%");
        }

        if($request->has("type")) {   
            $search=$request["type"];
            if($search=='backend_admin'){
                $q->where(function($q) use($search) {
                    $q->orWhere('level','like','%'.$search.'%');
                });
            }else{
                $q->where(function($q) use($search) {
                    $q->orWhere('type','like','%'.$search.'%');
                });
            }
                
        }

    	if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            $q->orderBy('created_at','desc');
        }
        
        $recordsPerPage = $this->recordsPerPage("admin-user-list");

        $Users = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['name', 'mobile_number', 'email_address', 'type', 'level'];

        $sort_columns = [];
        foreach ($columns as $column) {
            $sort_columns[$column]["params"] = [
                'page' => $page,
                'sortBy' => $column
            ];
            if($request->has('sortOrder')) {
                $sort_columns[$column]["params"]["sortOrder"] = $request["sortOrder"] == "asc" ? "desc" : "asc";
                if($sort_columns[$column]["params"]["sortOrder"] == "asc") {
                    $sort_columns[$column]["angle"] = "up";
                } else {
                    $sort_columns[$column]["angle"] = "down";
                }
            } else {
                $sort_columns[$column]["params"]["sortOrder"] = "desc";
                $sort_columns[$column]["angle"] = "down";
            }
            
            if($request->has("search")) {
                    $sort_columns[$column]["params"]["search"] = $request->search;
            }

        }
        $countType = AdminRepo::CountEmployerJObseekerAndAdmin();
        $isRequestSearch=$request->has('search');
//echo'<pre>';print_r($Users);exit;
    	return view('backend.user.listing', [
    		"Users" => $Users,
            'sort_columns' => $sort_columns,
            'isRequestSearch'=>$isRequestSearch,
            'countType'=>$countType,
		]);
    }

    public function postListing(Request $request)
    {
        $action = $request->submit;

        switch($action) {
            case "Apply":
                switch($request["bulkid"]) {
                    case "deleted":
                        if(count($request["usermultiple"]) > 0) {
                            foreach ($request["usermultiple"] as $value) {
                                $val = User::find($value);
                                $val->delete();
                            }
                            //Tag::whereIn('id', $request["tagmultiple"])->delete();
                            return redirect()->back()->with([
                                'success_message' => "Deleted successfully"
                            ]);
                        } else {
                            return redirect()->back()->with([
                                'error_message' => "No items selected to delete!!"
                            ]);
                        }
                    break;

                    default:
                        return redirect()->back()->with([
                                'error_message' => "Please select any bulk action!!"
                            ]);
                    break;
                }

            break;

            case "Search":
                return redirect()->route('admin-user-list', ['search'=>$request->filter])->withInput(Input::all());
            break;
        }
    }

    public function getEditUser(Request $request, User $user){
        $login_user=MyAuth::user('admin');
        if($user->type=='BACKEND'){
            if($login_user->type!='BACKEND'){
                return redirect()->route('admin-user-list')->with([
                'error_message' => "You are not authorize to edit admin profile!"
                ]);
            }
        }
        if($user->type=='EMPLOYER'){
            $userAddress = PublicRepo::getEmployerUserAddress($user->id);
            $Employer = PublicRepo::getEmployer($user->id);
            $Countries = PublicRepo::allCountries(0);
            $Recruitertype = PublicRepo::allRecruiterTypes();
        
            $filename = 'avatars/100x100/'.$user->id.'.png';
            if(Storage::exists($filename)) {
                $image_vallid=true;
            } else {
                $image_vallid=false;
            }
            return view('backend.user.edit-employer',[
                'employers'=>$Employer,
                'userAddress'=>$userAddress,
                'User'=>$user,
                'countries'=>$Countries,
                'Recruitertypes'=>$Recruitertype,
                'image_vallid'=>$image_vallid
                ]);

            }elseif($user->level=='BACKEND_ADMIN'){
                $filename = 'avatars/100x100/'.$user->id.'.png';
                if(Storage::exists($filename)) {
                    $image_vallid=true;
                } else {
                    $image_vallid=false;
                }
                return view('backend.user.edit-admin',[
                'User'=>$user,
                'image_vallid'=>$image_vallid
                ]);
            }elseif ($user->type=='JOB_SEEKER') {
                //$user = MyAuth::user();
                $filename = 'avatars/100x100/'.$user->id.'.png';
                if(Storage::exists($filename)) {
                    $image_vallid=true;
                } else {
                    $image_vallid=false;
                }
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
//echo'<pre>';print_r($current_values);exit;
                return view('backend.user.edit-jobseeker', [
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
                    'values' => $current_values,
                    'image_vallid'=>$image_vallid
                ]);
            }
    }

    public function postEditJobseeker(Request $request, User $user) {

        $this->validate($request, [
            'title'                 => 'required|not_in:0|integer',     // user
            'first_name'            => 'required|min:1|alpha_single_name',
            'surname'               => 'required|min:1|alpha_single_name',
            'email_address'         => 'required|email|unique:users,email_address,'.$user->id,
            'mobile_number'         => 'digits:10',

            //hidden 'country'              => 'required|not_in:0|integer',     // user_address
            //hidden 'state'                    => 'required|not_in:0|integer',
            'city'                  => 'required|not_in:0|integer',
            //'street'                => 'required',
            'postalcode'            => 'postalcode',

            'education'             => 'required|not_in:0|integer',     // user_experiences
            'recent_job_title'      => 'required', 
            //hidden 'current_salary_type'  => 'required|not_in:0|integer',
            'current_salary_range'  => 'required|not_in:0|integer',
            //removed 'job_type'                => 'required|not_in:0|integer',
            'experience'            => 'required|not_in:0|integer',
            'experience_level'      => 'required|not_in:0|integer',

            'desired_job_title'     => 'required|not_in:0|integer',
            //array 'desired_location'      => 'address',
            //hidden 'desired_salary_type'  => 'required|not_in:0|integer',
            'desired_salary_range'  => 'required|not_in:0|integer',
            
            //'certificates'                                            // user_certificates
            //'skills'                                                  // user_skills

            //'about_me'                => '',          // user_settings
            'profile_privacy'       => 'required|in:1,2,3|integer'
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

        $skills             = $request->has('skills') ? $request->skills : [];
        $desired_locations  = $request->has('desired_locations') ? $request->desired_locations : [];
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
        $messages[] = $desired_locations_saved[0] ? "Desired locations saved" : $desired_locations_saved[1];
        $messages[] = $user_jobtypes[0] ? "User job types saved" : $user_jobtypes[1];

        $finalStatus = $user_jobtypes[0] && $user_saved[0] && $address_saved[0] && $experience_saved[0] && $profile_saved[0] && $certificates_saved[0] && $skills_saved[0] && $desired_locations_saved[0];
        $messageKey = $finalStatus ? "success_message" : "error_message";

        return redirect()->route('admin-user-list')->withInput(Input::all())->with([
            "$messageKey" => $messages
        ]);

    }

    public function postEditEmployer(Request $request, User $user) {
        
        $this->validate($request, [
            'recruiter_type_id' => 'required|findId:recruiter_types',
            'company_name' => 'required',
            'name' => 'required',
            'mobile_number' => 'min:10|numeric|unique:users,mobile_number,'.$user->id,
            'email_address' => 'required|email|email|unique:users,email_address,'.$user->id,
            'country_id' => 'required|findId:countries',
            'state_id' => 'required|findId:states,,country_id,'.$request->country_id,
            'city_id' => 'required|findId:cities,,state_id,'.$request->state_id,
            //'street' => 'required',
            'postal_code' => 'postalcode'
        ]);
        $Employer = PublicRepo::getEmployer($user->id);
        
        list($status[0],$message[0],$user)=AdminRepo::updateEmployerUser($user->id,$request->all());
        
            list($status[1],$message[1]) = UserRepo::updateUserAddress($user, "residance", array(
            'city_id'       => $request->city_id,
            'postal_code'   => $request->postal_code,
            'street'        => $request->street
        ));

        list($employerStatus,$employerStatus,$employer)=AdminRepo::updateEmployer($Employer,$request->all());
        if($employerStatus) {
            if($status[0] && $status[1]){
                return redirect()->route('admin-user-list',['type'=>$request->type,'page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
                'success_message' => "User successfully saved!"
                ]);
            }else{
                return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while adding your user or address field, try again"
                ]);
            }
            
        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while adding your User, try again"
            ]);
        }
    }

    public function postEditAdmin(Request $request, User $user) {
        
        $this->validate($request, [
            'name' => 'required',
            'mobile_number' => 'min:10|numeric|unique:users,mobile_number,'.$user->id,
            'email_address' => 'required|email|email|unique:users,email_address,'.$user->id,
        ]);
        
        list($status[0],$message[0],$user)=AdminRepo::updateEmployerUser($user->id,$request->all());
        
        if($status[0]){
            return redirect()->route('admin-user-list',['type'=>$request->type,'page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
            'success_message' => "admin successfully saved!"
            ]);
        }else{
            return redirect()->back()->withInput(Input::all())->with([
            'error_message' => "There was an error while adding your admin, try again"
            ]);
        }
        
    }

     //add employer
    public function getActiveInactiveUser(Request $request){
        
        if($request->action=='active'){
           $status=User::STATUS_ACTIVATED;
        }elseif ($request->action=="inactive") {
            $status=User::STATUS_DEACTIVATED;
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while update your employer status, try again"
            ]);   
        }

        list($status,$message,$jobrec) = AdminRepo::updateEmployerAction($request->user_id, $status);
        if($status){
                $user=PublicRepo::getUser($request->user_id);
                if($user->status=="ACTIVATED"){
                    Notifier::employerActivationMail($user);
                }
            
           return redirect()->route('admin-user-list',['page'=>$request->page,'type'=>$request->type,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
                'success_message' => "Status successfully saved!"
            ]); 
       }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while update your employer, try again"
            ]);
       }
    }

    //Delete User
    public function postDeleteUser(Request $request, User $user) {

        if($user->delete()) {
            return redirect()->back()->withInput(Input::all())->with([
                'success_message' => "User successfully deleted!"
            ]);
        } else {
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while deleting your user, try again!"
            ]);
        }

    }

}
