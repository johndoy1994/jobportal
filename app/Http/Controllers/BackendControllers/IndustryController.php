<?php

namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\BackendControllers\BackendController;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\IndustryRequest;
use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class IndustryController extends BackendController
{
    public function getListing(Request $request)
    {
        if($request->has("search")) {
            $q =Industry::where('name', 'like', "%".$request["search"]."%");
        } else {
            $q =Industry::where('name', 'like', "%%");
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
        $recordsPerPage = $this->recordsPerPage("industry-listing");
        $Industrys = $q->paginate($recordsPerPage);
        
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
    	return view('backend.industry.listing', [
    		"Industrys" => $Industrys,
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
                        if(count($request["industrymultiple"]) > 0) {
                            foreach ($request["industrymultiple"] as $value) {
                                $val = Industry::find($value);
                                $val->delete();
                            }
                            //Industry::whereIn('id', $request["industrymultiple"])->delete();
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
                return redirect()->route('admin-industry', ['search'=>$request->filter])->withInput(Input::all());
            break;
        }
    }

    Public function getNewIndustry(Request $request){
    	return view('backend.industry.new-industry');
    }

    public function postNewIndustry(IndustryRequest $request) {

    	if(Industry::where('name', '=', $request->name)->first()) {
            return redirect()->back()->withInput(Input::all())->with(['error_message'=>"Already exists!!"]);
        } 
		$Industrys = new Industry();
		$Industrys->name = $request["name"];
		if($Industrys->save()) {
			return redirect()->back()->with([
				'success_message' => "New Industry successfully added!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while adding your Industry, try again"
			]);
		}

    }	

     // Edit JobType
    public function getEditIndustry(Request $request, Industry $Industry) {
    	return view('backend.industry.edit-industry',[
    		"Industrys"=>$Industry
		]);
    }

    public function postEditIndustry(IndustryRequest $request, Industry $Industry) {
		
        if(Industry::where('name', '=', $request->name)->where('id', '!=', $Industry->id)->first()) {
            return redirect()->back()->with(['error_message'=>"Already exists!!"]);
        }
		$Industry->name = $request["name"];
		if($Industry->update()) {
			return redirect()->route("admin-industry",['page'=>$request->page,'search'=>$request->search,'sortBy'=>$request->sortBy,'sortOrder'=>$request->sortOrder])->withInput(Input::all())->with([
				'success_message' => "Industry successfully saved!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while saving your Industry, try again"
			]);
		}
    }

    public function postDeleteIndustry(Request $request, Industry $Industry) {

    	if($Industry->delete()) {
			return redirect()->back()->withInput(Input::all())->with([
				'success_message' => "Industry successfully deleted!"
			]);
		} else {
			return redirect()->back()->withInput(Input::all())->with([
				'error_message' => "There was an error while deleting your Industry, try again!"
			]);
		}

    }
}
