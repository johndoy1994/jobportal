<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\UserResume;
use App\Repos\API\PublicRepo;
use App\Repos\AdminRepo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class ApplicationController extends BackendController
{
    public function getListing(Request $request)
    {
    	$currentDate = \Carbon\Carbon::now()->format("Y-m-d");
    	$q = JobApplication::select(
	            "job_applications.*",
	            DB::raw('users.name'),
	            DB::raw('users.mobile_number'),
	            DB::raw('users.email_address'),
	            DB::raw('jobs.title'),
	            DB::raw('datediff(job_applications.created_at, "'.$currentDate.'") as days'),
                DB::raw('user_resumes.filename')

	        );
        
        $q->join('users', 'users.id', '=', 'job_applications.user_id');
        $q->join('jobs', 'jobs.id', '=', 'job_applications.job_id');
        $q->leftJoin("user_resumes", "user_resumes.user_id", '=', 'job_applications.user_id');
    	
    	if($request->has("search")) {   
        	$search=$request["search"];
            $q->where(function($q) use($search) {
                $q->orWhere('users.name','like','%'.$search.'%');
                $q->orWhere('users.mobile_number','like','%'.$search.'%');
                $q->orWhere('users.email_address','like','%'.$search.'%');
                $q->orWhere('jobs.title','like','%'.$search.'%');
                $q->orWhere('job_applications.status','like','%'.$search.'%');
                
            });
        }
        if($request->has("status")) {   
            $search=$request["status"];
            if($search!="guest"){
                $q->where(function($q) use($search) {
                    $q->orWhere('job_applications.status','like','%'.$search.'%');
                });
            }else{
                $search=1;
                $q->where(function($q) use($search) {
                    $q->orWhere('job_applications.is_guest','like','%'.$search.'%');
                });
            }
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
                $q->whereBetween('job_applications.created_at', [$fromDate, $toDate]);
            });
        }

    	if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            //$q->orderBy('name');
            $q->orderBy('created_at','desc');
        }
        $recordsPerPage = $this->recordsPerPage("application-listing");
        $Applications = $q->paginate($recordsPerPage);
       
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['name','mobile_number','email_address','title','status'];

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
        //count status
        $countStatus = AdminRepo::countApplicationStatus();
        
        $isRequestSearch=$request->has('search');
    	return view('backend.application.listing', [
    		"Applications" => $Applications,
            'sort_columns' => $sort_columns,
            'countStatus'=>$countStatus,
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
                        if(count($request["applicationmultiple"]) > 0) {
                            foreach ($request["applicationmultiple"] as $value) {
                                $val = JobApplication::find($value);
                                $val->delete();
                            }
                            //JobApplication::whereIn('id', $request["applicationmultiple"])->delete();
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
                return redirect()->route('admin-application', ['search'=>$request->filter])->withInput(Input::all());
            break;

            case "SearchDateVish":
                return redirect()->route('admin-application', ['date'=>$request->date])->withInput(Input::all());
            break;
        }
    }

    public function getShowJobApplication(Request $request, JobApplication $JobApplication) {
        $jobApp = PublicRepo::getJobApplication($JobApplication->id);
        
        if($request->has('type') && (($request->type==1) || ($request->type==2))){
            $type=($request->type==1)? "admin-application" : "admin-candidate";
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'Url not vallid ,please try again.'
            ]);
        }
        return view('backend.application.job-application-detail', [
            "app" => $jobApp,
            "type"=>$type
        ]);
    }

    public function postJobApplicationStatusChange(Request $request){
        list($status,$message) = [false , "Invalid request"];
        if($request->has('id') && $request->has('status')){
            if(in_array($request->status, ['in-process','accepted','rejected','cancelled'])) {
                list($status,$message) = AdminRepo::ApplicationStatusUpdate($request->id,$request->status);
            } else {
                list($status,$message) = [false , "some thing want to wrong please try again."];
            }
        }else{
            list($status,$message) = [false , "some thing want to wrong please try again"];
        }

        return response()->json($status);   
    }

    public function getResumeDownload(Request $request,User $id){
        $resume = UserResume::where('user_id', $id->id)->first();
        if($resume){
            $file = storage_path().'/app/resumes/'.$resume->filename;
            return response()->download($file, $id->getName()."_".$resume->filename, ['content-type' => $resume->mime]);        
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'User CV is not available.'
            ]);
        }
    }
}
