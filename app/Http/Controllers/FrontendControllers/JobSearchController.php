<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GuestController;
use App\Http\Requests;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Repos\API\searchLocations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class JobSearchController extends GuestController
{
    
    public function getIndex(Request $request) {

    	$keyword = "";
    	$location = "";
    	$radius = 0;
    	$salaryType = 0;
    	$salaryRate = 0;
    	$daysAgo = 0;
    	$recruiterType = 0;
    	$jobType = 0;
        $jobCategory = 0;
        $loggedInUserId = '';
        $cookieJobSearchData = array();
        $jobCategoryName=null;
        $jobTypeName=null;
        $recruiterTypeName=null;
        $salaryTypeName=null;
        $radius_data=false;
        $companyName=null;
        // Auto Suggestion when user is logged in
        if(MyAuth::check()) {
            $user = MyAuth::user();
            $loggedInUserId = $user->id;

            // validate empty search terms
            //if($keyword == "" && $location == "" && $radius == 0 && $salaryType == 0 && $salaryRate == 0 && $daysAgo == 0 && $recruiterType == 0 && $jobType == 0) {
            if(!$request->has('keywords') && !$request->has('location') && $request->has('radius') && $request->radius == 0 && count($request->all()) == 3) {
                // Auto Address
                $user_address = $user->addresses()->first();
                if($user_address && $user_address->country()) {
                    $location = $user_address->city->getName().", ".$user_address->state()->getName();
                } else if($user_address) {
                    $location = $user_address->street;
                    if(trim($user_address->postal_code) != "") {
                        $location .= "- ".$user_address->postal_code;
                    }
                }
                

                // // Auto Job Title
                $user_experience = $user->experiences()->first();

                // if($user_experience) {
                //     $desired_job_title = $user_experience->desired_job_title;
                //     $keyword = $desired_job_title->getTitle();
                // }

                // Auto Desired Locations - Multi Location Searchs
                    // Not now...

                // Auto Desired Salary Type and Range
                if($user_experience) {
                    $desired_salary_range = $user_experience->desired_salary_range;
                    if($desired_salary_range && $desired_salary_range->salaryType) {
                        $salaryType = 0;//$desired_salary_range->salaryType->id;
                        $salaryRate = 0;//$desired_salary_range->range_from;
                    }
                    if($user_experience->desired_job_title->category){
                        $categoryData=$user_experience->desired_job_title->category;
                        $jobCategory = $categoryData->id;
                        $jobCategoryName=$categoryData->name;
                        
                    }
                }
            }
        }


        if($request->has('jobType')) {
    		$jobType = $request->jobType;
            if($jobType){
                $jobTypeData=PublicRepo::getJobType($jobType);
                $jobTypeName=$jobTypeData->name;
            }
    	}
        if($request->has('jobCategory')) {
            $jobCategory = $request->jobCategory;
            if($jobCategory){
                $CategoryData=PublicRepo::getJobCategory($jobCategory);
                $jobCategoryName=$CategoryData->name;
            }
        }

    	if($request->has('recruiterType')) {
    		$recruiterType = $request->recruiterType;
            if($recruiterType){
                $recruiterTypeData=PublicRepo::getRecruiterType($recruiterType);
                $recruiterTypeName=$recruiterTypeData->name;
            }
    	}

    	if($request->has("daysAgo")) {
    		$daysAgo = $request->daysAgo;
    	}

    	if($request->has("keywords")) {
    		$keyword = $request->keywords;
    	}

    	if($request->has("location")) {
    		$location = $request->location;
    	}

    	if($request->has("radius")) {
    		$radius = $request->radius;
    	}

    	if($request->has("salaryType")) {
    		$salaryType = $request->salaryType;
            if($salaryType){
                $salaryTypeData=PublicRepo::getSalaryType($salaryType);
                $salaryTypeName=$salaryTypeData->salary_type_name;
            }
    	}

    	if($request->has("salaryRate")) {
    		$salaryRate = $request->salaryRate;
    	}

        $get_params = ['keywords'=>$keyword, 'location'=>$location, 'radius'=>$radius, 'salaryType'=> $salaryType, 'salaryRate' => $salaryRate, 'daysAgo'=>$daysAgo, 'recruiterType'=>$recruiterType, 'jobType'=>$jobType,'jobCategory'=>$jobCategory];

        if($request->has("sortBy")) {
            $get_params["sortBy"] = $request->sortBy;
        } else {
            $get_params["sortBy"] = "";
        }
        $perPage = 10;
        if($request->has('viewMode')) {
            $get_params["viewMode"] = $request->viewMode;
            if($get_params["viewMode"]=='map'){
                $perPage = PublicRepo::countJobs();
            }
        } else {
            $get_params["viewMode"] = "list";
        }

        $filters = [];

        if($request->has('jobTitle')) {
            $filters["jobTitleId"] = $request->jobTitle;
            $get_params["jobTitle"] = $request->jobTitle;
        } 
         if($request->has('jobCategory')) {
            //$filters["jobCategoryId"] = $request->jobCategory; By mohan
            $filters["jobCategory"] = $request->jobCategory; 
            $get_params["jobCategory"] = $request->jobCategory;
        }

        if($request->has('employer')) {
            $filters["employerId"] = $request->employer;
            $get_params["employer"] = $request->employer;
            if($request->employer){
                $EmployerData=PublicRepo::getEmployerData($request->employer);
                if($EmployerData){
                    $companyName=$EmployerData->company_name;
                }
            }
        }

        if($request->has('salaryRateTo')) {
            $filters["salaryRateTo"] = $get_params["salaryRateTo"] = $request->salaryRateTo;
        }

        if($request->has('onlyNegotiable')) {
            $filters["onlyNegotiable"] = true;   
        }

        //$get_params["filters"] = $filters;

        //$perPage = 10;

        //////////////////////////////
        /// Location validator ///////
        //////////////////////////////

        $location = $request->has('location') ? $request->location : '';

        $cityId = 0;
        $Extcities=null;
        if($location != "") {
            $Extcities = PublicRepo::searchExactLocations($location, 0);
          
            $cities = PublicRepo::searchLocationsHome($location, 0);
            
            if(count($Extcities) == 0 && count($cities) != 1) {
                return view('frontend.job-location-selector', [ 'cities' => $cities ]);
            }else { 
                $cityId = $cities[0]->id;
            }
            $filters["cityId"] = $cityId;
            
        } 

        /// End Location Validator ///

        /* ===== Check search job keywords from cookie and store ===== */
        
        $countParameters = count(explode('&', $_SERVER['QUERY_STRING']));
        
        $cookieName = 'jobSearchFor'.$loggedInUserId;
        if(empty($location)){
            $radius_data=true;
            $filters["radius_data"] = $radius_data;
        }

        if(empty($keyword) && empty($location) && empty($radius) && $countParameters == 3)
        {   
            if(Cookie::has($cookieName)){

                $cookieJobSearchData = unserialize(Cookie::get($cookieName));
                $get_params["keywords"]     = (isset($cookieJobSearchData['keywords'])) ? $cookieJobSearchData['keywords'] : "";
                $get_params["location"]     = (isset($cookieJobSearchData['location'])) ? $cookieJobSearchData['location'] : "";
                $get_params["radius"]       = (isset($cookieJobSearchData['radius'])) ? $cookieJobSearchData['radius'] : "0";
                $get_params["salaryType"]   = (isset($cookieJobSearchData['salaryType'])) ? $cookieJobSearchData['salaryType'] : "";
                $get_params["salaryRate"]   = (isset($cookieJobSearchData['salaryRate'])) ? $cookieJobSearchData['salaryRate'] : "";
                $get_params["daysAgo"]      = (isset($cookieJobSearchData['daysAgo'])) ? $cookieJobSearchData['daysAgo'] : "0";
                $get_params["recruiterType"]= (isset($cookieJobSearchData['recruiterType'])) ? $cookieJobSearchData['recruiterType'] : "";
                $get_params["jobType"]      = (isset($cookieJobSearchData['jobType'])) ? $cookieJobSearchData['jobType'] : "";
                $get_params["jobCategory"]  = (isset($cookieJobSearchData['jobCategory'])) ? $cookieJobSearchData['jobCategory'] : "";
                $get_params["sortBy"]       = (isset($cookieJobSearchData['sortBy']))?:"";
                $perPage                    = (isset($cookieJobSearchData['perPage'])) ? $cookieJobSearchData['perPage'] : "";
                $filters                    = unserialize($cookieJobSearchData['filters']);
            }
        }

        $cookie = 'default-provalue-cookie';
        if($countParameters > 3)
        {
            // Store searched job keywords
            //$cookie = Cookie::forget($cookieName);
            $newJobSearchKeywords["keywords"]     = $get_params['keywords'];
            $newJobSearchKeywords["location"]     = $get_params['location'];
            $newJobSearchKeywords["radius"]       = $get_params['radius'];
            $newJobSearchKeywords["salaryType"]   = $get_params['salaryType'];
            $newJobSearchKeywords["salaryRate"]   = $get_params['salaryRate'];
            $newJobSearchKeywords["daysAgo"]      = $get_params['daysAgo'];
            $newJobSearchKeywords["recruiterType"]= $get_params['recruiterType'];
            $newJobSearchKeywords["jobType"]      = $get_params['jobType'];
            $newJobSearchKeywords["jobCategory"]      = $get_params['jobCategory'];
            $newJobSearchKeywords["sortBy"]       = $get_params['sortBy'];
            $newJobSearchKeywords['perPage']      = $perPage;
            $newJobSearchKeywords['filters']      = serialize($filters);

            $cookie = Cookie::forever($cookieName, serialize($newJobSearchKeywords));    
        }

        if($countParameters <= 1)
        {
            $cookie = Cookie::forget($cookieName);
        }
        /*== Check search keywords from cookie End ==*/

        list($jobs, $location_cordinates, $clearAddress) = PublicRepo::searchJobs($get_params["keywords"], $get_params["location"], $get_params["radius"], $get_params["salaryType"], $get_params["salaryRate"], $get_params["daysAgo"], $get_params["recruiterType"], $get_params["jobType"], $get_params["sortBy"], $perPage, $filters,"",$get_params["jobCategory"],$get_params["viewMode"]);
        
        Log::info("jobSearchLog", $get_params);
        try {
            $routeName = app('router')->getRoutes()->match(app('request')->create($request->header('referrer')))->getName();
            if($routeName == "front-home") {
                PublicRepo::updatePublicSearch($request, count($jobs));
            }
        } catch(\Exception $e) {}

        session()->put('search_history', $get_params);
        
        if($location_cordinates[0] == 0 && $location_cordinates[1] == 0) {
            $location_cordinates[2] = 2;
        } else {
            $location_cordinates[2] = 11;
        }
        
        $get_params["location"] = $clearAddress;
        // echo '<pre>';
        // print_r($jobs);
        // echo '</pre>';

        $markers = [
            'exacts' => [],
            'approx' => []
        ];
        
        if($get_params["viewMode"] == "map") {
            foreach($jobs as $job) {
                $uniqPoint = $job->longitude.$job->latitude;
                $postal_code = $job->postal_code;
                if($postal_code > 0) { // exact location
                    if(!isset($markers["exacts"][$uniqPoint])) {
                        $markers["exacts"][$uniqPoint] = [];
                    }
                    $markers["exacts"][$uniqPoint][] = [
                        'point' => [$job->latitude, $job->longitude],
                        'job' => $job->toArray()                        
                    ];
                } else { // approx location
                    if(!isset($markers["approx"][$uniqPoint])) {
                        $markers["approx"][$uniqPoint] = [];
                    }
                    $markers["approx"][$uniqPoint][] = [
                        'point' => [$job->latitude, $job->longitude],
                        'job' => $job->toArray()
                    ];
                }
            }
        }

        if($location && $location!=""){
            $locationArray=explode(", ", $location);
            $cityData=PublicRepo::getlocationSearchState($locationArray);
            $defaultQuery=false;
            $stateId=null;
            if($cityData){
                $stateId=$cityData->state_id;
                $defaultQuery=true;
            }
        }else{
            $stateId=null;
            $defaultQuery=false;
        }

//echo"<pre>";print_r(count(PublicRepo::allJObLocations($stateId,$defaultQuery)));exit;
    	return response()->view('frontend.job-search' , [

			'searchMiles'		=> PublicRepo::allSearchMiles(),
			'salaryTypes'		=> PublicRepo::allSalaryTypes(),
			'searchDayAgos'		=> PublicRepo::allSearchDayAgos(),
			'recruiterTypes'	=> PublicRepo::allRecruiterTypes(),
			'jobTypes'			=> PublicRepo::allJobTypes(),
            'jobCategory'       => PublicRepo::allJobCategories(),
            'locations'         => PublicRepo::allJObLocations($stateId,$defaultQuery),
			'route_params'		=> $get_params,
            'Extcities'=>$Extcities,

			'results'			=> $jobs,
            'location_point' => $location_cordinates,
            'location_markers' => $markers,
            'jobCategoryName' =>$jobCategoryName,
            'jobTypeName' =>$jobTypeName,
            'recruiterTypeName' =>$recruiterTypeName,
            'salaryTypeName' =>$salaryTypeName,
            'daysAgo'=>$daysAgo,
            'radius_data'=>$radius_data,
            'companyName'=>$companyName


		])->withCookie($cookie);
        //return $next($request)->headers->setCookie($cookie);
    }

    public function getJobDetail(Request $request) {

        $validJob = false;
        $job = null;
        $get_params = $request->all();

        $get_params = [
            "keywords" => isset($request["keywords"]) ? $request["keywords"] : "", 
            "location" => isset($request["location"]) ? $request["location"] : "", 
            "radius" => isset($request["radius"]) ? $request["radius"] : 0, 
            "salaryType" => isset($request["salaryType"]) ? $request["salaryType"] : 0, 
            "salaryRate" => isset($request["salaryRate"]) ? $request["salaryRate"] : 0, 
            "daysAgo" => isset($request["daysAgo"]) ? $request["daysAgo"] : 0, 
            "recruiterType" => isset($request["recruiterType"]) ? $request["recruiterType"] : 0, 
            "jobType" => isset($request["jobType"]) ? $request["jobType"] : 0, 
            "jobCategory" =>isset($request["jobCategory"]) ? $request["jobCategory"] : 0, 
            "sortBy" => isset($request["sortBy"]) ? $request["sortBy"] : ""
        ];

        $user_point = [0,0];
        $job_point = [0,0];

        $previousJobId = 0;
        $nextJobId = 0;

        $validJob = false;
        if($request->has('jobId')) {
            list($jobs, $location_cordinates, $clearAddress) = PublicRepo::searchJobs($get_params["keywords"], $get_params["location"], $get_params["radius"], $get_params["salaryType"], $get_params["salaryRate"], $get_params["daysAgo"], $get_params["recruiterType"], $get_params["jobType"], $get_params["sortBy"], PublicRepo::countJobs(), []);
            
            if($jobs) {
                /// Old logic as per direct JobId Search...
                // foreach($jobs as $_job) {
                //     $validJob = true;
                //     $job = $_job;
                //     break;
                // }

                foreach($jobs as $_job) {
                    
                    if($_job->id == $request->jobId) {

                        $validJob = true;
                        $job = $_job;
                    } else {
                        if($validJob) {
                            $nextJobId = $_job->id;
                            break;
                        } else {
                            $previousJobId = $_job->id;
                        }
                    }

                }

            }
        }

        $nearByJobs = [];
        if($validJob && $job) { // find near-by jobs
            if($job){
                $address=$job->city_name.", ".$job->state_name.", ".$job->country_name;
                $location=($get_params["location"]!="") ? $get_params["location"] : $address;
            }else{
                $location=$get_params["location"];
            }

            list($jobs, $location_cordinates, $clearAddress) = PublicRepo::searchJobs($get_params["keywords"], $location, $get_params["radius"], $get_params["salaryType"], $get_params["salaryRate"], $get_params["daysAgo"], $get_params["recruiterType"], $get_params["jobType"], $get_params["sortBy"], 4);
            $nearByJobs = $jobs;
        }

        $jobApplication = null;

        if(MyAuth::check() && $validJob) {
            $jobApplication = $job->isApplied();//PublicRepo::findJobApplication(MyAuth::user() , $job);
            $job->setReaded();
        }

        $showDaySelection = false;
        if($validJob && $job->jobType) {
            $showDaySelection = $job->jobType->day_selection == 1 ? true : false;
        }

        return view('frontend.job-detail', [
            'job' => $job,
            'nearByJobs' => $nearByJobs,
            'valid_job' => $validJob,
            'route_params' => $get_params,
            'jobApplication' => $jobApplication,
            'showDaySelection' => $showDaySelection,
            'nextJobId' => $nextJobId,
            'previousJobId' => $previousJobId
        ]);
    }

}