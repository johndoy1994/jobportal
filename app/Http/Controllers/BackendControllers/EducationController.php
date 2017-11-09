<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class EducationController extends BackendController
{
    public function getListing(Request $request)
    {
        if($request->has("search")) {
            $q =Education::where('name', 'like', "%".$request["search"]."%");
        } else {
            $q =Education::where('name', 'like', "%%");
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
        $recordsPerPage = $this->recordsPerPage("education-listing");
        $Educations = $q->paginate($recordsPerPage);
        
        $page = $request->has('page') ? $request->page : 1;

        $columns = ['name','order'];

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
    	return view('backend.education.listing', [
    		"Educations" => $Educations,
            'sort_columns' => $sort_columns,
            'isRequestSearch'=>$isRequestSearch
		]);
    }

    public function postMoveOrder(Request $request, Education $education, $action) {

        if(!in_array($action, ['up','down'])) {
            return redirect()->back()->with(['error_message'=>"Unknown action, try again"]);
        }

        $current_order  = $education->order;

        if(($current_order == Education::getFirstOrder() && $action == "up") || ($current_order == Education::getLastOrder() && $action=="down")) {
            return redirect()->back()->with(['error_message'=>"Invalid action, try again"]);
        }
        
        switch($action) {
            case "up":
                $new_order = Education::getLastOrder($current_order);
                $_education = Education::where('order', $new_order)->first();
                if($_education) {
                    $_education->order = $current_order;
                    $_education->update();
                }
                $education->order = $new_order;
                $education->update();
                return redirect()->back()->with(['success_message' => "Moved"]);
            break;
            case "down":
                $new_order = Education::getFirstOrder($current_order);
                $_education = Education::where('order', $new_order)->first();
                if($_education) {
                    $_education->order = $current_order;
                    $_education->update();
                }
                $education->order = $new_order;
                $education->update();
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
                        if(count($request["educationmultiple"]) > 0) {
                            foreach ($request["educationmultiple"] as $value) {
                                $val = Education::find($value);
                                $val->delete();
                            }
                            //Education::whereIn('id', $request["educationmultiple"])->delete();
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
                return redirect()->route('admin-education', ['search'=>$request->filter])->withInput(Input::all());
            break;
        }
    }

    Public function getNewEducation(Request $request){
    	return view('backend.education.new-education');
    }

    public function postNewEducation(Request $request) {

    	$this->validate($request, [
    		'name' => 'required', //|unique:education
    	],[
    		'name.required'=>'Education name must not be empty.'
    	]);

        if(Education::where('name', '=', $request->name)->first()) {
            return redirect()->back()->withInput(Input::all())->with(['error_message'=>"Already exists!!"]);
        }

		$Educations = new Education();
        $Educations->order = Education::getLastOrder() + 1;
		$Educations->name = $request["name"];
		if($Educations->save()) {
			return redirect()->back()->with([
				'success_message' => "New Education successfully added!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while adding your Education, try again"
			]);
		}

    }	

     // Edit JobType
    public function getEditEducation(Request $request, Education $Education) {
    	return view('backend.education.edit-education',[
    		"Educations"=>$Education
		]);
    }

    public function postEditEducation(Request $request, Education $Education) {
		$this->validate($request, [
    		'name' => 'required',//|unique:education,name,'.$Education->id
    	],[
    		'name.required'=>'Education must not be empty.'
    	]);
        if(Education::where('name', '=', $request->name)->where('id', '!=', $Education->id)->first()) {
            return redirect()->back()->with(['error_message'=>"Already exists!!"]);
        }
		$Education->name = $request["name"];
		if($Education->update()) {
			return redirect()->route("admin-education",['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->withInput(Input::all())->with([
				'success_message' => "Education successfully saved!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while saving your Education, try again"
			]);
		}
    }

    public function postDeleteEducation(Request $request, Education $Education) {

    	if($Education->delete()) {
			return redirect()->back()->withInput(Input::all())->with([
				'success_message' => "Education successfully deleted!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while deleting your Education, try again!"
			]);
		}

    }
}
