<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\JobApplication;
use App\Repos\AdminRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class CandidateController extends BackendController
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
        $q->where('job_applications.status','accepted');
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
        $recordsPerPage = $this->recordsPerPage("candidate-listing");
        $Candidates = $q->paginate($recordsPerPage);
       
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['name','mobile_number','email_address','title'];

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
        $isRequestSearch=$request->has('search');
        $countStatus = AdminRepo::countCandidateStatus();
	       	return view('backend.candidate.listing', [
	    		"Candidates" => $Candidates,
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
                        if(count($request["candidatemultiple"]) > 0) {
                            foreach ($request["candidatemultiple"] as $value) {
                                $val = JobApplication::find($value);
                                $val->delete();
                            }
                            //JobApplication::whereIn('id', $request["candidatemultiple"])->delete();
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
                return redirect()->route('admin-candidate', ['search'=>$request->filter])->withInput(Input::all());
            break;
        }
    }
}
