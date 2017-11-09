<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\RecruiterTypeRequest;
use App\Models\RecruiterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class RecruiterTypeController extends BackendController
{
	//RecruiterType listing
    public function getListing(Request $request)
    {	
    	if($request->has("search")) {
            $q =RecruiterType::where('name', 'like', "%".$request["search"]."%");
        } else {
            $q =RecruiterType::where('name', 'like', "%%");
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
        $recordsPerPage = $this->recordsPerPage("recruiter-listing");
        $RecruiterType = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['name'];

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
    	return view('backend.recruitertype.listing', [
    		"RecruiterTypes" => $RecruiterType,
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
                        if(count($request["recruitertypemultiple"]) > 0) {
                            foreach ($request["recruitertypemultiple"] as $value) {
                                $val = RecruiterType::find($value);
                                $val->delete();
                            }
                            //RecruiterType::whereIn('id', $request["recruitertypemultiple"])->delete();
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
                return redirect()->route('admin-recruitertype', ['search'=>$request->filter])->withInput(Input::all());
            break;
        }
    }


    //add New RecruiterType
    Public function getNewRecruiterType(Request $request){
    	return view('backend.recruitertype.new-recruitertype');
    }

    public function postNewRecruiterType(RecruiterTypeRequest $request) {

        if(RecruiterType::where('name', '=', $request->name)->first()) {
            return redirect()->back()->withInput(Input::all())->with(['error_message'=>"Already exists!!"]);
        }    

		$RecruiterTypes = new RecruiterType();
		$RecruiterTypes->name = $request["name"];
		if($RecruiterTypes->save()) {
			return redirect()->back()->with([
				'success_message' => "New recruiter type successfully added!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while adding your recruiter type, try again"
			]);
		}

    }

    // Edit RecruiterType
    public function getEditRecruiterType(Request $request, RecruiterType $RecruiterType) {
    	return view('backend.recruitertype.edit-recruitertype',[
    		"RecruiterTypes"=>$RecruiterType
		]);
    }	

     
    public function postEditRecruiterType(RecruiterTypeRequest $request, RecruiterType $RecruiterType) {
		
        if(RecruiterType::where('name', '=', $request->name)->where('id', '!=', $RecruiterType->id)->first()) {
            return redirect()->back()->with(['error_message'=>"Already exists!!"]);
        }
		$RecruiterType->name = $request["name"];
		if($RecruiterType->update()) {
			return redirect()->route("admin-recruitertype",['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->withInput(Input::all())->with([
				'success_message' => "Recruiter type successfully saved!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while saving your recruiter type, try again"
			]);
		}
    }

    //Delete RecruiterType
    public function postDeleteRecruiterType(Request $request, RecruiterType $RecruiterType) {

    	if($RecruiterType->delete()) {
			return redirect()->back()->withInput(Input::all())->with([
				'success_message' => "Recruiter type successfully deleted!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while deleting your recruiter type, try again!"
			]);
		}

    }
}
