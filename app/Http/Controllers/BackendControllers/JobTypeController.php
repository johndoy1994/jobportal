<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\JobType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class JobTypeController extends BackendController
{
    public function getListing(Request $request)
    {
        if($request->has("search")) {
            $q =JobType::where('name', 'like', "%".$request["search"]."%");
        } else {
            $q =JobType::where('name', 'like', "%%");
        }
    	
        if($request->has('sortBy')) {
            $sortOrder = "asc";
            if($request->has('sortOrder')) {
                $sortOrder=$request["sortOrder"];
            }
            $q->orderBy($request['sortBy'], $sortOrder);
        } else {
            $q->orderBy('order');
        }
        
        $recordsPerPage = $this->recordsPerPage("job-type-listing");

        $JobTypes = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['name','day_selection','order'];

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
        $isRequestSearch=$request->has('search');
    	return view('backend.jobtype.listing', [
    		"JobTypes" => $JobTypes,
            'sort_columns' => $sort_columns,
            'isRequestSearch'=>$isRequestSearch
		]);
    }

    public function postMoveOrder(Request $request, JobType $JobType, $action) {

        if(!in_array($action, ['up','down'])) {
            return redirect()->back()->with(['error_message'=>"Unknown action, try again"]);
        }

        $current_order  = $JobType->order;

        if(($current_order == JobType::getFirstOrder() && $action == "up") || ($current_order == JobType::getLastOrder() && $action=="down")) {
            return redirect()->back()->with(['error_message'=>"Invalid action, try again"]);
        }
        
        switch($action) {
            case "up":
                $new_order = JobType::getLastOrder($current_order);//$current_order - 1;
                $_JobType = JobType::where('order', $new_order)->first();
                if($_JobType) {
                    $_JobType->order = $current_order;
                    $_JobType->update();
                }
                $JobType->order = $new_order;
                $JobType->update();
                return redirect()->back()->with(['success_message' => "Moved"]);
            break;
            case "down":
                $new_order = JobType::getFirstOrder($current_order);
                $_JobType = JobType::where('order', $new_order)->first();
                if($_JobType) {
                    $_JobType->order = $current_order;
                    $_JobType->update();
                }
                $JobType->order = $new_order;
                $JobType->update();
                return redirect()->back()->with(['success_message' => "Moved"]);
            break;
        }
        
        return redirect()->back();

    }
        
    public function postListing(Request $request)
    {
        $action = $request->submit;

        switch($action) {
            case "Apply":
                switch($request["bulkid"]) {
                    case "deleted":
                        if(count($request["jobtypemultiple"]) > 0) {
                            foreach ($request["jobtypemultiple"] as $value) {
                                $val = JobType::find($value);
                                $val->delete();
                            }
                            //JobType::whereIn('id', $request["jobtypemultiple"])->delete();
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
                return redirect()->route('admin-jobtype', ['search'=>$request->filter])->withInput(Input::all());
            break;
        }
    }

    Public function getNewJobType(Request $request){
    	return view('backend.jobtype.new-jobtype');
    }

    public function postNewJobType(Request $request) {

    	$this->validate($request, [
    		'name' => 'required',//|unique:job_types
    	],[
    		'name.required'=>'JobType must not be empty.'
    	]);
        if(JobType::where('name', '=', $request->name)->first()) {
            return redirect()->back()->withInput(Input::all())->with(['error_message'=>"Already exists!!"]);
        }
        if($request["day_selection"]){
            $day_selection=1;
        }else{
            $day_selection=0;
        }
		$JobTypes = new JobType();
        $JobTypes->order = JobType::getLastOrder() + 1;
		$JobTypes->name = $request["name"];
        $JobTypes->day_selection = $day_selection;
		if($JobTypes->save()) {
			return redirect()->back()->with([
				'success_message' => "New jobtype successfully added!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while adding your jobtype, try again"
			]);
		}

    }	

     // Edit JobType
    public function getEditJobType(Request $request, JobType $JobType) {
    	return view('backend.jobtype.edit-jobtype',[
    		"JobTypes"=>$JobType
		]);
    }

    public function postEditJobType(Request $request, JobType $JobType) {
		$this->validate($request, [
    		'name' => 'required',//|unique:job_types,name,'.$JobType->id
    	],[
    		'name.required'=>'JobType must not be empty.'
    	]);
        if(JobType::where('name', '=', $request->name)->where('id', '!=', $JobType->id)->first()) {
            return redirect()->back()->with(['error_message'=>"Already exists!!"]);
        }
        
        if($request["day_selection"]){
            $day_selection=1;
        }else{
            $day_selection=0;
        }
        
		$JobType->name = $request["name"];
        $JobType->day_selection = $day_selection;
		if($JobType->update()) {
			return redirect()->route("admin-jobtype",['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->withInput(Input::all())->with([
				'success_message' => "JobType successfully saved!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while saving your JobType, try again"
			]);
		}
    }

    public function postDeleteJobType(Request $request, JobType $JobType) {

    	if($JobType->delete()) {
			return redirect()->back()->withInput(Input::all())->with([
				'success_message' => "JobType successfully deleted!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while deleting your JobType, try again!"
			]);
		}

    }
}
