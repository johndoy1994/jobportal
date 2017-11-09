<?php

namespace App\Http\Controllers\RecruiterControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RecruiterControllers\RecruiterController;
use App\Http\Requests;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\UserResume;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use Illuminate\Http\Request;
use Illuminate\Http\withInput;
use Illuminate\Support\Facades\Input;

class SearchCVController extends RecruiterController
{
    public function getIndex(Request $request) {
    	$jobCategories = PublicRepo::allJobCategories();
    	$jobTypes = PublicRepo::allJobTypes();
    	$salaryTypes = PublicRepo::allSalaryTypes();

    	$filters = [
    		'viewMode'=>'list',
    		'keywords'=>'',
    		'location'=>'',
    		'jobCategory'=>0,
    		'jobType'=>0,
    		'salaryType'=>0,
    		'salaryRate'=>0,
    		'salaryRateTo'=>0,
    		'sortBy'=>'date'
		];

		$filters["keywords"] = $request->has('keywords') ? $request->keywords : "";
		$filters["location"] = $request->has('location') ? $request->location : "";
		$filters["viewMode"] = $request->has('viewMode') ? $request->viewMode : "list";
		$filters["jobCategory"] = $request->has('jobCategory') ? $request->jobCategory : 0;
		$filters["jobType"] = $request->has('jobType') ? $request->jobType : 0;
		$filters["salaryType"] = $request->has('salaryType') ? $request->salaryType : 0;
		$filters["salaryRate"] = $request->has('salaryRate') ? $request->salaryRate : 0;
		$filters["salaryRateTo"] = $request->has('salaryRateTo') ? $request->salaryRateTo : 0;
		$filters["sortBy"] = $request->has('sortBy') ? $request->sortBy : 'date';


        list($cvs, $location_cords) = PublicRepo::searchCVs($filters);

        if($location_cords[0] == 0 && $location_cords[1] == 0) {
            $location_cords[2] = 2;
        } else {
            $location_cords[2] = 14;
        }

        $location_markers = [];

        if($filters["viewMode"] == "map") {
            $pointsAsPerCv = [];
            foreach($cvs as $cv) {
                $cities = $cv->getDesiredLocations_cities();
                foreach($cities as $city) {
                    $uniqPoint = $city->latitude.$city->longitude;
                    if(isset($pointsAsPerCv[$uniqPoint.$cv->id])) {
                        continue;
                    }
                    $pointsAsPerCv[$uniqPoint.$cv->id] = 0;
                    $point = [$city->latitude, $city->longitude];
                    if(!isset($location_markers[$uniqPoint])) {
                        $location_markers[$uniqPoint] = [];
                    }
                    $location_markers[$uniqPoint][] = [$cv->id, $cv->getName(), $point];
                }
            }            
        }
//echo'<pre>';print_r($filters);exit;
    	return view('recruiter.search-cv.index',[
    		'jobCategories'=>$jobCategories,
    		'jobTypes' => $jobTypes,
    		'salaryTypes'=>$salaryTypes, 

            'cvs' => $cvs,

    		'filters'=>$filters, 

            'location_point'=>$location_cords,
            'location_markers'=>$location_markers
		]);
    }

    public function getCvDetail(Request $request) {
        $cvId = $request->id;

        $jobCategories = PublicRepo::allJobCategories();
        $jobTypes = PublicRepo::allJobTypes();
        $salaryTypes = PublicRepo::allSalaryTypes();

        $filters = [
            'viewMode'=>'list',
            'keywords'=>'',
            'location'=>'',
            'jobCategory'=>0,
            'jobType'=>0,
            'salaryType'=>0,
            'salaryRate'=>0,
            'salaryRateTo'=>0,
            'sortBy'=>'date'
        ];

        $filters["keywords"] = $request->has('keywords') ? $request->keywords : "";
        $filters["location"] = $request->has('location') ? $request->location : "";
        $filters["viewMode"] = $request->has('viewMode') ? $request->viewMode : "list";
        $filters["jobCategory"] = $request->has('jobCategory') ? $request->jobCategory : 0;
        $filters["jobType"] = $request->has('jobType') ? $request->jobType : 0;
        $filters["salaryType"] = $request->has('salaryType') ? $request->salaryType : 0;
        $filters["salaryRate"] = $request->has('salaryRate') ? $request->salaryRate : 0;
        $filters["salaryRateTo"] = $request->has('salaryRateTo') ? $request->salaryRateTo : 0;
        $filters["sortBy"] = $request->has('sortBy') ? $request->sortBy : 'date';

        list($cvs, $location_cords) = PublicRepo::searchCVs($filters,0);

        $validCv = null;
        $prevCv = null;
        $nextCv = null;

        foreach($cvs as $cv) {
            if($cv->id == $cvId) {
                $validCv = $cv;
            } else {
                if(isset($validCv)) {
                    $nextCv = $cv;
                    break;
                } else {
                    $prevCv = $cv;
                }
            }
        }

        return view('recruiter.search-cv.cv-detail',[
            'cvs' => $cvs,
            'filters'=>$filters,
            'cv' => $validCv, 
            'nextCv' => $nextCv,
            'prevCv' => $prevCv
        ]);
    }

    public function getResumeDownload(Request $request,User $id){
        $resume = UserResume::where('user_id', $id->id)->first();
        $user=MyAuth::user('recruiter');
        $jobapplicationData=PublicRepo::getJobApplicationRecord($user,$id);

        if($jobapplicationData){
            if($resume){
                $file = storage_path().'/app/resumes/'.$resume->filename;
                return response()->download($file, $id->getName()."_".$resume->filename, ['content-type' => $resume->mime]);        
            }else{
                return redirect()->back()->withInput(Input::all())->with([
                    'error_message' => 'User CV is not available.'
                ]);
            }
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                    'error_message' => 'You are not authorize to view this applicant resume.'
                ]);
        }
    }
}
