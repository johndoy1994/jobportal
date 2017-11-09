<?php

namespace App\Http\Controllers\BackendControllers;

use App\Helpers\Notifier;
use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Job;
use App\Models\JobType;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Repos\AdminRepo;
use App\Repos\JobRepo;
use App\Utility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class JobController extends BackendController
{
    public function getListing(Request $request)
    {

        $currentDate = \Carbon\Carbon::now()->format("Y-m-d");


        $q = Job::select(
                "jobs.*",
                DB::raw('employers.company_name'),
                DB::raw('job_types.name as job_type__name'),
                DB::raw('job_types.id as job_type_id'),
                DB::raw('job_titles.job_category_id'),
                DB::raw('job_addresses.city_id'),
                DB::raw('job_categories.name as category_name'),
                DB::raw('job_categories.id as category_id'),
                DB::raw('job_addresses.postal_code'),
                DB::raw('cities.name as cityname'),
                DB::raw('states.name as statename'),
                DB::raw('countries.name as countryname'),
                DB::raw('count(job_applications.id) as application_count'),
                DB::raw('datediff(jobs.expiration_date, "'.$currentDate.'") as expired_days')
            );
        $q->join('employers', 'employers.id', '=', 'jobs.employer_id');
        $q->join('job_types','job_types.id', "=", "jobs.job_type_id");
        $q->join('job_titles','job_titles.id', "=", "jobs.job_title_id");
        $q->join('job_addresses','job_addresses.job_id', "=", "jobs.id");
        $q->join('job_categories','job_categories.id', "=", "job_titles.job_category_id");
        $q->join('cities','cities.id', "=", "job_addresses.city_id");
        $q->join('states','states.id', "=", "cities.state_id");
        $q->join('countries','countries.id', "=", "states.country_id");
        $q->leftJoin("job_applications", "jobs.id", '=', 'job_applications.job_id');

    	if($request->has("search")) {   
        	$search=$request["search"];
            $q->where(function($q) use($search) {
                $q->orWhere('jobs.title','like','%'.$search.'%');
                $q->orWhere('employers.company_name','like','%'.$search.'%');
                $q->orWhere('job_types.name','like','%'.$search.'%');
                $q->orWhere('job_categories.name','like','%'.$search.'%');
                $q->orWhere('job_addresses.street','like','%'.$search.'%');
                $q->orWhere('job_addresses.postal_code','like','%'.$search.'%');
                $q->orWhere('cities.name','like','%'.$search.'%');
                $q->orWhere('states.name','like','%'.$search.'%');
                $q->orWhere('countries.name','like','%'.$search.'%');
            });
        }

        if($request->has("date")) { 
            $fromDate = \Carbon\Carbon::createFromFormat("d-m-Y H:i:s", Carbon::now()->format("1-m-Y 00:00:00"));
            $toDate = $fromDate->copy()->addMonths(1);

            try {
                $date = \Carbon\Carbon::createFromFormat("F Y", $request->date);
                $fromDate = \Carbon\Carbon::createFromFormat("d-m-Y H:i:s", $date->format("1-m-Y 00:00:00"));
                $toDate = $fromDate->copy()->addMonths(1);
            } catch(\Exception $e) {}

            $q->where(function($q) use($fromDate, $toDate) {
                $q->whereBetween('jobs.created_at', [$fromDate, $toDate]);
            });
        }

        if($request->has("jobTypeId")) {   
            $search=$request["jobTypeId"];

            $q->where(function($q) use($search) {
                $q->orWhere('job_types.id','like','%'.$search.'%');
            });
        }

        if($request->has("employeeId")) {   
            $search=$request["employeeId"];

            $q->where(function($q) use($search) {
                $q->orWhere('employers.id','like','%'.$search.'%');
            });
        }

        if($request->has("jobCategoryId")) {   
            $search=$request["jobCategoryId"];

            $q->where(function($q) use($search) {
                $q->orWhere('job_categories.id','like','%'.$search.'%');
            });
        }

        if($request->has("jobCityId")) {   
            $search=$request["jobCityId"];

            $q->where(function($q) use($search) {
                $q->orWhere('cities.id','like','%'.$search.'%');
            });
        }


        if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            $q->orderBy('jobs.renew_date','jons.title');
        }

        $q->groupBy("jobs.id");

        $recordsPerPage = $this->recordsPerPage("job-listing");

        $Jobs = $q->paginate($recordsPerPage);
    	
        $page = $request->has('page') ? $request->page : 1;

        $jobTypeId = $request->has("jobTypeId") ? $request->jobTypeId : "";
        $jobCategoryId = $request->has("jobCategoryId") ? $request->jobCategoryId : "";
        $jobCityId = $request->has("jobCityId") ? $request->jobCityId : "";

        $columns = ['title', 'company_name', 'job_type__name','expired_days','application_count'];

        $sort_columns = [];
        foreach ($columns as $column) {
            $sort_columns[$column]["params"] = [
                'page' => $page,
                'sortBy' => $column,
                'jobTypeId' => $jobTypeId,
                'jobCityId' => $jobCityId,
                'jobCategoryId' => $jobCategoryId,
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
        $isRequestSearch=$request->has('search');
      	return view('backend.job.listing', [
    		"Jobs" => $Jobs,
            'sort_columns' => $sort_columns,
            'isRequestSearch'=>$isRequestSearch
		]);

    }

    public function postListing(Request $request)
    {
        $action = $request->submit;

        switch($action) {
            case "Apply":
                switch($request["bulkid"]) {
                    case "deleted":
                        if(count($request["jobmultiple"]) > 0) {
                            foreach ($request["jobmultiple"] as $value) {
                                $val = Job::find($value);
                                $val->delete();
                            }
                            //Job::whereIn('id', $request["jobmultiple"])->delete();
                            return redirect()->back()->with([
                                'success_message' => "Deleted successfully"
                            ]);
                        } else {
                            return redirect()->back()->with([
                                'error_message' => "No items selected to delete!!"
                            ]);
                        }
                    break;

                    case "active":
                        if(count($request["jobmultiple"]) > 0) {
                            $jobs = Job::whereIn('id', $request["jobmultiple"])->update([
                                'status'=>'active'
                            ]);
                            return redirect()->back()->with([
                                    'success_message' => "Active successfully"
                                ]);
                        } else {
                            return redirect()->back()->with([
                                'error_message' => "No items selected to active!!"
                            ]);
                        }
                    break;                    

                    case "inactive":
                        if(count($request["jobmultiple"]) > 0) {
                            $jobs = Job::whereIn('id', $request["jobmultiple"])->update([
                                    'status'=>'inactive'    
                                ]);
                            return redirect()->back()->with([
                                        'success_message' => "Inactive successfully"
                                    ]);
                        } else {
                            return redirect()->back()->with([
                                'error_message' => "No items selected to inactive!!"
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
                return redirect()->route('admin-job', ['search'=>$request->filter])->withInput(Input::all());
            break;

            case "SearchDateVish":
                return redirect()->route('admin-job', ['date'=>$request->date])->withInput(Input::all());
            break;
        }
    }

    //add New Job
    Public function getNewJob(Request $request){
    	
    	$employers = PublicRepo::allEmployers();
    	$Categories = PublicRepo::allJobCategories();
    	$jobTitles = PublicRepo::allJobTitles();
    	$educations = PublicRepo::allEducations();
    	$experiences = PublicRepo::allExperiences();
    	$jobTypes = PublicRepo::allJobTypes();
    	$experienceLevels = PublicRepo::allExperienceLevels();
    	$salaryTypes = PublicRepo::allSalaryTypes();
    	$payBys = PublicRepo::allPayBy();
    	$payPeriods = PublicRepo::allPayPeriod();
    	$Countries = PublicRepo::allCountries(0);
    	$Cities = PublicRepo::allCities();

        $oldJobTitle = null;
        $oldstateTitle = null;
        $oldCityTitle = null;
        $TagsAllskill=null;
        $jobskills =null;
        $salary_range_id=null;
        $showCalendar = false;

        $oldDates = [];
        if(old()) {
            if(old("job_title_id")) {
                $jobTitleId = old("job_title_id");

                $jobTitle = PublicRepo::getJobTitle($jobTitleId);
                $TagsAllskill = PublicRepo::skillsOf($jobTitleId);
                if($jobTitle) {
                    $oldJobTitle = [$jobTitle->id, $jobTitle->getTitle()];
                }
            }

            if(old("state_id")) {
                $stateId = old("state_id");
                $stateTitle = PublicRepo::getState($stateId);
                if($stateTitle) {
                    $oldstateTitle = [$stateTitle->id, $stateTitle->getName()];
                }
            }

            if(old("city_id")) {
                $cityId = old("city_id");
                $cityTitle = PublicRepo::getCity($cityId);
                if($cityTitle) {
                    $oldCityTitle = [$cityTitle->id, $cityTitle->getName()];
                }
            }
            
            if(old("skills")) {
                $jobskills = old("skills");
            }

            // if(old("salary_range_id")) {
            //     $rangeId = old("salary_range_id");
            //     $range = PublicRepo::salaryRangeOf($rangeId);
               
            //     if($range[0]) {
            //         $range=$range[0];
            //         $salary_range_id = [$range->id, $range->range_from.'-'.$range->range_to];
            //     }
            // }

            if(old("salary_range_id")) {
                $rangeId = old("salary_range_id");
                $range = PublicRepo::getSalaryRange($rangeId);
               
                if($range) {
                    //$range=$range[0];
                    $salary_range_id = [$range->id, $range->range_from.'-'.$range->range_to];
                }
            }

            if(old('dates')) {
                $oldDates = old('dates');
            }
        }
//echo'<pre>';print_r($TagsAllskill);echo "</br>";
//print_r($jobskills);exit;
        return view('backend.job.new-job',[
        	'employers'=>$employers,
        	'Categories'=>$Categories,
        	'educations'=>$educations,
        	'experiences'=>$experiences,
        	'jobTypes'=>$jobTypes,
        	'experienceLevels'=>$experienceLevels,
        	'salaryTypes'=>$salaryTypes,
        	'payBys'=>$payBys,
        	'payPeriods'=>$payPeriods,
        	'Countries'=>$Countries,
        	'Cities'=>$Cities,

            // old inputs
            'oldJobTitle' => $oldJobTitle,
            'oldstateTitle'=>$oldstateTitle,
            'oldCityTitle'=>$oldCityTitle,
            'jobskills'=>$jobskills,
            'TagsAllskill'=>$TagsAllskill,
            'oldsalary_range_id'=>$salary_range_id,
            'oldDates' => $oldDates

    	]);
    }

    Public function postNewJob(Request $request){
    	$this->validate($request, [
    		'employer_id'=> 'required|findId:employers',
            'title' => 'required',
            'vacancies' => 'required|integer',
            'job_category_id'=> 'required|findId:job_categories',
            'job_title_id' => 'required|findId:job_titles,,job_category_id,'.$request->job_category_id,
            'education_id' => 'required|findId:education',
            'experience_id'=> 'required|findId:experiences',
            'experience_level_id' => 'required|findId:experience_levels',
            'job_type_id' => 'required|findId:job_types',
            'country_id'=> 'required|findId:countries',
            'state_id' => 'required|findId:states',
            'city_id' => 'required|findId:cities,,state_id,'.$request->state_id,
            //'street'=> 'required',
            'postal_code' => 'postalcode',
            //'certificates' => 'required',
            'keyword' => 'required',
            'starting_date'=> 'required|date_format:"Y-m-d"',
            'ending_date' => 'date_format:"Y-m-d"',
            'expiration_date' => 'required|date_format:"Y-m-d"',
            'work_schedule_from' => 'required|date_format:"H:i:s"',
            'work_schedule_to'=> 'required|date_format:"H:i:s"',
            'salary_type_id' => 'required|findId:salary_types',
            'salary' => 'required_if:salary_check,1|regex:/^\d*(\.\d*)?$/',
            'pay_by_id'=> 'required|findId:pay_bies',
            'pay_period_id' => 'required|findId:pay_periods',
            //'salary_range_id'=>'required|findId:salary_ranges',
            //'benefits' => 'required',
            'description' => 'required',
    	],[
    		'employer_id.required'=> 'Please select employer.',
            'title.required' => 'title enter title.',
            'vacancies.required' => 'Please enter vacancies.',
            'job_category_id.required'=> 'Please select job category.',
            'job_title_id.required' => 'Please select job title.',
            'education_id.required' => 'Please select education.',
            'experience_id.required.'=> 'Please select experience.',
            'experience_level_id.required' => 'Please select experience level.',
            'job_type_id.required' => 'Please select job type.',
            'country_id.required'=> 'Please select Country .',
            'state_id.required' =>'Please select state.',
            'city_id.required' => 'Please select city.',
            //'street.required'=> 'Please enter street.',
            'postal_code.required' => 'Please enter postal code.',
            //'certificates.required' =>'Please enter certificates.',
            'keyword.required' =>'Please enter job keyword.',
            'starting_date.required'=>'Please select starting date.',
            //'ending_date.required' => 'Please select ending date.',
            'work_schedule_from.required' =>'Please select work schedule from.',
            'work_schedule_to.required'=>'Please select Country name.',
            'salary_type_id.required' =>'Please select Country name.',
            //'salary.required' =>'Please enter salary.',
            'pay_by_id.required'=>'Please select payBy.',
            'pay_period_id.required' =>'Please select pay period.',
            //'benefits.required' =>'Please enter benefits.',
            //'description.required' =>'Please select description.',
    	]);
    	// list($status,$record)= PublicRepo::getsalaryof($request->salary,$request->salary_range_id);
     //    if($status){
            // if($request->ending_date){
            //     $starting_date = Carbon::createFromFormat("Y-m-d", $request->starting_date); 
            //     $ending_date   = Carbon::createFromFormat("Y-m-d", $request->ending_date);
            //     $days=$starting_date->diffInDays($ending_date,false);
            // }else{
            //     $days=0;
            // }
            list($JobIsUnic[0],$JobIsUnicMessage[0])=PublicRepo::IsUnicJob($request->employer_id,$request->title,$request->city_id,$request->starting_date);
            if(!$JobIsUnic[0]){
                return redirect()->back()->withInput(Input::all())->with([
                    'error_message' => $JobIsUnicMessage[0]
                ]);
            }
            list($dateStatus[0],$dateMessage[0])=AdminRepo::jobDateValidation($request->starting_date,$request->ending_date);
            list($dateStatus[1],$dateMessage[1])=AdminRepo::jobExpireDateValidation($request->expiration_date);
            if(($work_schedule_from = Utility::parseCarbonDate($request->work_schedule_from, "H:i:s")) &&
               ($work_schedule_to = Utility::parseCarbonDate($request->work_schedule_to, "H:i:s"))) {
                if($work_schedule_to < $work_schedule_from) {
                    return redirect()->back()->withInput(Input::all())->withErrorMessage("Invalid schedule from to must be greater than schedule from, please try again.");
                }
            } else {
                return redirect()->back()->withInput(Input::all())->withErrorMessage("Invalid schedule entry, please try again.");
            }
            if($dateStatus[0] && $dateStatus[1]){

                if($request->has("job_type_id")) {
                    $jobType = PublicRepo::getJobType($request->job_type_id);
                    if($jobType) {
                        if($jobType->day_selection == 1) {
                            // New logic for past day validation
                            if(!$request->has('dates') || ($request->has('dates') && (count($request->dates) == 0 || !Job::validPartDays($request->dates,"Y-m-d"))) ) {
                                return redirect()->back()->withInput(Input::all())->with([
                                    'error_message' => "Sorry you must select valid days from job calendar for part time jobs (you cannot select past dates)."
                                ]);
                            }

                            /// Old logic for past day validation
                            // if(!$request->has('dates') || ($request->has('dates') && (count($request->dates) == 0 || !Utility::validateDatesInArray($request->dates,"Y-m-d","date_greaterThanToday"))) ) {
                            //     return redirect()->back()->withInput(Input::all())->with([
                            //         'error_message' => "Sorry you must select valid days from job calendar for part time jobs (you cannot select past dates)."
                            //     ]);
                            // }
                        }
                    }
                }

            	list($status,$message,$jobrec) = AdminRepo::addJob($request->all());
            	if($status){
            		$certificate_line = $request->has('certificates') ? $request->certificates : "";
                	$certificates = explode(",", $certificate_line);

                	// save certificates
                    $certificates_saved = AdminRepo::addJobCertificates($jobrec, $certificates);

                    $city = PublicRepo::searchLocations(null, null, $request->city_id);
                    $searchAddress = $city[0]->full_address." - ".$request->postal_code;
                    list($pointSuccess, $point, $correctedAddress) = PublicRepo::getGeoLocationPoint($searchAddress);
                	// save Address
                	list($jobstatus[0],$jobmessage[0]) = AdminRepo::addJobAddress($jobrec, array(
        	            'city_id'       => $request->city_id,
        	            'postal_code'   => $request->postal_code,
        	            'street'        => $request->street,
                        'latitude'      => $point[0],
                        'longitude'      => $point[1],
                	));

                	// save Skills
                	$skills = $request->has('skills') ? $request->skills : [];
                	$skills_saved = AdminRepo::addSkills($jobrec, $skills);

                	// save Keyword
                	$keyword_line = $request->has('keyword') ? $request->keyword : [];
                	$keyword = explode(",", $keyword_line);
                	list($jobstatus[1],$jobmessage[1]) = AdminRepo::addkeyword($jobrec, $keyword);

                	// save weekly
                	$weekly = $request->has('weekly') ? $request->weekly : [];
                	$weekly_saved = AdminRepo::addweekly($jobrec, $weekly);
                    if($jobstatus[0] && $jobstatus[1]){
                        Notifier::jobPosted($jobrec,MyAuth::user('admin'));
                    	return redirect()->back()->with([
                        	'success_message' => "New job successfully added!"
                    	]);
                    }else{
                        $jobrec->delete();
                        return redirect()->back()->with([
                            'error_message' => "There was an error while adding your keyword and Address, try again"
                        ]);
                    }
            	}else{
                    return redirect()->back()->withInput(Input::all())->with([
                    	'error_message' => "There was an error while adding your job, try again"
                	]);
            	}
            }else{
                $error_message = "";

                if(!$dateStatus[0]) {
                    $error_message .= $dateMessage[0]."<br/>";
                }

                if(!$dateStatus[1]) {
                    $error_message .= $dateMessage[1]."<br/>";
                }

                return redirect()->back()->withInput(Input::all())->with([
                        'error_message' => $error_message
                ]);
            }
        // }else{
        //     return redirect()->back()->withInput(Input::all())->with([
        //             'error_message' => "please enter salary between salary range"
        //         ]);
        // }

    }

    //add New Job
    Public function getEditJob(Request $request, Job $Job){
    	
    	$jobWeekdays = PublicRepo::jobWeekdayOf($Job->id);
    	$jobCertificates = PublicRepo::jobCertificateOf($Job->id);
    	$certificates = "";
    	foreach($jobCertificates as $jobCertificate) {
    		if(!empty($jobCertificate)){
    			$certificates .= $jobCertificate->certificate.", ";
    		}
    	}
    	$certificates= rtrim($certificates,', ');
    	
    	$jobKeywords = PublicRepo::jobKeywordsOf($Job->id);
    	$Keywords = "";
    	foreach($jobKeywords as $jobKeyword) {
    		if(!empty($jobKeyword)){
    			$Keywords .= $jobKeyword->keyword.", ";
    		}
    	}
    	$Keywords= rtrim($Keywords,', ');
    	$TagsAllskill = PublicRepo::skillsOf($Job->job_title_id);

    	$jobskills = $Job->skills;
    	$employers = PublicRepo::allEmployers();
    	$Categories = PublicRepo::allJobCategories();
    	$jobTitles = PublicRepo::allJobTitles();
    	$educations = PublicRepo::allEducations();
    	$experiences = PublicRepo::allExperiences();
    	$jobTypes = PublicRepo::allJobTypes();
    	$experienceLevels = PublicRepo::allExperienceLevels();
    	$salaryTypes = PublicRepo::allSalaryTypes();
    	$payBys = PublicRepo::allPayBy();
    	$payPeriods = PublicRepo::allPayPeriod();
    	$Countries = PublicRepo::allCountries(0);
    	$Cities = PublicRepo::allCities();

        $oldDates = [];
        $jMeta = $Job->jMeta();

        if(isset($jMeta["days"])) {
            $oldDates = $jMeta["days"];
        }

        return view('backend.job.edit-job',[
        	'employers'=>$employers,
        	'Categories'=>$Categories,
        	'educations'=>$educations,
        	'experiences'=>$experiences,
        	'jobTypes'=>$jobTypes,
        	'experienceLevels'=>$experienceLevels,
        	'salaryTypes'=>$salaryTypes,
        	'payBys'=>$payBys,
        	'payPeriods'=>$payPeriods,
        	'Countries'=>$Countries,
        	'Cities'=>$Cities,
        	'Jobs'=>$Job,
        	'certificates'=>$certificates,
        	'Keywords'=>$Keywords,
        	'jobWeekdays'=>$jobWeekdays,
        	'jobskills'=>$jobskills,
        	'TagsAllskill'=>$TagsAllskill,
            'oldDates'=>$oldDates

        	]);
    }

    public function postEditJob(Request $request, Job $Job){
    	$this->validate($request, [
    		'employer_id'=> 'required|findId:employers',
            'title' => 'required',
            'vacancies' => 'required|integer',
            'job_category_id'=> 'required|findId:job_categories',
            'job_title_id' => 'required|findId:job_titles,,job_category_id,'.$request->job_category_id,
            'education_id' => 'required|findId:education',
            'experience_id'=> 'required|findId:experiences',
            'experience_level_id' => 'required|findId:experience_levels',
            'job_type_id' => 'required|findId:job_types',
            'country_id'=> 'required|findId:countries',
            'state_id' => 'required|findId:states',
            'city_id' => 'required|findId:cities,,state_id,'.$request->state_id,
            //'street'=> 'required',
            'postal_code' => 'postalcode',
            //'certificates' => 'required',
            'keyword' => 'required',
            'starting_date'=> 'required|date_format:"Y-m-d"',
            'ending_date' => 'date_format:"Y-m-d"',
            'expiration_date' => 'required|date_format:"Y-m-d"',
            'work_schedule_from' => 'required|date_format:"H:i:s"',
            'work_schedule_to'=> 'required|date_format:"H:i:s"',
            'salary_type_id' => 'required|findId:salary_types',
            'salary' => 'required_if:salary_check,1|regex:/^\d*(\.\d*)?$/',
            'pay_by_id'=> 'required|findId:pay_bies',
            'pay_period_id' => 'required|findId:pay_periods',
            //'salary_range_id'=>'required|findId:salary_ranges',
            //'benefits' => 'required',
            'description' => 'required',
    	],[
    		'employer_id.required'=> 'Please select employer.',
            'title.required' => 'title enter title.',
            'vacancies.required' => 'Please enter vacancies.',
            'job_category_id.required'=> 'Please select job category.',
            'job_title_id.required' => 'Please select job title.',
            'education_id.required' => 'Please select education.',
            'experience_id.required.'=> 'Please select experience.',
            'experience_level_id.required' => 'Please select experience level.',
            'job_type_id.required' => 'Please select job type.',
            'country_id.required'=> 'Please select Country .',
            'state_id.required' =>'Please select state.',
            'city_id.required' => 'Please select city.',
            //'street.required'=> 'Please enter street.',
            'postal_code.required' => 'Please enter postal code.',
            //'certificates.required' =>'Please enter certificates.',
            'keyword.required' =>'Please enter job keyword.',
            'starting_date.required'=>'Please select starting date.',
            //'ending_date.required' => 'Please select ending date.',
            'work_schedule_from.required' =>'Please select work schedule from.',
            'work_schedule_to.required'=>'Please select Country name.',
            'salary_type_id.required' =>'Please select Country name.',
            //'salary.required' =>'Please enter salary.',
            'pay_by_id.required'=>'Please select payBy.',
            'pay_period_id.required' =>'Please select pay period.',
            //'benefits.required' =>'Please enter benefits.',
            //'description.required' =>'Please select description.',
    	]);
        // list($status,$record)= PublicRepo::getsalaryof($request->salary,$request->salary_range_id);
        // if($status){

            // if($request->ending_date){
            //     $starting_date = Carbon::createFromFormat("Y-m-d", $request->starting_date); 
            //     $ending_date   = Carbon::createFromFormat("Y-m-d", $request->ending_date);
            //     $days=$starting_date->diffInDays($ending_date,false);
            // }else{
            //     $days=0;
            // }
            list($JobIsUnic[0],$JobIsUnicMessage[0])=PublicRepo::IsUnicJob($request->employer_id,$request->title,$request->city_id,$request->starting_date,$Job->id);
            if(!$JobIsUnic[0]){
                return redirect()->back()->withInput(Input::all())->with([
                    'error_message' => $JobIsUnicMessage[0]
                ]);
            }
            $strdays="";
            $expdays="";
            if($startDate = Utility::parseCarbonDate($request->starting_date,"Y-m-d")){
                $strdays=Utility::diffInDates($startDate->format("Y-m-d"),$Job->starting_date->format("Y-m-d"));
            }
                
            if($expdt = Utility::parseCarbonDate($request->expiration_date,"Y-m-d")){
                $expdays=Utility::diffInDates($expdt->format("Y-m-d"),$Job->expiration_date->format("Y-m-d"));
            }

            // if($strdays==0){
            //     list($dateStatus[0],$dateMessage[0])=[true, "true",null];
            // }else{
            //     list($dateStatus[0],$dateMessage[0])=AdminRepo::jobDateValidation($request->starting_date,$request->ending_date);
            // }

            if($strdays != 0) {
                list($dateStatus[0],$dateMessage[0])=AdminRepo::jobDateValidation($request->starting_date,$request->ending_date, true);
            } else {
                list($dateStatus[0],$dateMessage[0])=AdminRepo::jobDateValidation($request->starting_date,$request->ending_date, false);
            }

            if($expdays==0){
                list($dateStatus[1],$dateMessage[1])=[true, "true",null];
            }else{
                list($dateStatus[1],$dateMessage[1])=AdminRepo::jobExpireDateValidation($request->expiration_date);
            }
            if(($work_schedule_from = Utility::parseCarbonDate($request->work_schedule_from, "H:i:s")) &&
               ($work_schedule_to = Utility::parseCarbonDate($request->work_schedule_to, "H:i:s"))) {
                if($work_schedule_to < $work_schedule_from) {
                    return redirect()->back()->withInput(Input::all())->withErrorMessage("Invalid schedule from to must be greater than schedule from, please try again.");
                }
            } else {
                return redirect()->back()->withInput(Input::all())->withErrorMessage("Invalid schedule entry, please try again.");
            }
            if($dateStatus[0] && $dateStatus[1]){
            
                if($request->has("job_type_id")) {
                    $jobType = PublicRepo::getJobType($request->job_type_id);
                    if($jobType) {
                        if($jobType->day_selection == 1) {
                            /// New logic which ignore already setted dates for validation
                            if(!$request->has('dates') || ($request->has('dates') && (count($request->dates) == 0 || !$Job->isValidPartDays($request->dates,'Y-m-d'))) ) {
                                return redirect()->back()->withInput(Input::all())->with([
                                    'error_message' => "Sorry you must select valid days from job calendar for part time jobs (you cannot select past dates)."
                                ]);
                            }

                            /// Old Logic for Editing Job
                            // if(!$request->has('dates') || ($request->has('dates') && (count($request->dates) == 0 || !Utility::validateDatesInArray($request->dates,"Y-m-d","date_greaterThanToday"))) ) {
                            //     return redirect()->back()->withInput(Input::all())->with([
                            //         'error_message' => "Sorry you must select valid days from job calendar for part time jobs (you cannot select past dates)."
                            //     ]);
                            // }
                        }
                    }
                }
                
            	list($status,$message,$jobrec) = AdminRepo::updateJob($Job->id,$request->all(),$Job->renew_date);
            	if($status){
            		$certificate_line = $request->has('certificates') ? $request->certificates : "";
                	$certificates = explode(",", $certificate_line);

                    // save certificates
                	$certificates_saved = AdminRepo::updateJobCertificates($Job, $certificates);

                    $city = PublicRepo::searchLocations(null, null, $request->city_id);
                    $searchAddress = $city[0]->full_address." - ".$request->postal_code;
                    
                    list($pointSuccess, $point, $correctedAddress) = PublicRepo::getGeoLocationPoint($searchAddress);
                	// save Address
                	list($jobstatus[0],$jobmessage[0]) = AdminRepo::updateJobAddress($Job, array(
        	            'city_id'       => $request->city_id,
        	            'postal_code'   => $request->postal_code,
        	            'street'        => $request->street,
                        'latitude'      => $point[0],
                        'longitude'      => $point[1],
                	));

                	// save Skills
                	$skills = $request->has('skills') ? $request->skills : [];

                	$skills_saved = AdminRepo::updateSkills($Job, $skills);


                	// save Keyword
                	$keyword_line = $request->has('keyword') ? $request->keyword : [];
                	$keyword = explode(",", $keyword_line);
                	list($jobstatus[1],$jobmessage[1]) = AdminRepo::updatekeyword($Job, $keyword);

                	// save weekly
                	$weekly = $request->has('weekly') ? $request->weekly : [];
                	$weekly_saved = AdminRepo::updateweekly($Job, $weekly);
                    if($jobstatus[0] && $jobstatus[1]){
                    	return redirect()->route('admin-job',['employeeId'=>$request->jobTypeId,'jobTypeId'=>$request->jobTypeId,'date'=>$request->date,'jobCategoryId'=>$request->jobCategoryId,'jobCityId'=>$request->jobCityId,'page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
                        	'success_message' => "job saved!"
                    	]);
                    }else{
                        return redirect()->back()->with([
                            'error_message' => "There was an error while adding keyword and job address, try again"
                        ]);
                    }
            	}else{
            		return redirect()->back()->withInput(Input::all())->with([
                    	'error_message' => "There was an error while adding your job, try again"
                	]);
            	}
            }else{
                $error_message = "";

                if(!$dateStatus[0]) {
                    $error_message .= $dateMessage[0]."<br/>";
                }

                if(!$dateStatus[1]) {
                    $error_message .= $dateMessage[1]."<br/>";
                }

                return redirect()->back()->withInput(Input::all())->with([
                        'error_message' => $error_message
                ]);
            }
        // }else{
        //         return redirect()->back()->withInput(Input::all())->with([
        //                 'error_message' => "please enter salary between salary range"
        //             ]);
        //     }
    }

    //add New Job
    public function getActiveInactiveJob(Request $request){
        //print_r($request->action);exit;
        if($request->action=='active'){
           $status=$request->action; 
        }elseif ($request->action=="inactive") {
            $status=$request->action;
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while update your job status, try again"
            ]);   
        }

        list($status,$message,$jobrec) = AdminRepo::updateJobaction($request->JobId, $status);
        if($status){
           return redirect()->route('admin-job',['jobTypeId'=>$request->jobTypeId,'date'=>$request->date,'jobCategoryId'=>$request->jobCategoryId,'jobCityId'=>$request->jobCityId,'page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
                'success_message' => "Status successfully saved!"
            ]); 
       }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while update your job, try again"
            ]);
       }
    }

    Public function getRepostJob(Request $request, Job $Job){

        list($sts[0],$msg[0])=$Job->isRepostable();
        if(!$sts[0]){
            return redirect()->route('admin-job', ['search'=>$request->filter])->withInput(Input::all())->with([
                'error_message'=>$msg[0]
                ]);
        }
        if($Job->ending_date){
            $daysDiff=Utility::diffInDates($Job->starting_date->format("Y-m-d"),$Job->ending_date->format("Y-m-d"));
        }else{
            $daysDiff=0;
        }

        if($Job->expiration_date){
            $expirdaysDiff=Utility::diffInDates($Job->created_at->format("Y-m-d"),$Job->expiration_date->format("Y-m-d"));
        }else{
            $expirdaysDiff=0;
        }

        $jobWeekdays = PublicRepo::jobWeekdayOf($Job->id);
        $jobCertificates = PublicRepo::jobCertificateOf($Job->id);
        $certificates = "";
        foreach($jobCertificates as $jobCertificate) {
            if(!empty($jobCertificate)){
                $certificates .= $jobCertificate->certificate.", ";
            }
        }
        $certificates= rtrim($certificates,', ');
        
        $jobKeywords = PublicRepo::jobKeywordsOf($Job->id);
        $Keywords = "";
        foreach($jobKeywords as $jobKeyword) {
            if(!empty($jobKeyword)){
                $Keywords .= $jobKeyword->keyword.", ";
            }
        }
        $Keywords= rtrim($Keywords,', ');
        $TagsAllskill = PublicRepo::skillsOf($Job->job_title_id);

        $jobskills = $Job->skills;
        $employers = PublicRepo::allEmployers();
        $Categories = PublicRepo::allJobCategories();
        $jobTitles = PublicRepo::allJobTitles();
        $educations = PublicRepo::allEducations();
        $experiences = PublicRepo::allExperiences();
        $jobTypes = PublicRepo::allJobTypes();
        $experienceLevels = PublicRepo::allExperienceLevels();
        $salaryTypes = PublicRepo::allSalaryTypes();
        $payBys = PublicRepo::allPayBy();
        $payPeriods = PublicRepo::allPayPeriod();
        $Countries = PublicRepo::allCountries(0);
        $Cities = PublicRepo::allCities();

        $oldDates = [];
        // Removed as in Report Calendar Pad should be empty at first.
        // $jMeta = $Job->jMeta();
        // if(isset($jMeta["days"])) {
        //     $oldDates = $jMeta["days"];
        // }

        return view('backend.job.repost-job',[
            'employers'=>$employers,
            'Categories'=>$Categories,
            'educations'=>$educations,
            'experiences'=>$experiences,
            'jobTypes'=>$jobTypes,
            'experienceLevels'=>$experienceLevels,
            'salaryTypes'=>$salaryTypes,
            'payBys'=>$payBys,
            'payPeriods'=>$payPeriods,
            'Countries'=>$Countries,
            'Cities'=>$Cities,
            'Jobs'=>$Job,
            'certificates'=>$certificates,
            'Keywords'=>$Keywords,
            'jobWeekdays'=>$jobWeekdays,
            'jobskills'=>$jobskills,
            'TagsAllskill'=>$TagsAllskill,
            'oldDates'=>$oldDates,
            'daysDiff'=>$daysDiff,
            'expirdaysDiff'=>$expirdaysDiff,
            'isRepost' => true
        ]);
    }
    Public function postRepostJob(Request $request, Job $Job){
        list($sts[0],$msg[0])=$Job->isRepostable();
        if(!$sts[0]){
            return redirect()->route('admin-job', ['search'=>$request->filter])->withInput(Input::all())->with([
                'error_message'=>$msg[0]
                ]);
        }
        $this->validate($request, [
            'employer_id'=> 'required|findId:employers',
            'title' => 'required',
            'vacancies' => 'required|integer',
            'job_category_id'=> 'required|findId:job_categories',
            'job_title_id' => 'required|findId:job_titles,,job_category_id,'.$request->job_category_id,
            'education_id' => 'required|findId:education',
            'experience_id'=> 'required|findId:experiences',
            'experience_level_id' => 'required|findId:experience_levels',
            'job_type_id' => 'required|findId:job_types',
            'country_id'=> 'required|findId:countries',
            'state_id' => 'required|findId:states',
            'city_id' => 'required|findId:cities,,state_id,'.$request->state_id,
            //'street'=> 'required',
            'postal_code' => 'postalcode',
            //'certificates' => 'required',
            'keyword' => 'required',
            'starting_date'=> 'required|date_format:"Y-m-d"',
            'ending_date' => 'date_format:"Y-m-d"',
            'expiration_date' => 'required|date_format:"Y-m-d"',
            'work_schedule_from' => 'required|date_format:"H:i:s"',
            'work_schedule_to'=> 'required|date_format:"H:i:s"',
            'salary_type_id' => 'required|findId:salary_types',
            'salary' => 'required_if:salary_check,1|regex:/^\d*(\.\d*)?$/',
            'pay_by_id'=> 'required|findId:pay_bies',
            'pay_period_id' => 'required|findId:pay_periods',
            //'salary_range_id'=>'required|findId:salary_ranges',
            //'benefits' => 'required',
            'description' => 'required',
        ],[
            'employer_id.required'=> 'Please select employer.',
            'title.required' => 'title enter title.',
            'vacancies.required' => 'Please enter vacancies.',
            'job_category_id.required'=> 'Please select job category.',
            'job_title_id.required' => 'Please select job title.',
            'education_id.required' => 'Please select education.',
            'experience_id.required.'=> 'Please select experience.',
            'experience_level_id.required' => 'Please select experience level.',
            'job_type_id.required' => 'Please select job type.',
            'country_id.required'=> 'Please select Country .',
            'state_id.required' =>'Please select state.',
            'city_id.required' => 'Please select city.',
            //'street.required'=> 'Please enter street.',
            'postal_code.required' => 'Please enter postal code.',
            //'certificates.required' =>'Please enter certificates.',
            'keyword.required' =>'Please enter job keyword.',
            'starting_date.required'=>'Please select starting date.',
            //'ending_date.required' => 'Please select ending date.',
            'work_schedule_from.required' =>'Please select work schedule from.',
            'work_schedule_to.required'=>'Please select Country name.',
            'salary_type_id.required' =>'Please select Country name.',
            //'salary.required' =>'Please enter salary.',
            'pay_by_id.required'=>'Please select payBy.',
            'pay_period_id.required' =>'Please select pay period.',
            //'benefits.required' =>'Please enter benefits.',
            //'description.required' =>'Please select description.',
        ]);
        // list($status,$record)= PublicRepo::getsalaryof($request->salary,$request->salary_range_id);
        // if($status){
            // if($request->ending_date){
            //     $starting_date = Carbon::createFromFormat("Y-m-d", $request->starting_date); 
            //     $ending_date   = Carbon::createFromFormat("Y-m-d", $request->ending_date);
            //     $days=$starting_date->diffInDays($ending_date,false);
            // }else{
            //     $days=0;
            // }
            list($JobIsUnic[0],$JobIsUnicMessage[0])=PublicRepo::IsUnicJob($request->employer_id,$request->title,$request->city_id,$request->starting_date,$Job->id);
            if(!$JobIsUnic[0]){
                return redirect()->back()->withInput(Input::all())->with([
                    'error_message' => $JobIsUnicMessage[0]
                ]);
            }
            list($dateStatus[0],$dateMessage[0])=AdminRepo::jobDateValidation($request->starting_date,$request->ending_date);
            list($dateStatus[1],$dateMessage[1])=AdminRepo::jobExpireDateValidation($request->expiration_date);
            if(($work_schedule_from = Utility::parseCarbonDate($request->work_schedule_from, "H:i:s")) &&
               ($work_schedule_to = Utility::parseCarbonDate($request->work_schedule_to, "H:i:s"))) {
                if($work_schedule_to < $work_schedule_from) {
                    return redirect()->back()->withInput(Input::all())->withErrorMessage("Invalid schedule from to must be greater than schedule from, please try again.");
                }
            } else {
                return redirect()->back()->withInput(Input::all())->withErrorMessage("Invalid schedule entry, please try again.");
            }
            if($dateStatus[0] && $dateStatus[1]){
                if($request->has("job_type_id")) {
                    $jobType = PublicRepo::getJobType($request->job_type_id);
                    if($jobType) {
                        if($jobType->day_selection == 1) {
                            // New logic for past day validation
                            if(!$request->has('dates') || ($request->has('dates') && (count($request->dates) == 0 || !Job::validPartDays($request->dates,"Y-m-d"))) ) {
                                return redirect()->back()->withInput(Input::all())->with([
                                    'error_message' => "Sorry you must select valid days from job calendar for part time jobs (you cannot select past dates)."
                                ]);
                            }

                            /// Old logic for past day validation
                            // if(!$request->has('dates') || ($request->has('dates') && (count($request->dates) == 0 || !Utility::validateDatesInArray($request->dates,"Y-m-d","date_greaterThanToday"))) ) {
                            //     return redirect()->back()->withInput(Input::all())->with([
                            //         'error_message' => "Sorry you must select valid days from job calendar for part time jobs (you cannot select past dates)."
                            //     ]);
                            // }
                        }
                    }
                }
                list($status,$message,$jobrec) = AdminRepo::addJob($request->all());
                if($status){
                    $certificate_line = $request->has('certificates') ? $request->certificates : "";
                    $certificates = explode(",", $certificate_line);

                    // save certificates
                    $certificates_saved = AdminRepo::addJobCertificates($jobrec, $certificates);

                    $city = PublicRepo::searchLocations(null, null, $request->city_id);
                    $searchAddress = $city[0]->full_address." - ".$request->postal_code;
                    list($pointSuccess, $point, $correctedAddress) = PublicRepo::getGeoLocationPoint($searchAddress);
                    // save Address
                    list($jobstatus[0],$jobmessage[0]) = AdminRepo::addJobAddress($jobrec, array(
                        'city_id'       => $request->city_id,
                        'postal_code'   => $request->postal_code,
                        'street'        => $request->street,
                        'latitude'      => $point[0],
                        'longitude'      => $point[1],
                    ));

                    // save Skills
                    $skills = $request->has('skills') ? $request->skills : [];
                    $skills_saved = AdminRepo::addSkills($jobrec, $skills);

                    // save Keyword
                    $keyword_line = $request->has('keyword') ? $request->keyword : [];
                    $keyword = explode(",", $keyword_line);
                    list($jobstatus[1],$jobmessage[1]) = AdminRepo::addkeyword($jobrec, $keyword);

                    // save weekly
                    $weekly = $request->has('weekly') ? $request->weekly : [];
                    $weekly_saved = AdminRepo::addweekly($jobrec, $weekly);
                    if($jobstatus[0] && $jobstatus[1]){
                        $Job->delete();
                        Notifier::jobPosted($jobrec,MyAuth::user('admin'));
                        return redirect()->route('admin-job',['jobTypeId'=>$request->jobTypeId,'date'=>$request->date,'jobCategoryId'=>$request->jobCategoryId,'jobCityId'=>$request->jobCityId,'page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
                            'success_message' => "Repost Job successfully"
                        ]); 
                    }else{
                        $jobrec->delete();
                        return redirect()->back()->with([
                            'error_message' => "There was an error while adding your Repost job, try again"
                        ]);
                    }
                }else{
                    return redirect()->back()->withInput(Input::all())->with([
                        'error_message' => "There was an error while adding your job, try again"
                    ]);
                }
            }else{
                $error_message = "";

                if(!$dateStatus[0]) {
                    $error_message .= $dateMessage[0]."<br/>";
                }

                if(!$dateStatus[1]) {
                    $error_message .= $dateMessage[1]."<br/>";
                }

                return redirect()->back()->withInput(Input::all())->with([
                        'error_message' => $error_message
                ]);
            }
        // }else{
        //     return redirect()->back()->withInput(Input::all())->with([
        //             'error_message' => "please enter salary between salary range"
        //         ]);
        // }

    }

    Public function getRenewJob(Request $request, Job $Job){
        list($sts[0],$msg[0])=$Job->isRenewable();
        if(!$sts[0]){
            return redirect()->route('admin-job', ['search'=>$request->filter])->withInput(Input::all())->with([
                'error_message'=>$msg[0]
                ]);
        }
        if($Job->ending_date){
            $daysDiff=Utility::diffInDates($Job->starting_date->format("Y-m-d"),$Job->ending_date->format("Y-m-d"));
        }else{
            $daysDiff=0;
        }

        if($Job->expiration_date){
            $expirdaysDiff=Utility::diffInDates($Job->created_at->format("Y-m-d"),$Job->expiration_date->format("Y-m-d"));
        }else{
            $expirdaysDiff=0;
        }
        $jobWeekdays = PublicRepo::jobWeekdayOf($Job->id);
        $jobCertificates = PublicRepo::jobCertificateOf($Job->id);
        $certificates = "";
        foreach($jobCertificates as $jobCertificate) {
            if(!empty($jobCertificate)){
                $certificates .= $jobCertificate->certificate.", ";
            }
        }
        $certificates= rtrim($certificates,', ');
        
        $jobKeywords = PublicRepo::jobKeywordsOf($Job->id);
        $Keywords = "";
        foreach($jobKeywords as $jobKeyword) {
            if(!empty($jobKeyword)){
                $Keywords .= $jobKeyword->keyword.", ";
            }
        }
        $Keywords= rtrim($Keywords,', ');
        $TagsAllskill = PublicRepo::skillsOf($Job->job_title_id);

        $jobskills = $Job->skills;
        $employers = PublicRepo::allEmployers();
        $Categories = PublicRepo::allJobCategories();
        $jobTitles = PublicRepo::allJobTitles();
        $educations = PublicRepo::allEducations();
        $experiences = PublicRepo::allExperiences();
        $jobTypes = PublicRepo::allJobTypes();
        $experienceLevels = PublicRepo::allExperienceLevels();
        $salaryTypes = PublicRepo::allSalaryTypes();
        $payBys = PublicRepo::allPayBy();
        $payPeriods = PublicRepo::allPayPeriod();
        $Countries = PublicRepo::allCountries(0);
        $Cities = PublicRepo::allCities();
        $oldDates = [];
        $jMeta = $Job->jMeta();

        if(isset($jMeta["days"])) {
            $oldDates = $jMeta["days"];
        }
        return view('backend.job.renew-job',[
            'employers'=>$employers,
            'Categories'=>$Categories,
            'educations'=>$educations,
            'experiences'=>$experiences,
            'jobTypes'=>$jobTypes,
            'experienceLevels'=>$experienceLevels,
            'salaryTypes'=>$salaryTypes,
            'payBys'=>$payBys,
            'payPeriods'=>$payPeriods,
            'Countries'=>$Countries,
            'Cities'=>$Cities,
            'Jobs'=>$Job,
            'certificates'=>$certificates,
            'Keywords'=>$Keywords,
            'jobWeekdays'=>$jobWeekdays,
            'jobskills'=>$jobskills,
            'TagsAllskill'=>$TagsAllskill,
            'oldDates' => $oldDates,
            'daysDiff'=>$daysDiff,
            'expirdaysDiff'=>$expirdaysDiff
            ]);
    }
    public function postRenewJob(Request $request, Job $Job){
        list($sts[0],$msg[0])=$Job->isRenewable();
        if(!$sts[0]){
            return redirect()->route('admin-job', ['search'=>$request->filter])->withInput(Input::all())->with([
                'error_message'=>$msg[0]
                ]);
        }
        $this->validate($request, [
            'employer_id'=> 'required|findId:employers',
            'title' => 'required',
            'vacancies' => 'required|integer',
            'job_category_id'=> 'required|findId:job_categories',
            'job_title_id' => 'required|findId:job_titles,,job_category_id,'.$request->job_category_id,
            'education_id' => 'required|findId:education',
            'experience_id'=> 'required|findId:experiences',
            'experience_level_id' => 'required|findId:experience_levels',
            'job_type_id' => 'required|findId:job_types',
            'country_id'=> 'required|findId:countries',
            'state_id' => 'required|findId:states',
            'city_id' => 'required|findId:cities,,state_id,'.$request->state_id,
            //'street'=> 'required',
            'postal_code' => 'postalcode',
            //'certificates' => 'required',
            'keyword' => 'required',
            'starting_date'=> 'required|date_format:"Y-m-d"',
            'ending_date' => 'date_format:"Y-m-d"',
            'expiration_date' => 'required|date_format:"Y-m-d"',
            'work_schedule_from' => 'required|date_format:"H:i:s"',
            'work_schedule_to'=> 'required|date_format:"H:i:s"',
            'salary_type_id' => 'required|findId:salary_types',
            'salary' => 'required_if:salary_check,1|regex:/^\d*(\.\d*)?$/',
            'pay_by_id'=> 'required|findId:pay_bies',
            'pay_period_id' => 'required|findId:pay_periods',
            //'salary_range_id'=>'required|findId:salary_ranges',
            //'benefits' => 'required',
            'description' => 'required',
        ],[
            'employer_id.required'=> 'Please select employer.',
            'title.required' => 'title enter title.',
            'vacancies.required' => 'Please enter vacancies.',
            'job_category_id.required'=> 'Please select job category.',
            'job_title_id.required' => 'Please select job title.',
            'education_id.required' => 'Please select education.',
            'experience_id.required.'=> 'Please select experience.',
            'experience_level_id.required' => 'Please select experience level.',
            'job_type_id.required' => 'Please select job type.',
            'country_id.required'=> 'Please select Country .',
            'state_id.required' =>'Please select state.',
            'city_id.required' => 'Please select city.',
            //'street.required'=> 'Please enter street.',
            'postal_code.required' => 'Please enter postal code.',
            //'certificates.required' =>'Please enter certificates.',
            'keyword.required' =>'Please enter job keyword.',
            'starting_date.required'=>'Please select starting date.',
            //'ending_date.required' => 'Please select ending date.',
            'work_schedule_from.required' =>'Please select work schedule from.',
            'work_schedule_to.required'=>'Please select Country name.',
            'salary_type_id.required' =>'Please select Country name.',
            //'salary.required' =>'Please enter salary.',
            'pay_by_id.required'=>'Please select payBy.',
            'pay_period_id.required' =>'Please select pay period.',
            //'benefits.required' =>'Please enter benefits.',
            //'description.required' =>'Please select description.',
        ]);
        // list($status,$record)= PublicRepo::getsalaryof($request->salary,$request->salary_range_id);
        // if($status){

            // if($request->ending_date){
            //     $starting_date = Carbon::createFromFormat("Y-m-d", $request->starting_date); 
            //     $ending_date   = Carbon::createFromFormat("Y-m-d", $request->ending_date);
            //     $total=$starting_date->diffInDays($ending_date,false);
            // }else{
            //     $total=0;
            // }
            //list($dateStatus[0],$dateMessage[0])=AdminRepo::jobDateValidation($request->starting_date,$request->ending_date);
            //list($dateStatus[1],$dateMessage[1])=AdminRepo::jobExpireDateValidation($request->expiration_date);
            list($JobIsUnic[0],$JobIsUnicMessage[0])=PublicRepo::IsUnicJob($request->employer_id,$request->title,$request->city_id,$request->starting_date,$Job->id);
            if(!$JobIsUnic[0]){
                return redirect()->back()->withInput(Input::all())->with([
                    'error_message' => $JobIsUnicMessage[0]
                ]);
            }
            $strdays="";
            $expdays="";
            if($startDate = Utility::parseCarbonDate($request->starting_date,"Y-m-d")){
                $strdays=Utility::diffInDates($startDate->format("Y-m-d"),$Job->starting_date->format("Y-m-d"));
            }
                
            if($expdt = Utility::parseCarbonDate($request->expiration_date,"Y-m-d")){
                $expdays=Utility::diffInDates($expdt->format("Y-m-d"),$Job->expiration_date->format("Y-m-d"));
            }

            if($strdays != 0) {
                list($dateStatus[0],$dateMessage[0])=AdminRepo::jobDateValidation($request->starting_date,$request->ending_date, true);
            } else {
                list($dateStatus[0],$dateMessage[0])=AdminRepo::jobDateValidation($request->starting_date,$request->ending_date, false);
            }

            if($expdays==0){
                list($dateStatus[1],$dateMessage[1])=[true, "true",null];
            }else{
                list($dateStatus[1],$dateMessage[1])=AdminRepo::jobExpireDateValidation($request->expiration_date);
            }
            if(($work_schedule_from = Utility::parseCarbonDate($request->work_schedule_from, "H:i:s")) &&
               ($work_schedule_to = Utility::parseCarbonDate($request->work_schedule_to, "H:i:s"))) {
                if($work_schedule_to < $work_schedule_from) {
                    return redirect()->back()->withInput(Input::all())->withErrorMessage("Invalid schedule from to must be greater than schedule from, please try again.");
                }
            } else {
                return redirect()->back()->withInput(Input::all())->withErrorMessage("Invalid schedule entry, please try again.");
            }
            if($dateStatus[0] && $dateStatus[1]){
                $current_date = \Carbon\Carbon::now(); 
                $renew_date   = $Job->renew_date;
                $days=$renew_date->diffInDays($current_date,false);
                if($days >= 0){    
                    if($request->has("job_type_id")) {
                    $jobType = PublicRepo::getJobType($request->job_type_id);
                        if($jobType) {
                            if($jobType->day_selection == 1) {
                                // New logic for past day validation
                                if(!$request->has('dates') || ($request->has('dates') && (count($request->dates) == 0 || !$Job->isValidPartDays($request->dates,"Y-m-d"))) ) {
                                    return redirect()->back()->withInput(Input::all())->with([
                                        'error_message' => "Sorry you must select valid days from job calendar for part time jobs (you cannot select past dates)."
                                    ]);
                                }

                                /// Old logic for past day validation
                                // if(!$request->has('dates') || ($request->has('dates') && (count($request->dates) == 0 || !Utility::validateDatesInArray($request->dates,"Y-m-d","date_greaterThanToday"))) ) {
                                //     return redirect()->back()->withInput(Input::all())->with([
                                //         'error_message' => "Sorry you must select valid days from job calendar for part time jobs (you cannot select past dates)."
                                //     ]);
                                // }
                            }
                        }
                    }
                    list($status,$message,$jobrec) = AdminRepo::updateJob($Job->id,$request->all(),$current_date);
                    if($status){
                        $certificate_line = $request->has('certificates') ? $request->certificates : "";
                        $certificates = explode(",", $certificate_line);

                        // save certificates
                        $certificates_saved = AdminRepo::updateJobCertificates($Job, $certificates);

                        $city = PublicRepo::searchLocations(null, null, $request->city_id);
                        $searchAddress = $city[0]->full_address." - ".$request->postal_code;
                        list($pointSuccess, $point, $correctedAddress) = PublicRepo::getGeoLocationPoint($searchAddress);
                        // save Address
                        list($jobstatus[0],$jobmessage[0]) = AdminRepo::updateJobAddress($Job, array(
                            'city_id'       => $request->city_id,
                            'postal_code'   => $request->postal_code,
                            'street'        => $request->street,
                            'latitude'      => $point[0],
                            'longitude'      => $point[1],
                        ));

                        // save Skills
                        $skills = $request->has('skills') ? $request->skills : [];

                        $skills_saved = AdminRepo::updateSkills($Job, $skills);


                        // save Keyword
                        $keyword_line = $request->has('keyword') ? $request->keyword : [];
                        $keyword = explode(",", $keyword_line);
                        list($jobstatus[1],$jobmessage[1]) = AdminRepo::updatekeyword($Job, $keyword);

                        // save weekly
                        $weekly = $request->has('weekly') ? $request->weekly : [];
                        $weekly_saved = AdminRepo::updateweekly($Job, $weekly);
                        if($jobstatus[0] && $jobstatus[1]){
                            return redirect()->route('admin-job',['jobTypeId'=>$request->jobTypeId,'date'=>$request->date,'jobCategoryId'=>$request->jobCategoryId,'jobCityId'=>$request->jobCityId,'page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
                                'success_message' => "Renew job successfully!"
                            ]);
                        }else{
                            return redirect()->route('admin-job',['jobTypeId'=>$request->jobTypeId,'date'=>$request->date,'jobCategoryId'=>$request->jobCategoryId,'jobCityId'=>$request->jobCityId,'page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->with([
                                'error_message' => "There was an error while adding your Keyword and address, try again!"
                            ]);
                        }
                    }else{
                        return redirect()->back()->withInput(Input::all())->with([
                            'error_message' => "There was an error while adding your job, try again"
                        ]);
                    }
                }else{
                    return redirect()->back()->withInput(Input::all())->with([
                            'error_message' => "After 24 hourse you can renew job"
                        ]);
                }
            }else{
                $error_message = "";

                if(!$dateStatus[0]) {
                    $error_message .= $dateMessage[0]."<br/>";
                }

                if(!$dateStatus[1]) {
                    $error_message .= $dateMessage[1]."<br/>";
                }

                return redirect()->back()->withInput(Input::all())->with([
                        'error_message' => $error_message
                ]);
            }
        // }else{
        //     return redirect()->back()->withInput(Input::all())->with([
        //             'error_message' => "please enter salary between salary range"
        //         ]);
        // }
    }
}
