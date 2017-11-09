<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Degree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class DegreeController extends BackendController
{
    //Degree listing
    public function getListing(Request $request)
    {	
    	if($request->has("search")) {
            $q =Degree::where('name', 'like', "%".$request["search"]."%");
        } else {
            $q =Degree::where('name', 'like', "%%");
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
        $recordsPerPage = $this->recordsPerPage("degree-listing");
        $Degree = $q->paginate($recordsPerPage);
        
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
    	return view('backend.degree.listing', [
    		"Degrees" => $Degree,
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
                        if(count($request["degreemultiple"]) > 0) {
                            foreach ($request["degreemultiple"] as $value) {
                                $val = Degree::find($value);
                                $val->delete();
                            }
                            //Degree::whereIn('id', $request["degreemultiple"])->delete();
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
                return redirect()->route('admin-degree', ['search'=>$request->filter])->withInput(Input::all());
            break;
        }
    }


    //add New Degree
    Public function getNewDegree(Request $request){
    	return view('backend.degree.new-degree');
    }

    public function postNewDegree(Request $request) {

    	$this->validate($request, [
    		'name' => 'required',//|unique:degrees
    	],[
    		'name.required'=>'Degree name must not be empty.'
    	]);
        if(Degree::where('name', '=', $request->name)->first()) {
            return redirect()->back()->withInput(Input::all())->with(['error_message'=>"Already exists!!"]);
        } 
		$Degrees = new Degree();
		$Degrees->name = $request["name"];
		if($Degrees->save()) {
			return redirect()->back()->with([
				'success_message' => "New degree successfully added!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while adding your degree, try again"
			]);
		}

    }

    // Edit Degree
    public function getEditDegree(Request $request, Degree $Degree) {
    	return view('backend.degree.edit-degree',[
    		"Degrees"=>$Degree
		]);
    }	

     
    public function postEditDegree(Request $request, Degree $Degree) {
		$this->validate($request, [
    		'name' => 'required',//|unique:degrees,name,'.$Degree->id
    	],[
    		'name.required'=>'Degree name must not be empty.'
    	]);
        if(Degree::where('name', '=', $request->name)->where('id', '!=', $Degree->id)->first()) {
            return redirect()->back()->with(['error_message'=>"Already exists!!"]);
        }
		$Degree->name = $request["name"];
		if($Degree->update()) {
			return redirect()->route("admin-degree",['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->withInput(Input::all())->with([
				'success_message' => "degree successfully saved!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while saving your Degree, try again"
			]);
		}
    }

    //Delete Degree
    public function postDeleteDegree(Request $request, Degree $Degree) {

    	if($Degree->delete()) {
			return redirect()->back()->withInput(Input::all())->with([
				'success_message' => "Degree successfully deleted!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while deleting your Degree, try again!"
			]);
		}

    }
}
