<?php

namespace App\Http\Controllers\FrontendControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendControllers\FrontendController;
use App\Http\Requests;
use App\Models\JobAlert;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Repos\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends FrontendController
{
    public function __construct() {
        parent::__construct();
    }

    //Account Settings
    public function getIndex() {
    	$user = MyAuth::user();
        $userProfile = $user->profile;

    	$myResume = $user->resumes()->first();
        $profilePrivacy = $userProfile ? $userProfile->profile_privacy : 0;
        $jobAlerts = $user->job_alerts;
        $instantMatch = $user->instant_match;

    	return view('frontend.account.setting.index', [
    		'user'=>$user,
    		'hasResume' => $myResume ? true : false,
            'profileVisibility' => $profilePrivacy,
            "jobAlerts" => $jobAlerts,
            'instantMatch' => $instantMatch
		]);

    }

    public function postSaveProfileVisibility(Request $request) {
        $this->validate($request, [
            'profile_privacy' => 'required|in:1,2,3'
        ]);

        UserRepo::updateUserProfile(MyAuth::user(), array(
            "profile_privacy" => $request->profile_privacy
        ));

        return redirect()->back()->with(['success_message' => "Profile visibility updated."]);
    }

    public function getUpdateJobAlertStatus(Request $request, $alert, $action) {

        $alert = MyAuth::user()->job_alerts()->find($alert);
        if($alert) {
            list($success, $message) = PublicRepo::updateJobAlertStatus($alert, $action);
            if($success) {
                return redirect()->back()->with(['success_message'=>$message]);
            } else {
                return redirect()->back()->with(['error_message'=>$message]);
            }
        } else {
            return redirect()->back()->with(['error_message' => "Invalid Job Alert, try again."]);
        }        

    }

    public function getEditJobAlert(Request $request, $alert) {

        $alert = MyAuth::user()->job_alerts()->find($alert);

        if($alert) {

            return view('frontend.account.job-alerts.manage-alert', [
                'alert' => $alert,
                'user' => MyAuth::user(),
                'jobCategories' => PublicRepo::allJobCategories(),
                'searchMiles' => PublicRepo::allSearchMiles(),
                "allCountries" => PublicRepo::allCountries(0),
                "salaryTypes" => PublicRepo::allSalaryTypes(),
                "jobTypes" => PublicRepo::allJobTypes(),
                "industries" => PublicRepo::allIndustries()
            ]);

        } else {
            return redirect()->back()->with(['error_message' => "Alert not found, try again."]);
        }

    }

    public function getCreateJobAlert(Request $request) {

        return view('frontend.account.job-alerts.manage-alert', [
            'alert' => false,
            'user' => MyAuth::user(),
            'jobCategories' => PublicRepo::allJobCategories(),
            'searchMiles' => PublicRepo::allSearchMiles(),
            "allCountries" => PublicRepo::allCountries(0),
            "salaryTypes" => PublicRepo::allSalaryTypes(),
            "jobTypes" => PublicRepo::allJobTypes(),
            "industries" => PublicRepo::allIndustries()
        ]);

    }

    public function postSaveJobAlert(Request $request) {

        $this->validate($request, [
            "job_categories_id" => "required|findId:job_categories,0",
            "city_id" => "required|findId:cities,0",
            "job_title_id" => "required|findId:job_titles,0",
            //"keywords" => "required",
            "radius" => "required|integer",
            "salary_range_from" => "required|findId:salary_ranges,0",
            "job_type_id" => "required|findId:job_types,0",
            "industries_id" => "required|findId:industries,0"
        ], [
            "job_categories_id.required" => "Please select job category from list.",
            "job_categories_id.find_id" => "Selected job category not found, try again.",

            'city_id.required' => "Please select location from list.",
            "city_id.integer" => "Please select valid location from given list.",
            "city_id.find_id" => "Selected city not found, try again.",

            'job_title_id.required' => "Please select job title from list.",
            "job_title_id.integer" => "Please select valid job title from given list.",
            "job_title_id.find_id" => "Selected job title not found, try again.",

            'keywords.required' => "Please enter valid keyword.",
            
            'radius.required' => "Please select radius from list.",
            "radius.integer" => "Please select valid radius from given list.",

            'salary_range_from.required' => "Please select salary from list.",
            "salary_range_from.integer" => "Please select valid salary from given list.",
            "salary_range_from.find_id" => "Selected salary range not found, try again.",

            'job_type_id.required' => "Please select job type from list.",
            "job_type_id.integer" => "Please select valid job type from given list.",
            "job_type_id.find_id" => "Selected job type not found, try again.",            

            'industries_id.required' => "Please select industry from list.",
            "industries_id.integer" => "Please select valid industry from given list.",
            "industries_id.find_id" => "Selected industry not found, try again.",                        
        ]);

        $alert = null;

        if($request->has("alert_id")) {
            $alert = PublicRepo::getJobAlert($request->alert_id, MyAuth::user());
            if($alert) {

            } else {
                return redirect()->back()->with(['error_message' => "Job alert not found, try again."]);
            }
        }

        $updatedValues = [
            'city_id' => $request->city_id,
            'keywords' => $request->keywords,
            'radius' => $request->radius,
            'job_type_id' => $request->job_type_id,
            'industries_id' => $request->industries_id
        ];

        $jobTitle = PublicRepo::getJobTitle($request->job_title_id);
        if($jobTitle || $request->job_title_id == 0) {
            $jobTitleId = 0;
            $jobCategoryId = $request->job_categories_id;
            if($jobTitle) {
                $jobCategory = $jobTitle->category;
                $jobCategoryId = $jobCategory ? $jobCategory->id : 0;
                $jobTitleId = $jobTitle->id;
            }

            $updatedValues["job_title_id"] = $jobTitleId;
            $updatedValues["job_categories_id"] = $jobCategoryId;

            $salaryRange = PublicRepo::getSalaryRange($request->salary_range_from); 
            if($salaryRange || $request->salary_range_from == 0) {
                $salaryRangeFrom = 0;
                $salaryTypeId = 0;
                if($salaryRange) {
                    $salaryType = $salaryRange->salaryType;
                    $salaryTypeId = $salaryType ? $salaryType->id : 0;
                    $salaryRangeFrom = $salaryRange->range_from;
                }

                $updatedValues["salary_type_id"] = $salaryTypeId;
                $updatedValues["salary_range_from"] = $salaryRangeFrom;

                if($alert) {
                    foreach($updatedValues as $key=>$value) {
                        $alert->{$key} = $value;
                    }
                    $alert->update();
                } else {
                    $alert = new JobAlert();
                    foreach($updatedValues as $key=>$value) {
                        $alert->{$key} = $value;
                    }
                    MyAuth::user()->job_alerts()->save($alert);
                }

                return redirect()->back()->with(['success_message' => "Job alert saved!!"]);

            } else {
                return redirect()->back()->with(['error_message'=>"Salary not found, try again."]);    
            }

        } else {
            return redirect()->back()->with(['error_message'=>"Job title not found, try again."]);
        }

        if($alert) { // Edit mode

        } else { // Create Mode

        }

    }

    public function postInstantMatch(Request $request) {    

        $this->validate($request, [
            'status'=>'required|in:0,1|integer',
            'email_frequency'=>'required|in:1,2,3|integer',
            'push_frequency'=>'required|in:1,2,3|integer'
        ]);

        if($user = MyAuth::user()) {
            $fields = [
                'status' => $request->status,
                'email_frequency' => $request->email_frequency,
                'push_frequency' => $request->push_frequency
            ];
            if($request->has('pause')) {
                $fields["pause"] = $request->pause;
            }
            if($request->has('cancel_pause')) {
                $fields["pause"] = 0;
            }
            if(PublicRepo::updateInstantMatch($user, $fields)) {
                return redirect()->back()->withSuccessMessage("Instant match settings, saved.");
            } else {
                return redirect()->back()->withErrorMessage("Sorry, there was an error while saving setting, try again.");
            }
        } else {
            return redirect()->back()->withErrorMessage("You're not authorized to do this action!!");
        }

    }

}
