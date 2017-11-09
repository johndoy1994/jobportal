<?php

namespace App\Http\Controllers\RecruiterControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RecruiterControllers\RecruiterController;
use App\Http\Requests;
use App\Models\Job;
use App\Models\JobAddress;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Repos\EmployerRepo;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

class JobController extends RecruiterController
{

    public $modes = ['new','edit','repost','renew'];
    public $mode_titles = ['new'=>'Post new job','edit' => 'Update job details','repost' => 'Repost job','renew' => 'Renew job'];

    public function getIndex(Request $request, $mode, Job $job = null) {

        $recruiterUser = MyAuth::recruiter();

        if(!MyAuth::check('recruiter')) {
            return redirect()->route('recruiter-account-home')->withErrorMessage("You're not authorized to go there!!!");
        }

        if(!in_array($mode, $this->modes)) {
            return redirect()->route('recruiter-account-home')->withErrorMessage("Job operation not defined, try again.");
        }

        if(isset($job) && $job->exists) {
               
        } else {
            $job = null;
        }

        $page_title = $this->mode_titles[$mode];

        switch ($mode) {
            case 'edit':
            case 'repost':
            case 'renew':
                if(!isset($job)) {
                    return redirect()->route('recruiter-account-home')->withInput(Input::all())->withErrorMessage("Page not found");
                } else {
                    if($recruiterUser->employer && $recruiterUser->employer->id == $job->employer_id) {

                    } else {
                        return redirect()->route('recruiter-account-home')->withErrorMessage("You do not have rights to update job!!!");
                    }
                }
                break;
            
            default:
                
                break;
        }

        if($mode == "repost") {
            list($isRepostable) = $job->isRepostable();
            if(!$isRepostable) {
                return redirect()->route('recruiter-posted-jobs')->withErrorMessage('Cannot repost job now.');
            }
        }

        if($mode == "renew") {
            list($isRenewable) = $job->isRenewable();
            if(!$isRenewable) {
                return redirect()->route('recruiter-posted-jobs')->withErrorMessage('Cannot renewable job now.');
            }
        }

    	$education = PublicRepo::allEducations();
    	$experiences = PublicRepo::allExperiences();
    	$experienceLevels = PublicRepo::allExperienceLevels();
    	$jobTypes = PublicRepo::allJobTypes();
    	$payBies = PublicRepo::allPayBy();
    	$payPeriods = PublicRepo::allPayPeriod();
    	$salaryTypes = PublicRepo::allSalaryTypes();

    	$values = [
    		'job_title' => "",
    		'vacancies' => 1,
    		'countries' => null,
    		'states' 	=> null,
    		'cities'	=> null,
    		'street' => '',
    		'postal_code'	=> '',
    		'job_categories' => 0,
    		'job_titles' =>	0,
    		'requirements' => [],
    		'education' => 0,
    		'experiences' => 0,
    		'experience_levels' => 0,
    		'job_types' => 0,
    		'certificates' => '',
    		'dates' => [],
    		'starting_date' => '',
    		'ending_date' => '',
    		'weekdays' => [],
    		'schedule_from' => '',
    		'schedule_to' => '',
    		'pay_bies' => 0,
    		'pay_periods' => 0,
    		'salary_types' => 0,
            'salary' => 0,
    		'expiration_date' => '',
    		'benefits' => '',
    		'description' => '',

    		'salary_negotiable' => 0,
    		'no_ending_date' => 0
    	];

        if(isset($job)) {
            $values["job_title"] = $job->getTitle();
            $values["vacancies"] = $job->vacancies;
            if($job->getCountryId() > 0) {
                $country = PublicRepo::getCountry($job->getCountryId());
                if($country) {
                    $values["countries"] = [$country->id, $country->getName()];
                }
            }
            if($job->getStateId()>0) {
                $state = PublicRepo::getState($job->getStateId());
                if($state) {
                    $values["states"] = [$state->id, $state->getName()];
                }
            }
            if($job->getCityId()>0) {
                $city = PublicRepo::getCity($job->getCityId());
                if($city) {
                    $values["cities"] = [$city->id, $city->getName()];
                }
            }
            $values["street"] = $job->getStreet();
            $values["postal_code"] = $job->getPostalCode();
            if($job->getCategoryId()>0) {
                $jobCategory = PublicRepo::getJobCategory($job->getCategoryId());
                if($jobCategory) {
                    $values["job_categories"] = [$jobCategory->id, $jobCategory->getName()];
                }
            }
            if($job->getSubCategoryId()>0) {
                $jobTitle = PublicRepo::getJobTitle($job->getSubCategoryId());
                if($jobTitle) {
                    $values["job_titles"] = [$jobTitle->id, $jobTitle->getTitle()];
                    if(true) {
                        // requirements
                        $values["requirements"] = [];
                        $oldArray = $job->getTagIds();
                        foreach($jobTitle->tags as $tag) {
                            $selected = in_array("".$tag->id, $oldArray) ? true : false;
                            $values["requirements"][] = [$tag->id, $tag->getName(), $selected];
                        }
                    }
                }
            }
            $values["education"] = $job->education ? $job->education_id : 0;
            $values["experiences"] = $job->experience_id;
            $values["experience_levels"] = $job->experience_level ? $job->experience_level_id : 0;
            $values["job_types"] = $job->job_type_id;
            $values["certificates"] = $job->getCertificateLine();
            $jMeta = $job->jMeta();
            if(isset($jMeta["days"])) {
                $values["dates"] = $jMeta["days"];
            }
            $values["starting_date"] = $job->starting_date ? $job->starting_date->format('Y-m-d') : '';
            $values["ending_date"] = $job->ending_date ? $job->ending_date->format('Y-m-d') : '';
            $values["schedule_from"] = $job->work_schedule_from ? $job->work_schedule_from : '';
            $values["schedule_to"] = $job->work_schedule_to ? $job->work_schedule_to : '';
            $values["pay_bies"] = $job->pay_by_id;
            $values["pay_periods"] = $job->pay_period_id;
            $values["salary_types"] = $job->salary_type_id;
            if($job->salary == 0) {
                $values['salary_negotiable'] = 1;
            }
            $values["salary"] = $job->salary;
            $values['expiration_date'] = $job->expiration_date ? $job->expiration_date->format('Y-m-d') : '';
            $values['benefits'] = $job->benefits;
            $values['description'] = $job->description;
            if($job->ending_date) {
                
            } else {
                $values['no_ending_date'] = 1;
            }
            $values["weekdays"] = $job->getWeekDays();
            // $values['dates'] = old('dates');

            $values["show_calendar"] = false;
            if($job->job_type_id > 0) {
                $jobType = PublicRepo::getJobType($job->job_type_id);
                if($jobType && $jobType->day_selection == 1) {
                    $values["show_calendar"] = true;
                }
            }

            if($mode == "repost") {
                $values["starting_date"] = \Carbon\Carbon::now()->format('Y-m-d');
                $diff_starting_ending = 0;
                if($job->ending_date) {
                    $diff_starting_ending = Utility::diffInDates($job->starting_date->format('Y-m-d'),$job->ending_date->format('Y-m-d'));
                }
                $values["ending_date"] = $job->ending_date ? \Carbon\Carbon::now()->addDays($diff_starting_ending)->format('Y-m-d') : '';
                $diff_created_expiration = Utility::diffInDates($job->created_at->format('Y-m-d'), $job->expiration_date->format('Y-m-d'));
                $values['expiration_date'] = \Carbon\Carbon::now()->addDays($diff_created_expiration)->format('Y-m-d');
                $values['dates'] = [];
            }

        }

        if($request->old()) {
            $values["job_title"] = old('job_title');
            $values["vacancies"] = old('vacancies');
            if(old('countries')) {
                $country = PublicRepo::getCountry(old('countries'));
                if($country) {
                    $values["countries"] = [$country->id, $country->getName()];
                }
            }
            if(old('states')) {
                $state = PublicRepo::getState(old('states'));
                if($state) {
                    $values["states"] = [$state->id, $state->getName()];
                }
            }
            if(old('cities')) {
                $city = PublicRepo::getCity(old('cities'));
                if($city) {
                    $values["cities"] = [$city->id, $city->getName()];
                }
            }
            $values["street"] = old('street');
            $values["postal_code"] = old('postal_code');
            if(old('job_categories')) {
                $jobCategory = PublicRepo::getJobCategory(old('job_categories'));
                if($jobCategory) {
                    $values["job_categories"] = [$jobCategory->id, $jobCategory->getName()];
                }
            }
            if(old('job_titles')) {
                $jobTitle = PublicRepo::getJobTitle(old('job_titles'));
                if($jobTitle) {
                    $values["job_titles"] = [$jobTitle->id, $jobTitle->getTitle()];
                    if(old('requirements')) {
                        // requirements
                        $values["requirements"] = [];
                        $values["newRequirements"] = [];
                        foreach(old('requirements') as $value) {
                            if(!is_numeric($value)) {
                                $values["newRequirements"][] = $value;
                            }
                        }
                        $oldArray = old('requirements');
                        foreach($jobTitle->tags as $tag) {
                            $selected = in_array("".$tag->id, $oldArray) ? true : false;
                            $values["requirements"][] = [$tag->id, $tag->getName(), $selected];
                        }
                    }
                }
            }
            $values["education"] = old('education') ? old('education') : 0;
            $values["experiences"] = old('experiences') ? old('experiences') : 0;
            $values["experience_levels"] = old('experience_levels') ? old('experience_levels') : 0;
            $values["job_types"] = old('job_types') ? old('job_types') : 0;
            $values["certificates"] = old('certificates') ? old('certificates') : '';
            $values["dates"] = old('dates');
            $values["starting_date"] = old('starting_date') ? old('starting_date') : '';
            $values["ending_date"] = old('ending_date') ? old('ending_date') : '';
            $values["schedule_from"] = old('schedule_from') ? old('schedule_from') : '';
            $values["schedule_to"] = old('schedule_to') ? old('schedule_to') : '';
            $values["pay_bies"] = old('pay_bies') ? old('pay_bies') : 0;
            $values["pay_periods"] = old('pay_periods') ? old('pay_periods') : 0;
            $values["salary_types"] = old('salary_types') ? old('salary_types') : 0;
            if(old('salary_negotiable')) {
                $values['salary_negotiable'] = old('salary_negotiable');
            }
            $values["salary"] = old('salary');
            $values['expiration_date'] = old('expiration_date');
            $values['benefits'] = old('benefits');
            $values['description'] = old('description');
            if(old('no_ending_date')) {
                $values['no_ending_date'] = old('no_ending_date');
            }
            $values["weekdays"] = old('weekdays');
            $values['dates'] = old('dates');

            $values["show_calendar"] = false;
            if($values["job_types"] > 0) {
                $jobType = PublicRepo::getJobType($values['job_types']);
                if($jobType && $jobType->day_selection == 1) {
                    $values["show_calendar"] = true;
                }
            }
        }

    	return view('recruiter.account.manage-job',[
            '_mode' => $mode,
            '_jobId' => isset($job) ? $job->id : null,
            'page_title' => $page_title,

			'user' => MyAuth::user('recruiter'),
			'educations' => $education,
			'experiences' => $experiences,
			'experienceLevels' => $experienceLevels,
			'jobTypes' => $jobTypes,
			'payBies' => $payBies,
			'payPeriods' => $payPeriods,
			"salaryTypes" => $salaryTypes,

			'values' => $values
		]);
    }

    public function postJob(Request $request, $mode, Job $job = null) {

        $recruiterUser = MyAuth::recruiter();

        if(!MyAuth::check('recruiter')) {
            return redirect()->route('recruiter-account-home')->withErrorMessage("You're not authorized to go there!!!");
        }

        if(!in_array($mode, $this->modes)) {
            return redirect()->route('recruiter-account-home')->withErrorMessage("Job operation not defined, try again.");
        }

        if(isset($job) && $job->exists) {
               
        } else {
            $job = null;
        }

        $page_title = $this->mode_titles[$mode];

        switch ($mode) {
            case 'edit':
            case 'repost':
            case 'renew':
                if(!isset($job)) {
                    return redirect()->route('recruiter-account-home')->withInput(Input::all())->withErrorMessage("Page not found");
                } else {
                    if($recruiterUser->employer && $recruiterUser->employer->id == $job->employer_id) {

                    } else {
                        return redirect()->route('recruiter-account-home')->withErrorMessage("You do not have rights to update job!!!");
                    }
                }
                break;
            
            default:
                
                break;
        }

        if($mode == "repost") {
            list($isRepostable) = $job->isRepostable();
            if(!$isRepostable) {
                return redirect()->route('recruiter-posted-jobs')->withErrorMessage('Cannot repost job now.');
            }
        }

        if($mode == "renew") {
            list($isRenewable) = $job->isRenewable();
            if(!$isRenewable) {
                return redirect()->route('recruiter-posted-jobs')->withErrorMessage('Cannot renewable job now.');
            }
        }

        $rules=[
    		'job_title' => "required|unique:jobs,title",
    		'vacancies' => "required|integer|not_in:0",
    		'countries' => "required|findId",
    		'states' 	=> 'required|findId',
    		'cities'	=> 'required|findId',
    		//'street'    => 'required',
    		'postal_code'	=> 'postalcode',
    		'job_categories' => 'required|findId',
    		'job_titles' =>	'required|findId',
    		//requirements
    		'education' => 'required|findId',
    		'experiences' => 'required|findId',
    		'experience_levels' => 'required|findId',
    		'job_types' => 'required|findId',
    		//certificates
    		//dates
    		'starting_date' => 'required|date_format:"Y-m-d"',
    		'ending_date' => 'required_if:no_ending_date,""|date_format:"Y-m-d"',
    		//weekdays
    		'schedule_from' => 'required|date_format:"H:i:s"',
    		'schedule_to' => 'required|date_format:"H:i:s"',
    		'pay_bies' => 'required|findId',
    		'pay_periods' => 'required|findId',
    		'salary_types' => 'required|findId',
            'salary' => 'required_if:salary_negotiable,0|regex:/^\d*(\.\d*)?$/',
    		'expiration_date' => 'required|date_format:"Y-m-d"',
    		//'benefits' => 'required',
    		'description' => 'required'
		];
		if($mode!='new'){
            $rules['job_title'] = [
                'required',
                'unique:jobs,title,'.$job->id
            ];
        }

        $this->validate($request, $rules);

        $requirements = $request->requirements;
        $certificates = $request->certificates;
        $dates = $request->dates;
        $weekdays = $request->weekdays;

        if($request->has("job_types")) {
            $jobType = PublicRepo::getJobType($request->job_types);
            if($jobType) {
                if($jobType->day_selection == 1) {
                    if(!$request->has('dates') || ($request->has('dates') && (count($request->dates) == 0 || !Job::validPartDays($request->dates,"Y-m-d", $job))) ) {
                        return redirect()->back()->withInput(Input::all())->with([
                            'error_message' => "Sorry you must select valid days from job calendar for part time jobs (you cannot select past dates)."
                        ]);
                    }
                } else {
                    $dates=[];
                }
            }
        }

        $starting_date = $request->starting_date;
        $ending_date = $request->ending_date;

        $validate_starting_date = true;
        $validate_expiration_date = true;

        if($mode == "edit" || $mode == "renew") {
            if($request->starting_date == $job->starting_date->format('Y-m-d')) {
                $validate_starting_date=false;
            }
            if($request->expiration_date == $job->expiration_date->format('Y-m-d')) {
                $validate_expiration_date = false;
            }
        }

        if($starting_date = Utility::parseCarbonDate($request->starting_date, "Y-m-d")) {
            if($validate_starting_date) {
                if(!Utility::date_greaterThanToday($starting_date,"Y-m-d",true)) {
                    return redirect()->back()->withInput(Input::all())->withErrorMessage("Sorry, you cannot select past days as starting date, try again.");
                }
            }
        } else {
            return redirect()->back()->withInput(Input::all())->withErrorMessage("Selected starting date is invalid, please try again.");
        }

        if(!$request->has('no_ending_date')) {
            if($ending_date = Utility::parseCarbonDate($request->ending_date, "Y-m-d")) {
                if($ending_date < $starting_date) {
                    return redirect()->back()->withInput(Input::all())->withErrorMessage("Sorry, you cannot select ending date before starting date, try again.");
                }
            } else {
                return redirect()->back()->withInput(Input::all())->withErrorMessage("Selected ending date is invalid, please try again.");
            }
        }

        $schedule_from = $request->schedule_from;
        $schedule_to = $request->schedule_to;

        if(($schedule_from = Utility::parseCarbonDate($request->schedule_from, "H:i:s")) &&
           ($schedule_to = Utility::parseCarbonDate($request->schedule_to, "H:i:s"))) {
            if($schedule_to < $schedule_from) {
                return redirect()->back()->withInput(Input::all())->withErrorMessage("Invalid schedule from to must be greater than schedule from, please try again.");
            }
        } else {
            return redirect()->back()->withInput(Input::all())->withErrorMessage("Invalid schedule entry, please try again.");
        }

        $expiration_date = $request->expiration_date;

        if($expiration_date = Utility::parseCarbonDate($request->expiration_date, "Y-m-d")) {
            if($validate_expiration_date) {
                if(!Utility::date_greaterThanToday($expiration_date,"Y-m-d",true)) {
                    return redirect()->back()->withInput(Input::all())->withErrorMessage("Sorry, you cannot select past days as expiration date, try again.");
                }
            }
        } else {
            return redirect()->back()->withInput(Input::all())->withErrorMessage("Invalid expiration date, please try again.");
        }

        /// Process ///////////////////

        if($mode) {
            if(in_array($mode, $this->modes)) {
                $job_id = isset($job) ? $job->id : 0;
                $employer_id = $recruiterUser->employer->id;
                $meta = isset($job) ? $job->jMeta() : [];
                switch ($mode) {
                    case 'new':
                    case 'edit':
                    case 'repost':
                    case 'renew':
                        if(count($dates)>0) {
                            $meta["days"] = $dates;
                        } else {
                            $meta["days"] = [];
                        }
                        break;
                    
                    default:
                        return redirect()->back()->withInput(Input::all())->withErrorMessage("System error, please try again.");
                        break;
                }

                $updateRenewDate = $mode == "new" || $mode == "renew";
                if($mode == "repost") {
                    $job_id = 0;
                }

                $salary = 0;

                if($request->has('salary') && !$request->has('salary_negotiable')) {
                    $salary = $request->salary;
                }

                list($jobSaved,$jobOpMessage,$jobId) = EmployerRepo::saveJob($job_id, $employer_id, $request->job_title, $request->vacancies, $request->cities, $request->street, $request->postal_code, $request->job_titles, $requirements, $request->education, $request->experiences, $request->experience_levels, $request->job_types, $certificates, $starting_date, $ending_date, $weekdays, $request->schedule_from, $request->schedule_to, $request->pay_bies, $request->pay_periods, $request->salary_types, $salary, $expiration_date, $request->benefits, $request->description, $meta, $updateRenewDate);

                if($jobSaved) {
                    if($mode == "repost") {
                        $job->delete();
                        return redirect()->route('recruiter-job', ['mode'=> "edit", 'job'=> $jobId ])->withSuccessMessage("Job successfully reposted.");
                    }
                    return redirect()->back()->withSuccessMessage("Job successfully saved.");
                } else {
                    return redirect()->back()->withInput(Input::all())->withErrorMessage("There was an error while saving job, please try again.");
                }

            } else {
                return redirect()->back()->withInput(Input::all())->withErrorMessage("Defined job operation is not valid, try again.");
            }
        } else {
            return redirect()->back()->withInput(Input::all())->withErrorMessage("Invalid job operation, try again.");
        }

        /// Process End ///////////////

    	return redirect()->back()->withInput(Input::all());

    }

}
